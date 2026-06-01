<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WalletHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        // Menggunakan auth bawaan, sesuaikan jika timmu menggunakan logic custom auth
        $user = Auth::user(); 
        
        // Ambil riwayat dompet terbaru
        $histories = WalletHistory::where('user_id', $user->id)
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return view('rider.wallet.index', compact('user', 'histories'));
    }

    public function topUp(Request $request)
    {
        // 1. Validasi input nominal top up
        $request->validate([
            'amount' => 'required|numeric|min:10000|max:10000000',
        ], [
            'amount.required' => 'Nominal top-up wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'amount.min' => 'Minimal top-up adalah Rp10.000.',
            'amount.max' => 'Maksimal top-up simulasi adalah Rp10.000.000.',
        ]);

        $user = Auth::user();
        $amount = $request->amount;

        // 2. Gunakan DB Transaction agar proses update saldo & pencatatan riwayat sinkron
        DB::beginTransaction();
        try {
            // Update saldo user
            $user->balance += $amount;
            $user->save();

            // Catat log ke tabel wallet_histories
            WalletHistory::create([
                'user_id' => $user->id,
                'reference_id' => null, // null karena ini top-up murni, bukan pembayaran
                'type' => 'topup',
                'amount' => $amount,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Simulasi Top-Up berhasil! Saldo EV-Pay Anda telah bertambah.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memproses Top-Up. Silakan coba kembali.');
        }
    }
}