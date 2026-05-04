@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Dashboard Admin</h1>
    <p class="text-slate-400 mt-2">Manajemen dan Verifikasi Ekosistem SPKLU.</p>
</div>

<!-- Card Glassmorphism -->
<div class="bg-slate-800/40 border border-slate-700/50 rounded-3xl p-6 md:p-8 backdrop-blur-sm shadow-xl">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white flex items-center">
            <span class="w-3 h-3 rounded-full bg-amber-500 animate-pulse mr-3"></span>
            Antrean Dokumen Vendor
        </h2>
    </div>

    @php
        // Sistem fallback cerdas: otomatis membaca variabel apa pun yang dikirim dari controller
        $dataAntrean = $vendors ?? $pendingVendors ?? $pending_vendors ?? $data ?? collect();
    @endphp

    @if(count($dataAntrean) == 0)
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-16 h-16 bg-emerald-500/10 text-emerald-400 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Semua Selesai!</h3>
            <p class="text-slate-400">Tidak ada vendor yang menunggu verifikasi saat ini.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-700/50 text-slate-400 text-sm uppercase tracking-wider">
                        <th class="py-4 px-4 font-semibold">Nama Perusahaan</th>
                        <th class="py-4 px-4 font-semibold">NPWP</th>
                        <th class="py-4 px-4 font-semibold">Alamat</th>
                        <th class="py-4 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @foreach($dataAntrean as $vendor)
                    <tr class="hover:bg-slate-700/20 transition-colors">
                        <td class="py-4 px-4 text-white font-medium">{{ $vendor->company_name ?? '-' }}</td>
                        <td class="py-4 px-4 text-slate-300 font-mono text-sm">{{ $vendor->npwp ?? '-' }}</td>
                        <td class="py-4 px-4 text-slate-400 text-sm max-w-xs truncate">{{ $vendor->address ?? '-' }}</td>
                        <td class="py-4 px-4 text-right space-x-2">
                            <!-- Tombol Terima -->
                            <form action="{{ route('admin.vendors.approve', $vendor->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-white px-4 py-2 rounded-lg text-sm font-bold transition-all border border-emerald-500/20">
                                    TERIMA
                                </button>
                            </form>
                            <!-- Tombol Tolak -->
                            <form action="{{ route('admin.vendors.reject', $vendor->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white px-4 py-2 rounded-lg text-sm font-bold transition-all border border-rose-500/20">
                                    TOLAK
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection