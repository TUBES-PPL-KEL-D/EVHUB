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
        $transaction = Transaction::with(['chargerMachine.spklu', 'vehicle'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('rider.transactions.show', compact('transaction'));
    }

    public function prepareCharging($machine_id)
    {
        $machine = ChargerMachine::findOrFail($machine_id);
        
        // Ambil semua kendaraan milik user yang sedang login
        $vehicles = Vehicle::where('user_id', Auth::id())->get();

        return view('rider.transactions.prepare', compact('machine', 'vehicles'));
    }

    // public function startCharging(Request $request)
    // {
    //     $request->validate([
    //         'charger_machine_id' => 'required|exists:charger_machines,id',
    //     ]);

    //     // Cari mesin charger berdasarkan ID yang dikirim form
    //     $machine = ChargerMachine::findOrFail($request->charger_machine_id);

    //     // Proteksi ganda di sisi server jika ada user lain yang menembak link secara bersamaan
    //     if (strtolower($machine->status) !== 'available') {
    //         return redirect()->back()->with('error', 'Maaf, mesin ini baru saja digunakan atau sedang dalam perbaikan.');
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // 1. Buat record baru di tabel `transactions` dengan status 'pending'
    //         $transaction = Transaction::create([
    //             'user_id' => Auth::id(),
    //             'charger_machine_id' => $machine->id,
    //             'status' => 'pending',
    //             'energy_consumed' => 0, 
    //             'total_price' => 0,
    //             'started_at' => now(),
    //             'finished_at' => null,
    //         ]);

    //         // 2. Ubah status mesin di database menjadi 'unavailable' (Dipakai) sesuai permintaanmu
    //         $machine->update([
    //             'status' => 'unavailable'
    //         ]);

    //         DB::commit();

    //         // 3. Alihkan user langsung ke halaman daftar riwayat biar dia bisa melihat progresnya
    //         return redirect()->route('rider.transactions.index')
    //                         ->with('success', 'Pengisian daya dimulai! Silakan klik tombol "Selesai & Potong Saldo" jika baterai sudah penuh.');

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return redirect()->back()->with('error', 'Gagal memulai pengisian daya. Terjadi kesalahan pada sistem.');
    //     }
    // }

    // // Simulasi Berhenti Nge-charge & Potong Saldo Otomatis (PBI 32)
    // public function stopCharging(Request $request, $id)
    // {
    //     $transaction = Transaction::with('chargerMachine')->findOrFail($id);
    //     $user = Auth::user();

    //     // Validasi status memastikan transaksi masih berjalan (pending)
    //     if ($transaction->status !== 'pending') {
    //         return redirect()->back()->with('error', 'Transaksi ini sudah selesai atau gagal.');
    //     }

    //     // 1. Simulasi data pengisian daya (karena ini apps sandbox kuliah)
    //     // Anggap user men-charge acak antara 15 hingga 45 kWh
    //     $energyConsumed = rand(1500, 4500) / 100; 
    //     $pricePerKwh = $transaction->chargerMachine->price_per_kwh ?? 2500; // fallback jika harga kosong
    //     $totalPrice = $energyConsumed * $pricePerKwh;

    //     // 2. Cek apakah saldo EV-Pay mencukupi
    //     if ($user->balance < $totalPrice) {
    //         DB::beginTransaction();
    //         try {
    //             $transaction->update(['status' => 'failed']);
    //             DB::commit();
    //             $transaction->chargerMachine->update(['status' => 'available']);
    //             return redirect()->back()->with('error', 'Transaksi dihentikan otomatis. Saldo EV-Pay tidak mencukupi untuk membayar pengisian daya!');
    //         } catch (\Exception $e) {
    //             DB::rollback();
    //             return redirect()->back()->with('error', 'Gagal memproses pembatalan.');
    //         }
    //     }

    //     // 3. Proses Finansial Menggunakan DB Transaction (ACID Terjaga)
    //     DB::beginTransaction();
    //     try {
    //         // A. Update data transaksi utama
    //         $transaction->update([
    //             'energy_consumed' => $energyConsumed,
    //             'total_price' => $totalPrice,
    //             'finished_at' => now(),
    //             'status' => 'success'
    //         ]);

    //         // B. Potong otomatis saldo user aktif
    //         $user->balance -= $totalPrice;
    //         $user->save();

    //         // C. Catat mutasi keluar ke tabel wallet_histories
    //         WalletHistory::create([
    //             'user_id' => $user->id,
    //             'reference_id' => $transaction->id, // Menyimpan ID transaksi sesuai ERD kamu
    //             'type' => 'payment',
    //             'amount' => $totalPrice,
    //         ]);

    //         $transaction->chargerMachine->update(['status' => 'available']);
            
    //         DB::commit();
    //         return redirect()->route('rider.transactions.show', $transaction->id)
    //                          ->with('success', 'Pengisian selesai! Saldo EV-Pay Anda berhasil dipotong otomatis.');

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return redirect()->back()->with('error', 'Sistem gagal memproses pembayaran otomatis. Silakan coba lagi.');
    //     }
    // }
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
            // Buat record baru dengan menyimpan data inputan ke energy_consumed & total_price untuk sementara/final
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'charger_machine_id' => $machine->id,
                'vehicle_id' => $vehicle->id, // Kolom baru tersimpan
                'status' => 'pending',
                'energy_consumed' => $request->energy_target, // Menyimpan daya pesanan user
                'total_price' => $estimatedPrice,             // Menyimpan total harga pesanan user
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

    // 3. Fungsi menghentikan daya (Sudah tidak pakai rand() acak lagi)
    public function stopCharging(Request $request, $id)
    {
        $transaction = Transaction::with('chargerMachine')->findOrFail($id);
        $user = Auth::user();

        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi ini sudah selesai.');
        }

        // Nilai diambil langsung dari data yang diinput saat 'start' tadi
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