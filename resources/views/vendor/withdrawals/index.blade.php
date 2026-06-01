@extends('layouts.app')

@section('title', 'Withdrawal Vendor')

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-700">Withdrawal Vendor</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Penarikan Dana ke Rekening</h1>
                <p class="mt-2 text-sm text-slate-500">Ajukan penarikan dari pendapatan mesin dan pantau status prosesnya.</p>
            </div>
            <a href="{{ route('vendor.dashboard') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Kembali ke Dashboard</a>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl bg-slate-900 p-5 text-white shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Pendapatan Masuk</p>
                <p class="mt-3 text-2xl font-black">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-3xl bg-emerald-500 p-5 text-white shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-50/80">Saldo Tersedia</p>
                <p class="mt-3 text-2xl font-black">Rp{{ number_format($availableBalance, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-3xl bg-amber-100 p-5 text-slate-900 shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-amber-800">Dalam Proses</p>
                <p class="mt-3 text-2xl font-black">Rp{{ number_format($pendingAmount + $approvedAmount, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-3xl bg-slate-100 p-5 text-slate-900 shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Sudah Dibayar</p>
                <p class="mt-3 text-2xl font-black">Rp{{ number_format($paidAmount, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-xl font-bold text-slate-900">Ajukan Withdrawal</h2>
            <p class="mt-1 text-sm text-slate-500">Pastikan nominal tidak melebihi saldo yang tersedia.</p>

            <form action="{{ route('vendor.withdrawals.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700" for="amount">Nominal</label>
                    <input type="number" name="amount" id="amount" min="10000" step="0.01" value="{{ old('amount') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="50000">
                    @error('amount') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="bank_name">Nama Bank</label>
                        <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="BCA">
                        @error('bank_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700" for="bank_account_number">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="1234567890">
                        @error('bank_account_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700" for="bank_account_name">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_account_name" id="bank_account_name" value="{{ old('bank_account_name') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="Nama sesuai rekening">
                    @error('bank_account_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700" for="notes">Catatan</label>
                    <textarea name="notes" id="notes" rows="4" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="Opsional">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Kirim Pengajuan</button>
            </form>
        </div>

        <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-xl font-bold text-slate-900">Riwayat Withdrawal</h2>
            <p class="mt-1 text-sm text-slate-500">Pantau status pengajuan terbaru di sini.</p>

            <div class="mt-6 space-y-4">
                @forelse ($withdrawals as $withdrawal)
                    @php
                        $statusStyles = [
                            'pending' => 'bg-amber-100 text-amber-800',
                            'approved' => 'bg-blue-100 text-blue-800',
                            'rejected' => 'bg-rose-100 text-rose-800',
                            'paid' => 'bg-emerald-100 text-emerald-800',
                        ];
                        $statusClass = $statusStyles[$withdrawal->status] ?? 'bg-slate-100 text-slate-700';
                    @endphp
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">{{ $withdrawal->reference_code }}</p>
                                <p class="mt-1 text-lg font-bold text-slate-900">Rp{{ number_format($withdrawal->amount, 0, ',', '.') }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $withdrawal->bank_name }} · {{ $withdrawal->bank_account_name }} · {{ $withdrawal->bank_account_number }}</p>
                                @if($withdrawal->admin_notes)
                                    <p class="mt-2 text-sm text-slate-600">{{ $withdrawal->admin_notes }}</p>
                                @endif
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ strtoupper($withdrawal->status) }}</span>
                                <p class="mt-2 text-xs text-slate-400">{{ $withdrawal->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                        Belum ada pengajuan withdrawal.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection