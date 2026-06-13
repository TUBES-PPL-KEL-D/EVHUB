@extends('layouts.app')

@section('content')
<div class="space-y-6 animate-fade-in-up pb-10">
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

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-400">Withdrawal Sistem</p>
            <h1 class="mt-2 text-3xl font-black text-white tracking-tight">Penarikan Dana Pendapatan</h1>
            <p class="mt-1 text-sm text-slate-400">Ajukan pencairan hasil operasional mesin charger Anda langsung ke rekening bank.</p>
        </div>
        <a href="{{ route('vendor.dashboard') }}" class="bg-slate-800 text-slate-200 border border-slate-700 rounded-xl px-5 py-3 text-xs font-black uppercase tracking-widest hover:bg-slate-700 transition-colors shrink-0">
            Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-950/40 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pendapatan Kotor</p>
            <p class="text-2xl font-black text-white mt-2">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-emerald-500/80 uppercase tracking-wider">Saldo Tersedia</p>
            <p class="text-2xl font-black text-emerald-400 mt-2">Rp{{ number_format($availableBalance, 0, ',', '.') }}</p>
        </div>
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-amber-500/80 uppercase tracking-wider">Sedang Diproses</p>
            <p class="text-2xl font-black text-amber-400 mt-2">Rp{{ number_format($pendingAmount + $approvedAmount, 0, ',', '.') }}</p>
        </div>
        <div class="bg-blue-500/10 border border-blue-500/20 rounded-3xl p-6 shadow-xl">
            <p class="text-xs font-bold text-blue-500/80 uppercase tracking-wider">Berhasil Dicairkan</p>
            <p class="text-2xl font-black text-blue-400 mt-2">Rp{{ number_format($paidAmount, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-1 bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl">
            <h2 class="text-lg font-bold text-white mb-1">Ajukan Pencairan</h2>
            <p class="text-xs text-slate-400 mb-5">Batas penarikan dana minimum senilai Rp10.000.</p>
            
            <form action="{{ route('vendor.withdrawals.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5 ml-0.5">Nominal Withdrawal (Rp)</label>
                    <input type="number" name="amount" min="10000" max="{{ $availableBalance }}" placeholder="Contoh: 50000" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors">
                </div>

                <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-4">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencairan otomatis ditujukan ke:</p>
                    <p class="text-sm font-bold text-emerald-400 uppercase">{{ $vendor->profile->bank_name ?? 'BANK BELUM DIATUR' }} - {{ $vendor->profile->bank_account_number ?? '-' }}</p>
                    <p class="text-xs text-slate-300">a/n {{ $vendor->profile->bank_account_name ?? '-' }}</p>
                    
                    <input type="hidden" name="bank_name" value="{{ $vendor->profile->bank_name ?? '' }}">
                    <input type="hidden" name="bank_account_name" value="{{ $vendor->profile->bank_account_name ?? '' }}">
                    <input type="hidden" name="bank_account_number" value="{{ $vendor->profile->bank_account_number ?? '' }}">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5 ml-0.5">Catatan (Opsional)</label>
                    <textarea name="notes" placeholder="Pesan atau catatan tambahan untuk admin..." class="w-full h-20 px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors resize-none"></textarea>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white py-4 rounded-2xl shadow-lg shadow-emerald-500/20 transition-all duration-300 font-bold text-xl mt-4 hover:-translate-y-1">
                    Kirim Pengajuan
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
            <h2 class="text-lg font-bold text-white mb-1">Riwayat Pengajuan</h2>
            <p class="text-xs text-slate-400 mb-5">Daftar rekam log pelacakan status pencairan dana Anda.</p>
            
            <div class="space-y-4">
                @forelse ($withdrawals as $wd)
                    @php
                        $badgeStyles = [
                            'pending'  => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                            'approved' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                            'rejected' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                            'paid'     => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                        ];
                        $badgeClass = $badgeStyles[$wd->status] ?? 'bg-slate-800 text-slate-400';
                    @endphp
                    <div class="p-5 bg-slate-800/30 rounded-2xl border border-slate-700/40 flex flex-col sm:flex-row justify-between sm:items-center gap-4 transition-colors hover:border-slate-600">
                        <div class="space-y-2 flex-grow">
                            <div class="flex items-center gap-3">
                                <span class="font-mono text-xs font-bold text-white tracking-wider">{{ $wd->reference_code }}</span>
                                <span class="text-[10px] px-2.5 py-1 font-black uppercase tracking-widest rounded-full border {{ $badgeClass }}">{{ $wd->status }}</span>
                            </div>
                            <p class="text-lg font-black text-white">Rp{{ number_format($wd->amount, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-slate-500 font-medium">Rekening: <span class="uppercase text-slate-400 font-bold">{{ $wd->bank_name }}</span> - {{ $wd->bank_account_number }} a/n {{ $wd->bank_account_name }}</p>
                            @if($wd->notes)
                                <p class="text-[10px] text-slate-600 leading-relaxed bg-slate-950/40 p-2 rounded-lg mt-1 border border-slate-800">Catatan: "{{ $wd->notes }}"</p>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2 shrink-0 sm:items-end">
                            <span class="text-[10px] font-mono text-slate-500">{{ $wd->created_at->format('d/m/Y H:i') }}</span>
                            
                            @if ($wd->status === 'paid' && $wd->receipt_path)
                                <a href="{{ asset('storage/' . $wd->receipt_path) }}" target="_blank" class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500 hover:text-white px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all flex items-center gap-1.5 mt-1 whitespace-nowrap shadow-sm shadow-emerald-500/5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat Bukti
                                </a>
                            @elseif ($wd->status === 'rejected' && $wd->reject_reason)
                                <div class="bg-rose-500/5 border border-rose-500/10 p-2 rounded-lg mt-1 max-w-[200px]">
                                    <p class="text-[9px] text-rose-400/80 leading-relaxed">Alasan Penolakan Admin: "{{ $wd->reject_reason }}"</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center border border-dashed border-slate-700/60 rounded-2xl">
                        <p class="text-slate-500 font-medium text-xs uppercase tracking-wider">Belum ada aktivitas transaksi pencairan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection