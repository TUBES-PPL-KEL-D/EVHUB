@extends('layouts.app') 

@section('content') 
<div class="space-y-6 animate-fade-in-up pb-10"> 

    @if (session('success')) 
    <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl flex items-center space-x-3 shadow-sm"> 
        <div class="bg-emerald-500 p-1.5 rounded-full text-white"> 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> 
        </div> 
        <p class="font-bold text-emerald-400 text-sm">{{ session('success') }}</p> 
    </div> 
    @endif 

    @if (session('error')) 
    <div class="bg-rose-500/10 border border-rose-500/20 p-4 rounded-2xl flex items-center space-x-3 shadow-sm"> 
        <div class="bg-rose-500 p-1.5 rounded-full text-white"> 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg> 
        </div> 
        <p class="font-bold text-rose-400 text-sm">{{ session('error') }}</p> 
    </div> 
    @endif 

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div> 
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-400">Sistem Finansial</p> 
            <h1 class="mt-2 text-3xl font-black text-white tracking-tight">Daftar Pengajuan Pencairan Dana</h1> 
            <p class="mt-1 text-sm text-slate-400">Verifikasi, setujui, dan unggah bukti transfer manual kemitraan vendor.</p> 
        </div> 
        <a href="{{ route('admin.dashboard') }}" class="bg-slate-800 text-slate-200 border border-slate-700 rounded-xl px-5 py-3 text-xs font-black uppercase tracking-widest hover:bg-slate-700 transition-colors shrink-0">
            Kembali ke Dasbor
        </a> 
    </div> 

    <div class="grid gap-4 grid-cols-2 md:grid-cols-4"> 
        <div class="rounded-3xl bg-slate-900/60 border border-slate-700/40 p-5 text-white shadow-md"> 
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Masuk</p> 
            <p class="mt-2 text-3xl font-black text-white">{{ $withdrawals->count() }}</p> 
        </div> 
        <div class="rounded-3xl bg-amber-500/10 border border-amber-500/20 p-5 text-amber-400 shadow-md"> 
            <p class="text-xs font-bold uppercase tracking-wider text-amber-500/70">Pending</p> 
            <p class="mt-2 text-3xl font-black">{{ $withdrawals->where('status', 'pending')->count() }}</p> 
        </div> 
        <div class="rounded-3xl bg-blue-500/10 border border-blue-500/20 p-5 text-blue-400 shadow-md"> 
            <p class="text-xs font-bold uppercase tracking-wider text-blue-500/70">Approved</p> 
            <p class="mt-2 text-3xl font-black">{{ $withdrawals->where('status', 'approved')->count() }}</p> 
        </div> 
        <div class="rounded-3xl bg-emerald-500/10 border border-emerald-500/20 p-5 text-emerald-400 shadow-md"> 
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-500/70">Paid Out</p> 
            <p class="mt-2 text-3xl font-black">{{ $withdrawals->where('status', 'paid')->count() }}</p> 
        </div> 
    </div> 

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl"> 
        <div class="space-y-4"> 
            @forelse ($withdrawals as $withdrawal) 
                @php 
                    $statusStyles = [
                        'pending'   => 'bg-amber-500/10 text-amber-400 border border-amber-500/20', 
                        'approved'  => 'bg-blue-500/10 text-blue-400 border border-blue-500/20', 
                        'rejected'  => 'bg-rose-500/10 text-rose-400 border border-rose-500/20', 
                        'paid'      => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20', 
                    ]; 
                    $statusClass = $statusStyles[$withdrawal->status] ?? 'bg-slate-800 text-slate-400'; 
                @endphp 

                <div class="rounded-3xl border border-slate-700/40 bg-slate-800/30 p-6 transition-all hover:border-slate-600/50"> 
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6"> 
                        
                        <div class="space-y-3 flex-grow"> 
                            <div class="flex flex-wrap items-center gap-3"> 
                                <h2 class="text-xl font-bold text-white tracking-tight">{{ $withdrawal->vendor?->company_name ?? 'Vendor Utama' }}</h2> 
                                <span class="inline-flex rounded-full px-3 py-1 text-[10px] font-black tracking-widest uppercase {{ $statusClass }}">{{ $withdrawal->status }}</span> 
                            </div> 
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-slate-400">
                                <div>
                                    <p class="text-xs text-slate-500">Kode Referensi & Nominal</p>
                                    <p class="text-white font-bold mt-0.5"><span class="font-mono text-slate-400 font-medium mr-1">{{ $withdrawal->reference_code }}</span> · Rp{{ number_format($withdrawal->amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500">Rekening Tujuan Vendor</p>
                                    <p class="text-slate-200 font-medium mt-0.5 uppercase"><span class="font-black text-emerald-400">{{ $withdrawal->bank_name }}</span> · {{ $withdrawal->bank_account_name }} · <span class="font-mono text-white tracking-wider">{{ $withdrawal->bank_account_number }}</span></p>
                                </div>
                            </div>

                            @if($withdrawal->notes) 
                                <div class="bg-slate-900/30 border border-slate-800 p-3 rounded-xl text-xs text-slate-400 leading-relaxed">
                                    <span class="font-bold text-slate-500 block mb-0.5">Catatan Pengajuan Vendor:</span>
                                    "{{ $withdrawal->notes }}"
                                </div>
                            @endif 

                            <p class="text-[10px] font-mono text-slate-500 tracking-wide">Waktu Submit: {{ $withdrawal->created_at->format('d M Y, H:i') }} WIB</p> 
                        </div> 

                        <div class="flex flex-wrap gap-2 shrink-0 lg:justify-end"> 
                            
                            @if ($withdrawal->status === 'pending') 
                                <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST" class="inline"> 
                                    @csrf @method('PATCH') 
                                    <button type="submit" class="bg-emerald-500 text-slate-900 hover:bg-emerald-400 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-colors shadow-md">Setujui</button> 
                                </form> 
                                <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST" class="inline"> 
                                    @csrf @method('PATCH') 
                                    <button type="submit" class="bg-rose-500/10 text-rose-400 border border-rose-500/20 hover:bg-rose-500 hover:text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-colors">Tolak</button> 
                                </form> 
                            @endif 

                            @if ($withdrawal->status === 'approved') 
                                <form action="{{ route('admin.withdrawals.paid', $withdrawal) }}" method="POST" enctype="multipart/form-data" class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50 flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto"> 
                                    @csrf 
                                    @method('PATCH') 
                                    
                                    <div class="flex flex-col w-full sm:w-auto">
                                        <label class="text-[10px] font-black uppercase tracking-wider text-emerald-400 mb-1 ml-0.5">Unggah Bukti Transfer Manual</label>
                                        <input type="file" name="receipt" required class="text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20 cursor-pointer focus:outline-none border border-slate-700 rounded-xl p-1 bg-slate-950/40 w-full sm:w-64">
                                    </div>

                                    <button type="submit" class="bg-emerald-500 text-slate-900 hover:bg-emerald-400 w-full sm:w-auto px-5 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-colors shadow-lg self-end h-[38px] flex items-center justify-center">
                                        Tandai Paid
                                    </button> 
                                </form> 
                            @endif 

                            @if ($withdrawal->status === 'paid')
                                <div class="bg-emerald-500/5 border border-emerald-500/10 px-4 py-3 rounded-xl text-xs font-bold text-emerald-400/80 flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Pencairan Selesai Terproses
                                </div>
                            @endif

                        </div> 
                    </div> 
                </div> 
            @empty 
                <div class="rounded-2xl border border-dashed border-slate-700/60 bg-slate-800/10 px-4 py-12 text-center text-sm text-slate-500 font-medium uppercase tracking-wider"> 
                    Belum ada pengajuan pencairan dana dari vendor. 
                </div> 
            @endforelse 
        </div> 
    </div> 
</div> 
@endsection