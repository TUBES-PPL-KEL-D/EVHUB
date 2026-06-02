@extends('layouts.app')

@section('content')
<div class="space-y-6 animate-fade-in-up pb-10">
    
    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-400">Dashboard Pemilik Mesin</p>
            <h1 class="mt-2 text-3xl font-black text-white tracking-tight">Rekap Pendapatan {{ $vendor->company_name }}</h1>
            <p class="mt-1 text-sm text-slate-400">Ringkasan pendapatan dari transaksi mesin yang dimiliki vendor aktif.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pendapatan</p>
            <p class="text-3xl font-black text-white mt-2">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-[10px] text-slate-500 mt-1 font-medium">Dari transaksi sukses</p>
        </div>
        
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-emerald-500/80 uppercase tracking-wider">Transaksi Sukses</p>
            <p class="text-3xl font-black text-emerald-400 mt-2">{{ $successTransactions }}</p>
            <p class="text-[10px] text-emerald-500/60 mt-1 font-medium">Transaksi selesai dan dibayar</p>
        </div>
        
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-amber-500/80 uppercase tracking-wider">Rata-rata Pendapatan</p>
            <p class="text-3xl font-black text-amber-400 mt-2">Rp{{ number_format($averageRevenue, 0, ',', '.') }}</p>
            <p class="text-[10px] text-amber-500/60 mt-1 font-medium">Per transaksi sukses</p>
        </div>
        
        <div class="bg-blue-500/10 border border-blue-500/20 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-blue-500/80 uppercase tracking-wider">Total Mesin</p>
            <p class="text-3xl font-black text-blue-400 mt-2">{{ $chargers->count() }}</p>
            <p class="text-[10px] text-blue-500/60 mt-1 font-medium">Terhubung ke vendor ini</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl p-5 flex justify-between items-center hover:border-slate-600 transition-colors">
            <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</span>
            <span class="text-2xl font-black text-white">{{ $totalTransactions }}</span>
        </div>
        <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl p-5 flex justify-between items-center hover:border-slate-600 transition-colors">
            <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Transaksi Pending</span>
            <span class="text-2xl font-black text-white">{{ $pendingTransactions }}</span>
        </div>
        <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl p-5 flex justify-between items-center hover:border-slate-600 transition-colors">
            <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Transaksi Gagal</span>
            <span class="text-2xl font-black text-white">{{ $failedTransactions }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
            <h2 class="text-lg font-bold text-white mb-1">Rekap per Mesin</h2>
            <p class="text-xs text-slate-400 mb-5">Pendapatan dihitung dari transaksi berstatus success.</p>
            
            <div class="space-y-3">
                @forelse ($revenueByMachine as $rev)
                    <div class="p-4 bg-slate-800/30 rounded-2xl border border-slate-700/40 flex justify-between items-center transition-all hover:bg-slate-800/50">
                        <div>
                            <p class="text-sm font-bold text-white">{{ $rev['machine_name'] }}</p>
                            <p class="text-[10px] text-slate-500 font-medium uppercase tracking-wider mt-0.5">{{ $rev['spklu_name'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-emerald-400">Rp{{ number_format($rev['revenue'], 0, ',', '.') }}</p>
                            <p class="text-[10px] text-slate-500 font-medium mt-0.5">{{ $rev['transactions_count'] }} transaksi &middot; {{ $rev['energy'] }} kWh</p>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center border border-dashed border-slate-700/60 rounded-2xl">
                        <p class="text-slate-500 font-medium text-xs uppercase tracking-wider">Belum ada data pendapatan mesin.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
            <h2 class="text-lg font-bold text-white mb-1">Transaksi Terbaru</h2>
            <p class="text-xs text-slate-400 mb-5">Lima transaksi terakhir pada mesin vendor.</p>
            
            <div class="space-y-3">
                @forelse ($recentTransactions as $trx)
                    <div class="p-4 bg-slate-800/30 rounded-2xl border border-slate-700/40 flex justify-between items-center transition-all hover:bg-slate-800/50">
                        <div>
                            <p class="text-sm font-bold text-white">{{ $trx->chargerMachine->name ?? 'Mesin Dihapus' }}</p>
                            <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $trx->user->name ?? 'Pengendara EV' }} <span class="text-slate-600 mx-1">&middot;</span> <span class="font-mono text-slate-300">{{ $trx->vehicle->license_plate ?? '-' }}</span></p>
                            <p class="text-[10px] text-slate-500 font-mono mt-0.5">{{ $trx->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="text-right flex flex-col justify-between h-full">
                            @php
                                $statusColor = match($trx->status) {
                                    'success' => 'text-emerald-400',
                                    'pending' => 'text-amber-400',
                                    'failed'  => 'text-rose-400',
                                    default   => 'text-slate-400'
                                };
                            @endphp
                            <p class="text-[10px] font-black uppercase tracking-widest {{ $statusColor }}">{{ $trx->status }}</p>
                            <p class="text-sm font-bold text-white mt-1">Rp{{ number_format($trx->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center border border-dashed border-slate-700/60 rounded-2xl">
                        <p class="text-slate-500 font-medium text-xs uppercase tracking-wider">Belum ada riwayat transaksi.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-lg font-bold text-white mb-1">Akses Cepat</h2>
            <p class="text-xs text-slate-400">Navigasi ke fitur vendor yang terkait langsung dengan operasional.</p>
        </div>
        <div class="flex flex-wrap gap-3 w-full md:w-auto">
            <a href="{{ route('vendor.withdrawals.index') }}" class="flex-grow md:flex-grow-0 text-center bg-emerald-500 text-slate-900 hover:bg-emerald-400 px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-colors shadow-lg shadow-emerald-500/10">Withdrawal Dana</a>
            <a href="{{ route('vendor.chargers.usageHistory') }}" class="flex-grow md:flex-grow-0 text-center bg-slate-800 text-slate-300 border border-slate-700 hover:bg-slate-700 hover:text-white px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-colors">Lihat Riwayat</a>
            <a href="{{ route('vendor.chargers.index') }}" class="flex-grow md:flex-grow-0 text-center bg-slate-800 text-slate-300 border border-slate-700 hover:bg-slate-700 hover:text-white px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-colors">Kelola Mesin</a>
            <a href="{{ route('vendor.status') }}" class="flex-grow md:flex-grow-0 text-center bg-slate-800 text-slate-300 border border-slate-700 hover:bg-slate-700 hover:text-white px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-colors">Status Vendor</a>
        </div>
    </div>

</div>
@endsection