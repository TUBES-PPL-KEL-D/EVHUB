<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\WalletHistory;
use App\Models\ChargerMachine;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil transaksi yang terhubung dengan charger_machines
        $transactions = Transaction::with('chargerMachine')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('rider.transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = Transaction::with(['chargerMachine.spklu', 'vehicle'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('rider.transactions.show', compact('transaction'));
    }

    public function prepareCharging($machine_id)
    {
        $machine = ChargerMachine::findOrFail($machine_id);
        
        $vehicles = Vehicle::where('user_id', Auth::id())->get();

        return view('rider.transactions.prepare', compact('machine', 'vehicles'));
    }

    public function startCharging(Request $request)
    {
        $request->validate([
            'charger_machine_id' => 'required|exists:charger_machines,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'energy_target' => 'required|numeric|min:1',
        ]);

        $machine = ChargerMachine::findOrFail($request->charger_machine_id);
        $vehicle = Vehicle::where('user_id', Auth::id())->findOrFail($request->vehicle_id);
        $user = Auth::user();

        // VALIDASI 1: Cek kesesuaian tipe konektor
        if (strtolower($machine->connector_type) !== strtolower($vehicle->connector_type)) {
            return redirect()->back()->with('error', 'Tipe konektor mesin (' . $machine->connector_type . ') tidak cocok dengan kendaraan Anda (' . $vehicle->connector_type . ').');
        }

        // Hitung estimasi harga berdasarkan target kWh inputan user
        $pricePerKwh = $machine->price_per_kwh ?? 2500;
        $estimatedPrice = $request->energy_target * $pricePerKwh;

        // Hitung estimasi durasi berdasarkan daya mesin
        $estimatedDurationHours = $request->energy_target / $machine->capacity_kw;
        $estimatedDurationMinutes = ceil($estimatedDurationHours * 60);

        // VALIDASI 2: Cek kecukupan saldo awal sebelum charge dimulai
        if ($user->balance < $estimatedPrice) {
            return redirect()->back()->with('error', 'Saldo EV-Pay Anda tidak mencukupi. Estimasi biaya: Rp ' . number_format($estimatedPrice, 0, ',', '.') . ' (Saldo Anda: Rp ' . number_format($user->balance, 0, ',', '.') . ').');
        }

        // Proteksi ganda status mesin
        if (strtolower($machine->status) !== 'available') {
            return redirect()->back()->with('error', 'Maaf, mesin ini sedang digunakan oleh pengguna lain.');
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'charger_machine_id' => $machine->id,
                'vehicle_id' => $vehicle->id,
                'status' => 'pending',
                'energy_consumed' => $request->energy_target,
                'total_price' => $estimatedPrice, 
                'started_at' => now(),
                'finished_at' => null,
            ]);

            $machine->update(['status' => 'unavailable']);

            DB::commit();

            return redirect()->route('rider.transactions.index')
                            ->with('success', 'Pengisian daya dimulai! Silakan klik tombol "Selesai & Potong Saldo" jika sudah selesai.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memulai pengisian daya. Hubungi admin.');
        }
    }

    public function stopCharging(Request $request, $id)
    {
        $transaction = Transaction::with('chargerMachine')->findOrFail($id);
        $user = Auth::user();

        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi ini sudah selesai.');
        }

        $totalPrice = $transaction->total_price;

        if ($user->balance < $totalPrice) {
            DB::beginTransaction();
            try {
                $transaction->update(['status' => 'failed']);
                $transaction->chargerMachine->update(['status' => 'available']);
                DB::commit();
                return redirect()->back()->with('error', 'Transaksi gagal. Saldo mendadak tidak mencukupi!');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Gagal memproses pembatalan.');
            }
        }

        DB::beginTransaction();
        try {
            $transaction->update([
                'finished_at' => now(),
                'status' => 'success'
            ]);

            $user->balance -= $totalPrice;
            $user->save();

            WalletHistory::create([
                'user_id' => $user->id,
                'reference_id' => $transaction->id,
                'type' => 'payment',
                'amount' => $totalPrice,
            ]);

            $transaction->chargerMachine->update(['status' => 'available']);

            DB::commit();
            return redirect()->route('rider.transactions.show', $transaction->id)
                            ->with('success', 'Pengisian selesai! Saldo EV-Pay berhasil dipotong.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Sistem gagal memproses pembayaran.');
        }
    }
}