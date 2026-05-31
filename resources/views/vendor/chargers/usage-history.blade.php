@extends('layouts.app')

@section('title', 'Riwayat Pemakaian Mesin')

@section('content')
<div class="vendor-scope">
    <div class="mx-auto max-w-7xl">

        <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="mt-2 text-3xl font-bold text-white drop-shadow-[0_2px_6px_rgba(0,0,0,0.45)]">
                    Riwayat Pemakaian Mesin
                </h1>
                <p class="mt-2 text-slate-200 drop-shadow-[0_1px_3px_rgba(0,0,0,0.45)]">
                    Pantau penggunaan mesin charger, konsumsi energi, dan pendapatan stasiun Anda.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('vendor.chargers.index') }}"
                   class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100">
                    Kembali ke Daftar Mesin
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-3xl bg-white p-5 shadow-md ring-1 ring-slate-200">
                <p class="text-sm font-medium text-slate-500">Total Transaksi</p>
                <p class="mt-2 text-2xl font-bold text-slate-900">
                    {{ $totalTransactions }}
                </p>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-md ring-1 ring-slate-200">
                <p class="text-sm font-medium text-slate-500">Transaksi Sukses</p>
                <p class="mt-2 text-2xl font-bold text-emerald-600">
                    {{ $successTransactions }}
                </p>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-md ring-1 ring-slate-200">
                <p class="text-sm font-medium text-slate-500">Total Energi Terpakai</p>
                <p class="mt-2 text-2xl font-bold text-slate-900">
                    {{ number_format($totalUsage, 2, ',', '.') }} kWh
                </p>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-md ring-1 ring-slate-200">
                <p class="text-sm font-medium text-slate-500">Total Pendapatan</p>
                <p class="mt-2 text-2xl font-bold text-emerald-600">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl bg-white shadow-md ring-1 ring-slate-200">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h2 class="text-lg font-bold text-slate-900">
                    Detail Riwayat Pemakaian
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Data diurutkan dari transaksi terbaru.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-white text-slate-800 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 font-semibold">ID</th>
                            <th class="px-6 py-4 font-semibold">Mesin</th>
                            <th class="px-6 py-4 font-semibold">SPKLU</th>
                            <th class="px-6 py-4 font-semibold">Pengendara</th>
                            <th class="px-6 py-4 font-semibold">Kendaraan</th>
                            <th class="px-6 py-4 font-semibold">Energi</th>
                            <th class="px-6 py-4 font-semibold">Total Harga</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Waktu Mulai</th>
                            <th class="px-6 py-4 font-semibold">Waktu Selesai</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($transactions as $transaction)
                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-700">
                                    #TRX-{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">
                                        {{ $transaction->chargerMachine->name ?? '-' }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $transaction->chargerMachine->connector_type ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    {{ $transaction->chargerMachine->spklu->name ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800">
                                        {{ $transaction->user->name ?? 'User tidak ditemukan' }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $transaction->user->email ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($transaction->vehicle)
                                        <div class="font-medium text-slate-800">
                                            {{ $transaction->vehicle->merk }} {{ $transaction->vehicle->model }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            {{ $transaction->vehicle->license_plate }}
                                        </div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ number_format($transaction->energy_consumed, 2, ',', '.') }} kWh
                                </td>

                                <td class="px-6 py-4 font-semibold text-emerald-600">
                                    Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        {{ $transaction->status === 'success' ? 'bg-emerald-100 text-emerald-800' : 
                                           ($transaction->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ strtoupper($transaction->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    {{ $transaction->started_at ? $transaction->started_at->format('d M Y H:i') : '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $transaction->finished_at ? $transaction->finished_at->format('d M Y H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-3 h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m4 0V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10m18 0a2 2 0 01-2 2H5a2 2 0 01-2-2" />
                                    </svg>
                                    <p class="text-base font-medium text-slate-700">
                                        Belum ada riwayat pemakaian
                                    </p>
                                    <p class="mt-1">
                                        Riwayat akan muncul setelah pengendara menggunakan mesin charger Anda.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection