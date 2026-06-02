@extends('layouts.app') 
@section('title', 'Riwayat Pemakaian Mesin') 

@section('content') 
<div class="space-y-6 animate-fade-in-up pb-10"> 
    
    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-400">Log Infrastruktur</p>
            <h1 class="mt-2 text-3xl font-black text-white tracking-tight">Riwayat Pemakaian Mesin</h1>
            <p class="mt-1 text-sm text-slate-400">Pantau log penggunaan mesin charger, konsumsi energi, dan pendapatan stasiun Anda.</p>
        </div>
        <a href="{{ route('vendor.chargers.index') }}" class="bg-slate-800 text-slate-200 border border-slate-700 rounded-xl px-5 py-3 text-xs font-black uppercase tracking-widest hover:bg-slate-700 transition-colors shrink-0">
            Kembali ke Daftar Mesin
        </a>
    </div>

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl flex items-center space-x-3 shadow-sm">
        <div class="bg-emerald-500 p-1.5 rounded-full text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <p class="font-bold text-emerald-400 text-sm">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-500/10 border border-rose-500/20 p-4 rounded-2xl flex items-center space-x-3 shadow-sm">
        <div class="bg-rose-500 p-1.5 rounded-full text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>
        <p class="font-bold text-rose-400 text-sm">{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4"> 
        <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Transaksi</p>
            <p class="text-2xl font-black text-white mt-2">{{ $totalTransactions }}</p>
        </div>
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-emerald-500/80 uppercase tracking-wider">Transaksi Sukses</p>
            <p class="text-2xl font-black text-emerald-400 mt-2">{{ $successTransactions }}</p>
        </div>
        <div class="bg-blue-500/10 border border-blue-500/20 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-blue-500/80 uppercase tracking-wider">Total Energi Terpakai</p>
            <p class="text-2xl font-black text-blue-400 mt-2">{{ number_format($totalUsage, 2, ',', '.') }} <span class="text-sm">kWh</span></p>
        </div>
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-amber-500/80 uppercase tracking-wider">Total Pendapatan</p>
            <p class="text-2xl font-black text-amber-400 mt-2">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
    </div> 

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl overflow-hidden">
        <h2 class="text-lg font-bold text-white mb-1">Detail Riwayat Pemakaian</h2> 
        <p class="text-xs text-slate-400 mb-5">Data diurutkan berdasarkan transaksi yang paling baru terjadi.</p> 

        <div class="overflow-x-auto"> 
            <table class="w-full text-left text-sm text-slate-400 border-collapse min-w-[1000px]"> 
                <thead class="border-b border-slate-800 text-xs font-bold uppercase tracking-wider"> 
                    <tr> 
                        <th class="pb-4 pl-2">ID</th> 
                        <th class="pb-4">Mesin</th> 
                        <th class="pb-4">SPKLU</th> 
                        <th class="pb-4">Pengendara</th> 
                        <th class="pb-4">Kendaraan</th> 
                        <th class="pb-4">Energi</th> 
                        <th class="pb-4">Total Harga</th> 
                        <th class="pb-4 text-center">Status</th> 
                        <th class="pb-4">Waktu Mulai</th> 
                        <th class="pb-4">Waktu Selesai</th> 
                    </tr> 
                </thead> 
                <tbody class="divide-y divide-slate-800/60"> 
                    @forelse($transactions as $transaction) 
                        <tr class="hover:bg-slate-800/20 transition-colors"> 
                            <td class="py-4 pl-2 font-mono text-[10px] font-bold text-slate-500">#TRX-{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</td> 
                            <td class="py-4"> 
                                <div class="font-bold text-white">{{ $transaction->chargerMachine->name ?? '-' }}</div> 
                                <div class="text-[10px] font-black tracking-wider uppercase text-emerald-400 mt-0.5">{{ $transaction->chargerMachine->connector_type ?? '-' }}</div> 
                            </td> 
                            <td class="py-4 font-medium text-slate-300">{{ $transaction->chargerMachine->spklu->name ?? '-' }}</td> 
                            <td class="py-4"> 
                                <div class="font-bold text-white">{{ $transaction->user->name ?? 'Tidak ditemukan' }}</div> 
                                <div class="text-[10px] text-slate-500 mt-0.5">{{ $transaction->user->email ?? '-' }}</div> 
                            </td> 
                            <td class="py-4"> 
                                @if($transaction->vehicle) 
                                    <div class="font-bold text-slate-300">{{ $transaction->vehicle->merk }} {{ $transaction->vehicle->model }}</div> 
                                    <div class="text-[10px] font-mono tracking-widest text-slate-500 mt-0.5 uppercase">{{ $transaction->vehicle->license_plate }}</div> 
                                @else 
                                    <span class="text-slate-600">-</span> 
                                @endif 
                            </td> 
                            <td class="py-4 font-black text-blue-400">{{ number_format($transaction->energy_consumed, 2, ',', '.') }} kWh</td> 
                            <td class="py-4 font-black text-emerald-400">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</td> 
                            <td class="py-4 text-center"> 
                                @php
                                    $statusColor = match($transaction->status) {
                                        'success' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                        'pending' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                                        default   => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-black tracking-widest uppercase {{ $statusColor }}"> 
                                    {{ $transaction->status }} 
                                </span> 
                            </td> 
                            <td class="py-4 text-[10px] font-mono text-slate-500">{{ $transaction->started_at ? $transaction->started_at->format('d M Y H:i') : '-' }}</td> 
                            <td class="py-4 text-[10px] font-mono text-slate-500">{{ $transaction->finished_at ? $transaction->finished_at->format('d M Y H:i') : '-' }}</td> 
                        </tr> 
                    @empty 
                        <tr> 
                            <td colspan="10" class="py-12 text-center border-dashed border border-slate-800/60 rounded-3xl"> 
                                <p class="text-slate-500 font-medium text-xs uppercase tracking-wider">Belum ada riwayat pemakaian mesin dari pengendara.</p> 
                            </td> 
                        </tr> 
                    @endforelse 
                </tbody> 
            </table> 
        </div> 
    </div> 
</div> 
@endsection