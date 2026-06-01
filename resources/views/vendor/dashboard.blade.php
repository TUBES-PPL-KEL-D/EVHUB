@extends('layouts.app')

@section('title', 'Dashboard Vendor')

@section('content')
@php
    $currency = fn ($amount) => 'Rp ' . number_format((float) $amount, 0, ',', '.');
@endphp

<div class="vendor-scope text-slate-800">
    <div class="mx-auto max-w-7xl space-y-6">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 px-4 py-3 text-sm text-emerald-700 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-100 bg-red-50/70 px-4 py-3 text-sm text-red-700 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-[2rem] bg-white/90 shadow-sm ring-1 ring-slate-200/80 backdrop-blur-sm">
            <div class="border-b border-slate-200/80 bg-gradient-to-r from-slate-50 via-slate-50 to-emerald-50/60 px-6 py-6">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-700/80">Dashboard Pemilik Mesin</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-800">Rekap Pendapatan {{ $vendor->company_name }}</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-500">Ringkasan pendapatan dari transaksi mesin yang dimiliki vendor aktif.</p>
            </div>

            <div class="grid gap-4 px-6 py-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl bg-slate-800 p-5 text-white shadow-md shadow-slate-900/10">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-300/80">Total Pendapatan</p>
                    <p class="mt-3 text-3xl font-black">{{ $currency($totalRevenue) }}</p>
                    <p class="mt-2 text-sm text-slate-300/90">Dari transaksi sukses</p>
                </div>
                <div class="rounded-3xl bg-emerald-400/90 p-5 text-white shadow-md shadow-emerald-500/10">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-50/90">Transaksi Sukses</p>
                    <p class="mt-3 text-3xl font-black">{{ $successTransactions }}</p>
                    <p class="mt-2 text-sm text-emerald-50/90">Transaksi selesai dan dibayar</p>
                </div>
                <div class="rounded-3xl bg-amber-300/90 p-5 text-slate-900 shadow-md shadow-amber-500/10">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-900/70">Rata-rata Pendapatan</p>
                    <p class="mt-3 text-3xl font-black">{{ $currency($averageRevenue) }}</p>
                    <p class="mt-2 text-sm text-amber-900/70">Per transaksi sukses</p>
                </div>
                <div class="rounded-3xl bg-slate-50 p-5 text-slate-800 ring-1 ring-slate-200/80">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Total Mesin</p>
                    <p class="mt-3 text-3xl font-black">{{ $chargers->count() }}</p>
                    <p class="mt-2 text-sm text-slate-600">Terhubung ke vendor ini</p>
                </div>
            </div>

            <div class="grid gap-4 px-6 pb-6 md:grid-cols-3">
                <div class="rounded-3xl border border-slate-200/80 bg-slate-50/70 p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">Total Transaksi</p>
                    <p class="mt-2 text-2xl font-bold text-slate-800">{{ $totalTransactions }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200/80 bg-slate-50/70 p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">Transaksi Pending</p>
                    <p class="mt-2 text-2xl font-bold text-slate-800">{{ $pendingTransactions }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200/80 bg-slate-50/70 p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">Transaksi Gagal</p>
                    <p class="mt-2 text-2xl font-bold text-slate-800">{{ $failedTransactions }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-[2rem] bg-white/90 p-6 shadow-sm ring-1 ring-slate-200/80 backdrop-blur-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Rekap per Mesin</h2>
                        <p class="mt-1 text-sm text-slate-500">Pendapatan dihitung dari transaksi berstatus success.</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($revenueByMachine as $machine)
                        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/80 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-base font-bold text-slate-800">{{ $machine['machine_name'] }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $machine['spklu_name'] }}</p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-lg font-black text-emerald-600">{{ $currency($machine['revenue']) }}</p>
                                    <p class="text-sm text-slate-500">{{ $machine['transactions_count'] }} transaksi · {{ $machine['energy'] }} kWh</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300/80 bg-slate-50/80 px-4 py-8 text-center text-sm text-slate-500">
                            Belum ada transaksi sukses untuk mesin milik vendor ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[2rem] bg-white/90 p-6 shadow-sm ring-1 ring-slate-200/80 backdrop-blur-sm">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Transaksi Terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500">Lima transaksi terakhir pada mesin vendor.</p>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($recentTransactions as $transaction)
                        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-bold text-slate-800">{{ $transaction->chargerMachine?->name ?? 'Mesin' }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $transaction->user?->name ?? 'Pengguna' }} · {{ $transaction->vehicle?->license_plate ?? 'Kendaraan' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $transaction->finished_at?->format('d M Y H:i') ?? $transaction->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-base font-black {{ $transaction->status === 'success' ? 'text-emerald-600' : ($transaction->status === 'failed' ? 'text-rose-500' : 'text-amber-600') }}">
                                        {{ strtoupper($transaction->status) }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $currency($transaction->total_price) }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300/80 bg-slate-50/80 px-4 py-8 text-center text-sm text-slate-500">
                            Belum ada transaksi untuk ditampilkan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] bg-white/90 p-6 shadow-sm ring-1 ring-slate-200/80 backdrop-blur-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Akses Cepat</h2>
                    <p class="mt-1 text-sm text-slate-500">Navigasi ke fitur vendor yang terkait langsung dengan dashboard ini.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('vendor.withdrawals.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300/80 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Withdrawal Dana</a>
                    <a href="{{ route('vendor.chargers.usageHistory') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-600">Lihat Riwayat Penggunaan</a>
                    <a href="{{ route('vendor.chargers.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300/80 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Kelola Mesin</a>
                    <a href="{{ route('vendor.status') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300/80 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Status Vendor</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection