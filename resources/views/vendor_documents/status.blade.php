@extends('layouts.app') 

@section('title', 'Status Pendaftaran Vendor') 

@section('content') 
@php 
    $statusStyles = [
        'Pending'   => 'bg-amber-500/10 text-amber-400 border border-amber-500/20', 
        'Approved'  => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20', 
        'Rejected'  => 'bg-rose-500/10 text-rose-400 border border-rose-500/20', 
        'Suspended' => 'bg-slate-700/20 text-slate-400 border border-slate-700/30', 
    ]; 
    $statusClass = $statusStyles[$vendor->status] ?? 'bg-slate-800 text-slate-400'; 
    $isApproved = $vendor->status === 'Approved'; 
    $isRejected = $vendor->status === 'Rejected'; 
@endphp 

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

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-2xl"> 
        <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-400">Verifikasi Kemitraan</p>
        <h1 class="mt-2 text-3xl font-black text-white tracking-tight">Status Pendaftaran Vendor</h1> 
        <p class="mt-1 text-sm text-slate-400">Pantau status validasi berkas legalitas perusahaan Anda secara real-time.</p> 
    </div> 

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl space-y-6"> 
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-2xl bg-slate-800/40 border border-slate-700/30 p-5"> 
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Nama Perusahaan</p> 
                <p class="mt-1 text-lg font-black text-white">{{ $vendor->company_name }}</p> 
            </div> 

            <div class="rounded-2xl bg-slate-800/40 border border-slate-700/30 p-5"> 
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Status Peninjauan Saat Ini</p> 
                <p class="mt-2 inline-flex rounded-full px-4 py-1 text-xs font-black tracking-widest uppercase {{ $statusClass }}">{{ $vendor->status }}</p> 
            </div> 
        </div>

        <div class="rounded-2xl border border-slate-700/50 bg-slate-950/40 p-6"> 
            @if ($isApproved) 
                <p class="text-base font-bold text-emerald-400">Selamat, pendaftaran berkas Anda disetujui!</p> 
                <p class="mt-1 text-sm text-slate-400">Konsol akreditasi operasional Anda telah aktif. Anda sekarang memiliki izin penuh untuk mendaftarkan stasiun charger baru di platform EV-HUB.</p> 
            @elseif ($isRejected) 
                <p class="text-base font-bold text-rose-400">Dokumen legalitas ditolak oleh Admin.</p> 
                <p class="mt-1 text-sm text-slate-400">Terdapat ketidaksesuaian berkas dalam proses audit. Silakan periksa kembali berkas Anda dan lakukan pengiriman ulang melalui tombol perbaikan di bawah.</p> 
            @else 
                <p class="text-base font-bold text-amber-400 font-semibold">Dokumen dalam antrean review Admin.</p> 
                <p class="mt-1 text-sm text-slate-400">Proses pengecekan berkas komparatif biasanya memakan waktu maksimal 1x24 jam. Halaman dasbor operasional akan terbuka otomatis begitu status Anda berubah menjadi Approved.</p> 
            @endif 
        </div> 

        <div class="flex flex-col gap-4 border-t border-slate-800 pt-6 sm:flex-row sm:items-center sm:justify-between"> 
            <p class="text-xs font-mono text-slate-500">Pembaruan Sistem Terakhir: {{ $vendor->updated_at->format('d M Y, H:i') }} WIB</p> 
            <div class="flex flex-wrap gap-2 w-full sm:w-auto justify-end"> 
                @if ($isApproved) 
                    <a href="{{ route('vendor.dashboard') }}" class="bg-emerald-500 text-slate-900 hover:bg-emerald-400 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-colors shadow-lg shadow-emerald-500/10 text-center w-full sm:w-auto">Buka Dashboard Vendor</a> 
                @endif 
                @if ($isRejected) 
                    <a href="{{ route('vendor.documents.edit', $vendor) }}" class="bg-rose-500 text-white hover:bg-rose-400 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-colors text-center w-full sm:w-auto">Perbaiki & Upload Ulang</a> 
                @endif 
                <a href="{{ route('vendor.documents.show', $vendor) }}" class="border border-slate-700 text-slate-300 hover:text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-colors text-center w-full sm:w-auto">Detail Dokumen</a> 
                <a href="{{ url('/') }}" class="bg-slate-800 text-slate-300 hover:text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-colors text-center w-full sm:w-auto">Kembali</a> 
            </div> 
        </div> 
    </div> 
</div> 
@endsection