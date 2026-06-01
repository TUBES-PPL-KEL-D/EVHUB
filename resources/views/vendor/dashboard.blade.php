@extends('layouts.app')

@section('title', 'Dashboard Vendor')

@section('content')
@php
    $currency = fn ($amount) => 'Rp ' . number_format((float) $amount, 0, ',', '.');
@endphp

<div class="vendor-scope text-slate-900">
    <div class="mx-auto max-w-7xl space-y-6">
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

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-200 bg-gradient-to-r from-slate-50 to-emerald-50 px-6 py-6">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-700">Dashboard Pemilik Mesin</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Rekap Pendapatan {{ $vendor->company_name }}</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-600">Halaman ini menampilkan ringkasan pendapatan dari transaksi mesin yang dimiliki vendor aktif.</p>
            </div>

            <div class="grid gap-4 px-6 py-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl bg-slate-900 p-5 text-white shadow-lg">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Total Pendapatan</p>
                    <p class="mt-3 text-3xl font-black">{{ $currency($totalRevenue) }}</p>
                    <p class="mt-2 text-sm text-slate-400">Dari transaksi sukses</p>
                </div>
                <div class="rounded-3xl bg-emerald-500 p-5 text-white shadow-lg">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-50/80">Transaksi Sukses</p>
                    <p class="mt-3 text-3xl font-black">{{ $successTransactions }}</p>
                    <p class="mt-2 text-sm text-emerald-50/90">Transaksi selesai dan dibayar</p>
                </div>
                <div class="rounded-3xl bg-amber-500 p-5 text-white shadow-lg">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-50/80">Rata-rata Pendapatan</p>
                    <p class="mt-3 text-3xl font-black">{{ $currency($averageRevenue) }}</p>
                    <p class="mt-2 text-sm text-amber-50/90">Per transaksi sukses</p>
                </div>
                <div class="rounded-3xl bg-slate-100 p-5 text-slate-900 ring-1 ring-slate-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Total Mesin</p>
                    <p class="mt-3 text-3xl font-black">{{ $chargers->count() }}</p>
                    <p class="mt-2 text-sm text-slate-600">Terhubung ke vendor ini</p>
                </div>
            </div>

            <div class="grid gap-4 px-6 pb-6 md:grid-cols-3">
                <div class="rounded-3xl border border-slate-200 bg-white p-5">
                    <p class="text-sm font-semibold text-slate-500">Total Transaksi</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $totalTransactions }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-5">
                    <p class="text-sm font-semibold text-slate-500">Transaksi Pending</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $pendingTransactions }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-5">
                    <p class="text-sm font-semibold text-slate-500">Transaksi Gagal</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $failedTransactions }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Rekap per Mesin</h2>
                        <p class="mt-1 text-sm text-slate-500">Pendapatan dihitung dari transaksi berstatus success.</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($revenueByMachine as $machine)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-base font-bold text-slate-900">{{ $machine['machine_name'] }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $machine['spklu_name'] }}</p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-lg font-black text-emerald-700">{{ $currency($machine['revenue']) }}</p>
                                    <p class="text-sm text-slate-500">{{ $machine['transactions_count'] }} transaksi · {{ $machine['energy'] }} kWh</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                            Belum ada transaksi sukses untuk mesin milik vendor ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Transaksi Terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500">Lima transaksi terakhir pada mesin vendor.</p>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($recentTransactions as $transaction)
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-bold text-slate-900">{{ $transaction->chargerMachine?->name ?? 'Mesin' }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $transaction->user?->name ?? 'Pengguna' }} · {{ $transaction->vehicle?->license_plate ?? 'Kendaraan' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $transaction->finished_at?->format('d M Y H:i') ?? $transaction->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-base font-black {{ $transaction->status === 'success' ? 'text-emerald-700' : ($transaction->status === 'failed' ? 'text-rose-600' : 'text-amber-600') }}">
                                        {{ strtoupper($transaction->status) }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $currency($transaction->total_price) }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                            Belum ada transaksi untuk ditampilkan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Akses Cepat</h2>
                    <p class="mt-1 text-sm text-slate-500">Navigasi ke fitur vendor yang terkait langsung dengan dashboard ini.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('vendor.chargers.usageHistory') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Lihat Riwayat Penggunaan</a>
                    <a href="{{ route('vendor.chargers.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Kelola Mesin</a>
                    <a href="{{ route('vendor.status') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Status Vendor</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection