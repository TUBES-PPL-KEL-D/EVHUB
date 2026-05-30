@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <div class="flex justify-between items-center">
        <a href="{{ route('rider.transactions.index') }}" class="inline-flex items-center text-sm font-medium text-slate-400 hover:text-emerald-400 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar Riwayat
        </a>
        
        <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-4 rounded-xl text-xs transition-colors flex items-center space-x-2 shadow-lg shadow-emerald-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Unduh Bukti Struk</span>
        </button>
    </div>

    <div class="bg-slate-950/50 border border-slate-800 backdrop-blur-sm rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>

        <div class="text-center pb-6 border-b border-slate-800/80">
            <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-400 mx-auto mb-3 border border-emerald-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight">Rincian Transaksi EVHUB</h2>
            <p class="text-xs text-slate-500 mt-1">ID Transaksi: #TRX-{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="py-6 space-y-4 text-sm border-b border-slate-800/80">
            <div class="flex justify-between">
                <span class="text-slate-500">Status Pembayaran</span>
                <span class="font-semibold text-emerald-400">Sukses (Lunas Otomatis)</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Waktu Pengisian</span>
                <span class="text-white font-medium">{{ $transaction->created_at->format('d F Y, H:i') }} WIB</span>
            </div>
            <div class="flex justify-between border-t border-slate-700/50 pt-4">
                <span class="text-slate-500">Kendaraan</span>
                <span class="text-white font-medium">{{ $transaction->vehicle->merk }} {{ $transaction->vehicle->model }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Nomor Kendaraan (No. Polisi)</span>
                <span class="text-white font-mono font-semibold bg-slate-800 px-2 py-0.5 rounded border border-slate-700">{{ $transaction->vehicle->license_plate }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Stasiun SPKLU</span>
                <span class="text-white font-medium">{{ $transaction->chargerMachine->spklu->name ?? 'SPKLU Pusat' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Mesin Pengisi (Charger)</span>
                <span class="text-white font-medium">{{ $transaction->chargerMachine->name }} ({{ $transaction->chargerMachine->connector_type }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Tarif per kWh</span>
                <span class="text-white font-medium">Rp{{ number_format($transaction->chargerMachine->price_per_kwh, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="pt-6 space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-medium">Total Daya Dikonsumsi</span>
                <span class="text-lg font-bold text-emerald-400">{{ $transaction->energy_consumed }} kWh</span>
            </div>
            
            <div class="bg-slate-900/60 border border-slate-800/60 p-4 rounded-2xl flex justify-between items-center mt-2">
                <span class="text-sm font-bold text-white">Total Pengeluaran</span>
                <span class="text-2xl font-extrabold text-white">
                    Rp{{ number_format($transaction->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <div class="text-center text-[11px] text-slate-600 mt-8 pt-4 border-t border-dashed border-slate-800">
            Metode Pembayaran: Dompet Digital EV-Pay<br>
            Terima kasih telah berkontribusi mengurangi emisi karbon bersama EV-HUB.
        </div>
    </div>
</div>
@endsection