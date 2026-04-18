@extends('layouts.app')
@section('title', 'Verifikasi Vendor')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    
    <div>
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Otorisasi <span class="text-emerald-500">Vendor</span></h1>
        <p class="text-slate-500 font-medium mt-2">Tinjau dan validasi pendaftaran mitra SPKLU baru untuk ekosistem EV-HUB.</p>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl flex items-center space-x-3 shadow-sm">
        <div class="bg-emerald-500 p-1.5 rounded-full text-white shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <p class="font-bold text-emerald-800 text-sm">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white/90 backdrop-blur-xl rounded-[2rem] shadow-[0_10px_40px_rgb(0,0,0,0.03)] border border-white overflow-hidden p-2">
        <div class="px-6 py-5 flex justify-between items-center">
            <h2 class="text-lg font-extrabold text-slate-800">Antrean Dokumen</h2>
            <div class="bg-orange-50 text-orange-600 px-4 py-1.5 rounded-full text-xs font-bold flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                <span>{{ $pendingVendors->count() }} Pending</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                        <th class="px-6 py-4">Informasi Mitra</th>
                        <th class="px-6 py-4 text-center">Legalitas</th>
                        <th class="px-6 py-4 text-center">Keputusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($pendingVendors as $vendor)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-5">
                            <div class="font-bold text-slate-900 text-base">{{ $vendor->company_name }}</div>
                            <div class="text-sm text-slate-500 mt-1 font-medium">{{ $vendor->user->email ?? 'Tidak ada email' }}</div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @if($vendor->legality_document_path)
                            <a href="{{ asset('storage/' . $vendor->legality_document_path) }}" target="_blank" class="inline-flex items-center space-x-2 text-slate-600 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-600 font-bold px-4 py-2.5 rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="text-xs tracking-wide">CEK PDF</span>
                            </a>
                            @else
                            <span class="text-slate-400 text-xs font-medium bg-slate-50 px-3 py-1.5 rounded-lg">Tanpa File</span>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center items-center space-x-3">
                                <form action="{{ route('admin.vendors.approve', $vendor->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl text-xs font-bold tracking-wide shadow-[0_4px_15px_rgba(16,185,129,0.2)] hover:shadow-[0_6px_20px_rgba(16,185,129,0.3)] hover:-translate-y-0.5 transition-all">
                                        TERIMA
                                    </button>
                                </form>
                                <form action="{{ route('admin.vendors.reject', $vendor->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="bg-white border-2 border-slate-200 text-slate-500 hover:border-rose-500 hover:text-rose-600 px-6 py-2.5 rounded-xl text-xs font-bold tracking-wide hover:-translate-y-0.5 transition-all">
                                        TOLAK
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-20 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-emerald-200" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            </div>
                            <p class="text-slate-800 font-extrabold text-lg">Semua Selesai!</p>
                            <p class="text-slate-500 font-medium mt-1">Tidak ada dokumen vendor yang perlu diverifikasi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection