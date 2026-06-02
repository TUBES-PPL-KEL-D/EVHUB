<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Menandai pengajuan withdrawal sebagai PAID dan menyimpan berkas bukti transfer riil.
     * [TELAH DIPERBAIKI: MENANGKAP REQUEST FILE & UPDATE KOLOM receipt_path]
     */
    public function markPaid(Request $request, VendorWithdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'approved') {
            return redirect()->route('admin.withdrawals.index')->with('error', 'Hanya pengajuan berstatus approved yang bisa ditandai paid.');
        }

        // Validasi file bukti transfer yang diunggah oleh Admin
        $request->validate([
            'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'receipt.required' => 'Berkas bukti transfer manual wajib dilampirkan.',
            'receipt.file' => 'Input harus berupa berkas dokumen valid.',
            'receipt.mimes' => 'Format bukti transfer harus berupa PDF, JPG, JPEG, atau PNG.',
            'receipt.max' => 'Ukuran maksimal berkas bukti transfer adalah 5 MB.',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'status' => 'paid',
                'processed_at' => now(),
                'admin_notes' => 'Dana telah ditransfer ke rekening vendor.',
            ];

            // Proses pemindahan file fisik ke dalam disk storage public
            if ($request->hasFile('receipt')) {
                $path = $request->file('receipt')->store('vendor/receipts', 'public');
                $updateData['receipt_path'] = $path;
            }

            // Eksekusi pembaruan data baris transaksi ke database
            $withdrawal->update($updateData);

            DB::commit();
            return redirect()->route('admin.withdrawals.index')->with('success', 'Withdrawal berhasil ditandai paid dan bukti transfer tersimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.withdrawals.index')->with('error', 'Gagal memproses bukti transfer: ' . $e->getMessage());
        }
    }
}