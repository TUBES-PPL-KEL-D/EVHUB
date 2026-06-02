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

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-400">Infrastruktur Vendor</p>
            <h1 class="mt-2 text-3xl font-black text-white tracking-tight">Daftar Mesin Charger</h1>
            <p class="mt-1 text-sm text-slate-400">Kelola infrastruktur SPKLU, spesifikasi mesin pengisian daya, dan penyesuaian tarif aktif.</p>
        </div>
        <div class="flex flex-wrap gap-2 w-full md:w-auto">
            <a href="{{ route('vendor.chargers.usageHistory') }}" class="flex-grow md:flex-grow-0 text-center bg-slate-800 text-slate-200 border border-slate-700 rounded-xl px-5 py-3 text-xs font-black uppercase tracking-widest hover:bg-slate-700 transition-colors">
                Riwayat Pemakaian
            </a>
            <a href="{{ route('vendor.chargers.create') }}" class="flex-grow md:flex-grow-0 text-center bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 text-white rounded-xl px-5 py-3 text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-cyan-500/20 flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Mesin Baru
            </a>
        </div>
    </div>

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <th class="pb-4 pl-2 w-28">Foto Asset</th>
                        <th class="pb-4">Identitas Mesin</th>
                        <th class="pb-4">Lokasi SPKLU</th>
                        <th class="pb-4 w-80">Spesifikasi & Manajemen Tarif</th>
                        <th class="pb-4 text-center">Status</th>
                        <th class="pb-4 text-right pr-2">Aksi Modifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse ($chargers as $charger)
                        <tr class="text-sm text-slate-300 hover:bg-slate-800/20 transition-colors group">
                            <td class="py-5 pl-2 vertical-align-middle">
                                <div class="w-20 h-20 rounded-2xl border border-slate-700 bg-slate-950 overflow-hidden shadow-md group-hover:border-slate-600 transition-colors">
                                    @if($charger->photo_path)
                                        <img src="{{ asset('storage/' . $charger->photo_path) }}" alt="Foto Mesin" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-900 text-slate-600">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="py-5 pr-4 vertical-align-middle">
                                <p class="text-base font-bold text-white tracking-tight">{{ $charger->name }}</p>
                                <div class="flex items-center space-x-1.5 mt-1.5 text-xs text-slate-400 font-medium">
                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Operasional: {{ $charger->operational_hours ?? '24 Jam' }}</span>
                                </div>
                            </td>

                            <td class="py-5 pr-4 vertical-align-middle">
                                <span class="bg-slate-800/60 border border-slate-700 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-200 tracking-wide uppercase inline-block">
                                    {{ $charger->spklu->name ?? 'Stasiun Utama' }}
                                </span>
                            </td>

                            <td class="py-5 pr-4 vertical-align-middle">
                                <div class="space-y-3">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="inline-flex rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 px-2 py-0.5 text-[10px] font-black tracking-wider uppercase">⚡ PORT: {{ $charger->connector_type }}</span>
                                        <span class="inline-flex rounded-lg bg-purple-500/10 text-purple-400 border border-purple-500/20 px-2 py-0.5 text-[10px] font-black tracking-wider">{{ $charger->capacity_kw }} kW</span>
                                    </div>
                                    
                                    <form action="{{ route('vendor.chargers.updateTariff', $charger) }}" method="POST" class="bg-slate-950/50 border border-slate-800 p-2.5 rounded-2xl flex items-center gap-2 max-w-sm">
                                        @csrf
                                        @method('PATCH')
                                        <div class="relative flex-grow">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-500">Rp</span>
                                            <input type="number" name="price_per_kwh" value="{{ (int)$charger->price_per_kwh }}" min="0" required class="w-full pl-8 pr-2 py-1.5 bg-slate-900 border border-slate-700 rounded-xl text-xs font-bold text-white focus:outline-none focus:border-emerald-500 transition-colors">
                                        </div>
                                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-colors shrink-0 shadow-md">
                                            Simpan
                                        </button>
                                    </form>
                                    <p class="text-[10px] text-slate-500 font-medium pl-1">Tarif Saat Ini: <span class="text-emerald-400 font-bold">Rp{{ number_format($charger->price_per_kwh, 0, ',', '.') }}/kWh</span></p>
                                </div>
                            </td>

                            <td class="py-5 text-center vertical-align-middle">
                                @php
                                    $statusStyle = match(strtolower($charger->status ?? '')) {
                                        'available'   => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                        'maintenance' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                                        default        => 'bg-rose-500/10 text-rose-400 border border-rose-500/20'
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-3 py-1 text-[10px] font-black tracking-widest uppercase {{ $statusStyle }}">
                                    {{ $charger->status }}
                                </span>
                            </td>

                            <td class="py-5 text-right pr-2 vertical-align-middle">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('vendor.spklu.gallery.index', $charger->spklu_id) }}" class="p-2 bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition-colors border border-slate-700/60" title="Kelola Galeri">
                                        <span class="text-xs font-bold px-1">Galeri</span>
                                    </a>
                                    <a href="{{ route('vendor.chargers.edit', $charger) }}" class="p-2 bg-slate-800/80 hover:bg-slate-700 text-amber-400 hover:text-amber-300 rounded-lg transition-colors border border-slate-700/60" title="Edit Detail">
                                        <span class="text-xs font-bold px-1">Edit</span>
                                    </a>
                                    <form action="{{ route('vendor.chargers.destroy', $charger) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus infrastruktur mesin charger dan penanda lokasi SPKLU ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white rounded-lg transition-all border border-rose-500/20" title="Hapus Permanen">
                                            <span class="text-xs font-bold px-1">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs font-bold uppercase tracking-wider text-slate-500 border-dashed border border-slate-800/60 rounded-3xl">
                                Belum ada mesin charger yang didaftarkan. Klik tombol kanan atas untuk meletakkan SPKLU pada peta.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .vertical-align-middle {
        vertical-align: middle;
    }
</style>
@endsection