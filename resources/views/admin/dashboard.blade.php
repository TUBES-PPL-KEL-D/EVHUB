@extends('layouts.app') 

@section('content') 
<div class="space-y-6 animate-fade-in-up pb-10">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-2">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter">Pusat Kendali Admin</h1>
            <p class="text-slate-400 font-medium mt-1">Satu ruang terintegrasi untuk analitik, verifikasi berkas, dan manajemen operasional.</p>
        </div>
        <div class="bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-full flex items-center">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2"></span>
            <span class="text-emerald-400 text-xs font-bold tracking-widest uppercase">Sistem Operasional</span>
        </div>
    </div>

    @if(session('success')) 
    <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl flex items-center space-x-3 shadow-sm"> 
        <div class="bg-emerald-500 p-1.5 rounded-full text-white"> 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> 
        </div> 
        <p class="font-bold text-emerald-400 text-sm">{{ session('success') }}</p> 
    </div> 
    @endif 

    <div class="flex border-b border-slate-700/60 gap-2 mb-6 overflow-x-auto">
        <button id="tabBtn-overview" onclick="switchMainTab('overview')" class="main-tab-btn px-6 py-3.5 font-black text-xs tracking-widest uppercase border-b-2 transition-all duration-200 text-emerald-400 border-emerald-500 shrink-0">
            Overview & Analytics
        </button>
        <button id="tabBtn-verifikasi" onclick="switchMainTab('verifikasi')" class="main-tab-btn px-6 py-3.5 font-black text-xs tracking-widest uppercase border-b-2 transition-all duration-200 text-slate-500 border-transparent hover:text-slate-300 shrink-0">
            Antrean Verifikasi ({{ count($pendingVendors ?? []) }})
        </button>
        <button id="tabBtn-manajemen" onclick="switchMainTab('manajemen')" class="main-tab-btn px-6 py-3.5 font-black text-xs tracking-widest uppercase border-b-2 transition-all duration-200 text-slate-500 border-transparent hover:text-slate-300 shrink-0">
            Manajemen Vendor & Stasiun
        </button>
    </div>

    @include('admin.partials.tab-overview')
    @include('admin.partials.tab-verifikasi')
    @include('admin.partials.tab-manajemen')

</div>

@include('admin.partials.modals')
@include('admin.partials.scripts')

@endsection