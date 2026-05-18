@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Dashboard Admin</h1>
    <p class="text-slate-400 mt-2">Manajemen dan Verifikasi Ekosistem SPKLU serta Laporan Kendala.</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

    <div class="bg-slate-800/40 border border-slate-700/50 rounded-3xl p-6 md:p-8 backdrop-blur-sm shadow-xl flex flex-col h-full">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white flex items-center">
                <span class="w-3 h-3 rounded-full bg-amber-500 animate-pulse mr-3"></span>
                Antrean Dokumen Vendor
            </h2>
        </div>

        @php
            $dataAntrean = $vendors ?? $pendingVendors ?? $pending_vendors ?? $data ?? collect();
        @endphp

        @if(count($dataAntrean) == 0)
            <div class="flex flex-col items-center justify-center py-16 text-center flex-grow">
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
                            <th class="py-4 px-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @foreach($dataAntrean as $vendor)
                        <tr class="hover:bg-slate-700/20 transition-colors">
                            <td class="py-4 px-4 text-white font-medium">{{ $vendor->company_name ?? '-' }}</td>
                            <td class="py-4 px-4 text-slate-300 font-mono text-sm">{{ $vendor->npwp ?? '-' }}</td>
                            <td class="py-4 px-4 text-right space-x-2">
                                <form action="{{ route('admin.vendors.approve', $vendor->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-white px-3 py-2 rounded-lg text-xs font-bold transition-all border border-emerald-500/20">
                                        TERIMA
                                    </button>
                                </form>
                                <form action="{{ route('admin.vendors.reject', $vendor->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white px-3 py-2 rounded-lg text-xs font-bold transition-all border border-rose-500/20">
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

    <div class="bg-slate-800/40 border border-slate-700/50 rounded-3xl p-6 md:p-8 backdrop-blur-sm shadow-xl flex flex-col h-full">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white flex items-center">
                <span class="w-3 h-3 rounded-full bg-rose-500 animate-pulse mr-3"></span>
                Tiket Laporan Kendala
            </h2>
        </div>

        @if(!isset($recentTickets) || count($recentTickets) == 0)
            <div class="flex flex-col items-center justify-center py-16 text-center flex-grow">
                <div class="w-16 h-16 bg-blue-500/10 text-blue-400 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Aman Terkendali!</h3>
                <p class="text-slate-400">Tidak ada laporan kendala dari pengendara saat ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-700/50 text-slate-400 text-sm uppercase tracking-wider">
                            <th class="py-4 px-4 font-semibold">Pelapor</th>
                            <th class="py-4 px-4 font-semibold">Subjek</th>
                            <th class="py-4 px-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @foreach($recentTickets as $ticket)
                        <tr class="hover:bg-slate-700/20 transition-colors">
                            <td class="py-4 px-4 text-white font-medium">{{ $ticket->user->name ?? 'Pengguna' }}</td>
                            <td class="py-4 px-4 text-slate-300 text-sm max-w-[150px] truncate">{{ $ticket->subject ?? '-' }}</td>
                            <td class="py-4 px-4 text-right">
                                <a href="#" class="inline-block bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white px-4 py-2 rounded-lg text-xs font-bold transition-all border border-blue-500/20">
                                    TINJAU
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection