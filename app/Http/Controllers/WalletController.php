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
        $user = Auth::user(); 

        $histories = WalletHistory::where('user_id', $user->id)
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return view('rider.wallet.index', compact('user', 'histories'));
    }

    public function topUp(Request $request)
    {

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

        DB::beginTransaction();
        try {
            $user->balance += $amount;
            $user->save();

            WalletHistory::create([
                'user_id' => $user->id,
                'reference_id' => null,
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