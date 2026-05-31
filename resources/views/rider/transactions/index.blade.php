@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b border-slate-800/60 pb-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Riwayat Pengisian Daya</h1>
            <p class="text-slate-400 text-sm mt-1">Pantau konsumsi energi listrik (kWh) dan pengeluaran transaksi Anda.</p>
        </div>
        <div class="flex-shrink-0">
            <a href="javascript:history.back()" class="flex py-2.5 px-6 rounded-xl items-center text-sm font-bold bg-slate-800 text-white border border-slate-700 hover:bg-slate-700 hover:border-emerald-500/50 transition-all duration-300 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-4 py-3 rounded-xl text-sm flex items-center space-x-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-slate-950/40 border border-slate-800/80 backdrop-blur-sm rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-900/50 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="py-4 px-6">Mesin / SPKLU</th>
                        <th class="py-4 px-6">Waktu Mulai</th>
                        <th class="py-4 px-6">Daya Terpakai</th>
                        <th class="py-4 px-6">Total Biaya</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-sm text-slate-300">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-900/30 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-semibold text-white">{{ $tx->chargerMachine->name ?? 'Mesin Tidak Dikenal' }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">Tipe: {{ $tx->chargerMachine->connector_type ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div>{{ $tx->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $tx->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="py-4 px-6 font-medium">
                                @if($tx->status == 'success')
                                    <span class="text-emerald-400">{{ $tx->energy_consumed }} kWh</span>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-bold text-white">
                                @if($tx->status == 'success')
                                    Rp{{ number_format($tx->total_price, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if($tx->status == 'success')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Berhasil</span>
                                @elseif($tx->status == 'pending')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">Sedang Men-charge</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">Gagal</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($tx->status == 'pending')
                                    <form action="{{ route('rider.transactions.stop', $tx->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-rose-600 hover:bg-rose-500 text-white font-bold py-1.5 px-4 rounded-lg text-xs transition-all shadow-md">
                                            Selesai
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('rider.transactions.show', $tx->id) }}" class="inline-flex bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium py-1.5 px-4 rounded-lg text-xs border border-slate-700 transition-colors">
                                        Lihat Nota
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-500">
                                <svg class="w-12 h-12 text-slate-800 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <p class="text-sm">Tidak ditemukan riwayat pengisian daya.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection