<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Vendor;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VendorWithdrawalController extends Controller
{
    private function checkVendorStatus()
    {
        $vendor = Vendor::find(1);

        return $vendor;
    }

    private function buildSummary(Vendor $vendor): array
    {
        $totalRevenue = 0;

        if (Schema::hasTable('transactions')) {
            $totalRevenue = Transaction::whereHas('chargerMachine', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })->where('status', 'success')->sum('total_price');
        }

        $withdrawalsQuery = VendorWithdrawal::where('vendor_id', $vendor->id);
        $reservedAmount = (clone $withdrawalsQuery)
            ->whereIn('status', ['pending', 'approved', 'paid'])
            ->sum('amount');

        $pendingAmount = (clone $withdrawalsQuery)
            ->where('status', 'pending')
            ->sum('amount');

        $approvedAmount = (clone $withdrawalsQuery)
            ->where('status', 'approved')
            ->sum('amount');

        $paidAmount = (clone $withdrawalsQuery)
            ->where('status', 'paid')
            ->sum('amount');

        $rejectedCount = (clone $withdrawalsQuery)->where('status', 'rejected')->count();
        $withdrawals = (clone $withdrawalsQuery)->latest()->get();

        return [
            'totalRevenue' => (float) $totalRevenue,
            'reservedAmount' => (float) $reservedAmount,
            'availableBalance' => max(0, (float) $totalRevenue - (float) $reservedAmount),
            'pendingAmount' => (float) $pendingAmount,
            'approvedAmount' => (float) $approvedAmount,
            'paidAmount' => (float) $paidAmount,
            'rejectedCount' => $rejectedCount,
            'withdrawals' => $withdrawals,
        ];
    }

    public function index()
    {
        $vendor = $this->checkVendorStatus();

        if (! $vendor) {
            return redirect()->route('vendor.status')->with('error', 'Akses ditolak! Vendor ID 1 tidak ditemukan di database.');
        }

        if (! Schema::hasTable('vendor_withdrawals')) {
            return view('vendor.withdrawals.index', [
                'vendor' => $vendor,
                'totalRevenue' => 0,
                'reservedAmount' => 0,
                'availableBalance' => 0,
                'pendingAmount' => 0,
                'approvedAmount' => 0,
                'paidAmount' => 0,
                'rejectedCount' => 0,
                'withdrawals' => collect(),
            ])->with('error', 'Tabel withdrawal belum tersedia di database aktif. Jalankan migrasi database terlebih dahulu.');
        }

        $summary = $this->buildSummary($vendor);

        return view('vendor.withdrawals.index', array_merge(['vendor' => $vendor], $summary));
    }

    public function store(Request $request)
    {
        $vendor = $this->checkVendorStatus();

        if (! $vendor) {
            return redirect()->route('vendor.status')->with('error', 'Akses ditolak!');
        }

        if (! Schema::hasTable('transactions') || ! Schema::hasTable('vendor_withdrawals')) {
            return redirect()->route('vendor.withdrawals.index')->with('error', 'Tabel pendukung withdrawal belum tersedia di database aktif.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10000'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_name' => ['required', 'string', 'max:150'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'amount.required' => 'Nominal withdrawal wajib diisi.',
            'amount.numeric' => 'Nominal withdrawal harus berupa angka.',
            'amount.min' => 'Nominal withdrawal minimal Rp10.000.',
            'bank_name.required' => 'Nama bank wajib diisi.',
            'bank_account_name.required' => 'Nama pemilik rekening wajib diisi.',
            'bank_account_number.required' => 'Nomor rekening wajib diisi.',
        ]);

        $summary = $this->buildSummary($vendor);

        if ((float) $validated['amount'] > $summary['availableBalance']) {
            return redirect()->route('vendor.withdrawals.index')->with('error', 'Nominal melebihi saldo yang tersedia untuk ditarik.');
        }

        DB::beginTransaction();

        try {
            VendorWithdrawal::create([
                'vendor_id' => $vendor->id,
                'reference_code' => 'WD-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'amount' => $validated['amount'],
                'bank_name' => $validated['bank_name'],
                'bank_account_name' => $validated['bank_account_name'],
                'bank_account_number' => $validated['bank_account_number'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('vendor.withdrawals.index')->with('success', 'Pengajuan withdrawal berhasil dikirim dan sedang menunggu proses verifikasi.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('vendor.withdrawals.index')->with('error', 'Gagal memproses pengajuan withdrawal. Silakan coba lagi.');
        }
    }
}