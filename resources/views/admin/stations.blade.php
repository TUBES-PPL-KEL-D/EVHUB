@extends('layouts.app')
@section('title', 'Riwayat Stasiun')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    <div>
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Riwayat <span class="text-emerald-500">Stasiun</span></h1>
        <p class="text-slate-500 font-medium mt-2">Arsip pendaftaran vendor yang telah diputuskan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="bg-white/90 backdrop-blur-xl rounded-[2rem] shadow-[0_10px_40px_rgb(0,0,0,0.03)] border border-white p-6">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-lg font-extrabold text-slate-800">Telah Disetujui</h2>
            </div>
            
            <div class="space-y-4">
                @forelse($approvedVendors as $vendor)
                <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl flex justify-between items-center">
                    <div>
                        <div class="font-bold text-slate-900">{{ $vendor->company_name }}</div>
                        <div class="text-sm text-slate-500 font-medium">{{ $vendor->user->email ?? '-' }}</div>
                    </div>
                    <span class="bg-emerald-50 text-emerald-600 border border-emerald-100 px-3 py-1.5 rounded-lg text-xs font-bold tracking-wide">AKTIF</span>
                </div>
                @empty
                <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-400 font-medium">Belum ada data</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-[2rem] shadow-[0_10px_40px_rgb(0,0,0,0.03)] border border-white p-6">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <h2 class="text-lg font-extrabold text-slate-800">Ditolak</h2>
            </div>
            
            <div class="space-y-4">
                @forelse($rejectedVendors as $vendor)
                <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl flex justify-between items-center">
                    <div>
                        <div class="font-bold text-slate-900">{{ $vendor->company_name }}</div>
                        <div class="text-sm text-slate-500 font-medium">{{ $vendor->user->email ?? '-' }}</div>
                    </div>
                    <span class="bg-rose-50 text-rose-600 border border-rose-100 px-3 py-1.5 rounded-lg text-xs font-bold tracking-wide">DITOLAK</span>
                </div>
                @empty
                <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-400 font-medium">Belum ada data</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection