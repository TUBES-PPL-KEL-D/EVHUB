<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\WalletHistory;
use App\Models\ChargerMachine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // Menampilkan Semua Riwayat Transaksi Pengendara (PBI 33)
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

    // Menampilkan Detail Spesifik Satu Transaksi (PBI 33)
    public function show($id)
    {
        $transaction = Transaction::with('chargerMachine.spklu')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('rider.transactions.show', compact('transaction'));
    }

    public function startCharging(Request $request)
    {
        $request->validate([
            'charger_machine_id' => 'required|exists:charger_machines,id',
        ]);

        // Cari mesin charger berdasarkan ID yang dikirim form
        $machine = ChargerMachine::findOrFail($request->charger_machine_id);

        // Proteksi ganda di sisi server jika ada user lain yang menembak link secara bersamaan
        if (strtolower($machine->status) !== 'available') {
            return redirect()->back()->with('error', 'Maaf, mesin ini baru saja digunakan atau sedang dalam perbaikan.');
        }

        DB::beginTransaction();
        try {
            // 1. Buat record baru di tabel `transactions` dengan status 'pending'
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'charger_machine_id' => $machine->id,
                'status' => 'pending',
                'energy_consumed' => 0, 
                'total_price' => 0,
                'started_at' => now(),
                'finished_at' => null,
            ]);

            // 2. Ubah status mesin di database menjadi 'unavailable' (Dipakai) sesuai permintaanmu
            $machine->update([
                'status' => 'unavailable'
            ]);

            DB::commit();

            // 3. Alihkan user langsung ke halaman daftar riwayat biar dia bisa melihat progresnya
            return redirect()->route('rider.transactions.index')
                            ->with('success', 'Pengisian daya dimulai! Silakan klik tombol "Selesai & Potong Saldo" jika baterai sudah penuh.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memulai pengisian daya. Terjadi kesalahan pada sistem.');
        }
    }

    // Simulasi Berhenti Nge-charge & Potong Saldo Otomatis (PBI 32)
    public function stopCharging(Request $request, $id)
    {
        $transaction = Transaction::with('chargerMachine')->findOrFail($id);
        $user = Auth::user();

        // Validasi status memastikan transaksi masih berjalan (pending)
        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi ini sudah selesai atau gagal.');
        }

        // 1. Simulasi data pengisian daya (karena ini apps sandbox kuliah)
        // Anggap user men-charge acak antara 15 hingga 45 kWh
        $energyConsumed = rand(1500, 4500) / 100; 
        $pricePerKwh = $transaction->chargerMachine->price_per_kwh ?? 2500; // fallback jika harga kosong
        $totalPrice = $energyConsumed * $pricePerKwh;

        // 2. Cek apakah saldo EV-Pay mencukupi
        if ($user->balance < $totalPrice) {
            DB::beginTransaction();
            try {
                $transaction->update(['status' => 'failed']);
                DB::commit();
                $transaction->chargerMachine->update(['status' => 'available']);
                return redirect()->back()->with('error', 'Transaksi dihentikan otomatis. Saldo EV-Pay tidak mencukupi untuk membayar pengisian daya!');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Gagal memproses pembatalan.');
            }
        }

        // 3. Proses Finansial Menggunakan DB Transaction (ACID Terjaga)
        DB::beginTransaction();
        try {
            // A. Update data transaksi utama
            $transaction->update([
                'energy_consumed' => $energyConsumed,
                'total_price' => $totalPrice,
                'finished_at' => now(),
                'status' => 'success'
            ]);

            // B. Potong otomatis saldo user aktif
            $user->balance -= $totalPrice;
            $user->save();

            // C. Catat mutasi keluar ke tabel wallet_histories
            WalletHistory::create([
                'user_id' => $user->id,
                'reference_id' => $transaction->id, // Menyimpan ID transaksi sesuai ERD kamu
                'type' => 'payment',
                'amount' => $totalPrice,
            ]);

            $transaction->chargerMachine->update(['status' => 'available']);
            
            DB::commit();
            return redirect()->route('rider.transactions.show', $transaction->id)
                             ->with('success', 'Pengisian selesai! Saldo EV-Pay Anda berhasil dipotong otomatis.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Sistem gagal memproses pembayaran otomatis. Silakan coba lagi.');
        }
    }
}