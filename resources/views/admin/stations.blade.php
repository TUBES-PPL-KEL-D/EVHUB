@extends('layouts.app')
@section('title', 'Manajemen Stasiun & Vendor')

@section('content')
<div class="space-y-10 animate-fade-in-up">
    <div>
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Manajemen <span class="text-emerald-500">Stasiun</span></h1>
        <p class="text-slate-500 font-medium mt-2">Kontrol operasional dan status kemitraan vendor SPKLU.</p>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl flex items-center space-x-3 shadow-sm">
        <div class="bg-emerald-500 p-1.5 rounded-full text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <p class="font-bold text-emerald-800 text-sm">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-10">
        
        <div class="bg-white/90 backdrop-blur-xl rounded-[2.5rem] shadow-[0_10px_40px_rgb(0,0,0,0.03)] border border-white p-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Vendor Aktif</h2>
                </div>
            </div>
            
            <div class="space-y-4">
                @forelse($approvedVendors as $vendor)
                <div class="bg-slate-50/50 hover:bg-white transition-all border border-slate-100 p-5 rounded-3xl flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center font-black text-emerald-600 text-lg">
                            {{ substr($vendor->company_name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-extrabold text-slate-900 text-lg">{{ $vendor->company_name }}</div>
                            <div class="text-xs text-slate-500 font-bold tracking-wide uppercase">{{ $vendor->user->email }}</div>
                        </div>
                    </div>
                    <form action="{{ route('admin.vendors.suspend', $vendor->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="bg-slate-900 text-white px-6 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-rose-600 transition-colors shadow-lg" onclick="return confirm('Apakah Anda yakin ingin membekukan vendor ini?')">SUSPEND_ACC</button>
                    </form>
                </div>
                @empty
                <div class="text-center py-10 bg-slate-50 rounded-3xl border border-dashed border-slate-200 text-slate-400 font-bold uppercase tracking-widest text-xs">Belum ada vendor aktif</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-[2.5rem] shadow-[0_10px_40px_rgb(0,0,0,0.03)] border border-white p-8">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Daftar Suspend</h2>
            </div>
            
            <div class="space-y-4">
                @forelse($suspendedVendors as $vendor)
                <div class="bg-rose-50/30 border border-rose-100 p-5 rounded-3xl flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-rose-500 text-white rounded-2xl flex items-center justify-center font-black text-lg">
                            !
                        </div>
                        <div>
                            <div class="font-extrabold text-slate-900 text-lg italic">{{ $vendor->company_name }}</div>
                            <div class="text-xs text-rose-600 font-black tracking-widest uppercase">Akun Ditangguhkan</div>
                        </div>
                    </div>
                    <form action="{{ route('admin.vendors.activate', $vendor->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="bg-emerald-500 text-white px-6 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-500/20">RE_ACTIVATE</button>
                    </form>
                </div>
                @empty
                <div class="text-center py-10 bg-slate-50 rounded-3xl border border-dashed border-slate-200 text-slate-400 font-bold uppercase tracking-widest text-xs">Tidak ada akun yang dibekukan</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection