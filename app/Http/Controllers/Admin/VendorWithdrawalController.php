<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorWithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = VendorWithdrawal::with(['vendor.user', 'processedBy'])
            ->latest()
            ->get();

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function approve(VendorWithdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return redirect()->route('admin.withdrawals.index')->with('error', 'Pengajuan ini sudah diproses.');
        }

        $withdrawal->update([
            'status' => 'approved',
            'processed_at' => now(),
            'admin_notes' => 'Disetujui admin melalui panel sistem.',
        ]);

        return redirect()->route('admin.withdrawals.index')->with('success', 'Pengajuan withdrawal berhasil disetujui.');
    }

    public function reject(Request $request, VendorWithdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return redirect()->route('admin.withdrawals.index')->with('error', 'Pengajuan ini sudah diproses.');
        }

        $withdrawal->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'admin_notes' => $request->input('admin_notes', 'Ditolak admin melalui panel sistem.'),
        ]);

        return redirect()->route('admin.withdrawals.index')->with('success', 'Pengajuan withdrawal berhasil ditolak.');
    }

    public function markPaid(VendorWithdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'approved') {
            return redirect()->route('admin.withdrawals.index')->with('error', 'Hanya pengajuan berstatus approved yang bisa ditandai paid.');
        }

        DB::beginTransaction();

        try {
            $withdrawal->update([
                'status' => 'paid',
                'processed_at' => now(),
                'admin_notes' => 'Dana telah ditransfer ke rekening vendor.',
            ]);

            DB::commit();

            return redirect()->route('admin.withdrawals.index')->with('success', 'Withdrawal sudah ditandai sebagai paid.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('admin.withdrawals.index')->with('error', 'Gagal memperbarui status withdrawal.');
        }
    }
}