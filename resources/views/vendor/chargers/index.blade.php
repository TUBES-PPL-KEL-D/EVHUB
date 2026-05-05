@extends('layouts.app')

@section('title', 'Daftar Mesin Charger')

@section('content')
<div class="vendor-scope">
    <div class="mx-auto max-w-7xl">
        
        <!-- Header Section -->
        <div class="mb-6 flex flex-col justify-between sm:flex-row sm:items-center">
            <div>
                <h1 class="mt-2 text-3xl font-bold text-white drop-shadow-[0_2px_6px_rgba(0,0,0,0.45)]">Daftar Mesin Charger</h1>
                <p class="mt-2 text-slate-200 drop-shadow-[0_1px_3px_rgba(0,0,0,0.45)]">Kelola infrastruktur SPKLU dan mesin pengisian daya Anda.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('vendor.chargers.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-[#34CBDA] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Tambah Mesin Baru
                </a>
            </div>
        </div>

        <!-- Alert Success -->
        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Table Container -->
        <div class="overflow-hidden rounded-3xl bg-white shadow-md ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-800 border-b border-slate-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Foto</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Nama Mesin</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Lokasi SPKLU</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Spesifikasi</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($chargers as $charger)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <img src="{{ asset('storage/' . $charger->photo_path) }}" alt="Foto Mesin" class="h-16 w-24 rounded-lg object-cover border border-slate-200 shadow-sm">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $charger->name }}</div>
                                    <div class="text-xs text-slate-500 mt-1">Operasional: {{ $charger->operational_hours }}</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    {{ $charger->spklu->name ?? 'Tidak Terhubung' }}
                                </td>
                                <td class="px-6 py-4 space-y-1 text-slate-700">
                                    <!-- PBI 30: Menampilkan Data Referensi Tipe Port -->
                                    <div class="mb-2">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-sky-50 px-2 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                              <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                            </svg>
                                            PORT: {{ strtoupper($charger->connector_type) }}
                                        </span>
                                    </div>
                                    <div>Kapasitas: <span class="font-semibold">{{ $charger->capacity_kw }} kW</span></div>
                                    <div>Harga: <span class="font-semibold text-emerald-600">Rp {{ number_format($charger->price_per_kwh, 0, ',', '.') }}/kWh</span></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $charger->status == 'available' ? 'bg-emerald-100 text-emerald-800' : 
                                          ($charger->status == 'maintenance' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ strtoupper($charger->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('vendor.chargers.edit', $charger->id) }}" class="font-medium text-amber-600 hover:text-amber-800 transition">Edit</a>
                                        
                                        <form action="{{ route('vendor.chargers.destroy', $charger->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-red-600 hover:text-red-800 transition" onclick="return confirm('Apakah Anda yakin ingin menghapus mesin ini secara permanen?')">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-base font-medium text-slate-700">Belum ada mesin charger</p>
                                    <p class="mt-1">Anda belum mendaftarkan infrastruktur SPKLU apapun.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>
@endsection