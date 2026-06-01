@extends('layouts.app')

@section('content')
<style>
    .vehicle-img-container {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 2px solid rgba(16, 185, 129, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        transition: all 0.3s ease;
        box-shadow: 0 8px 16px rgba(0,0,0,0.4), inset 0 1px 0 rgba(16,185,129,0.1);
        overflow: hidden;
    }
    .group:hover .vehicle-img-container {
        border-color: rgba(16, 185, 129, 0.6);
        box-shadow: 0 12px 24px rgba(16,185,129,0.15), inset 0 1px 0 rgba(16,185,129,0.2);
    }
    .vehicle-img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.3s ease;
        border-radius: 50%;
    }
    .group:hover .vehicle-img {
        transform: scale(1.08);
    }
</style>
<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-500/50 bg-emerald-500/10 p-4 backdrop-blur-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-400">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="space-y-8 animate-fade-in-up mb-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight">Garasi <span class="text-emerald-500">Digital</span></h1>
                <p class="text-slate-400 font-medium mt-2">Kelola kendaraan EV Anda untuk kemudahan pengisian daya di SPKLU.</p>
            </div>
            <div>
                <a href="{{ route('rider.vehicles.create') }}"
                   class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl shadow-lg shadow-blue-600/30 text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 hover:-translate-y-0.5">
                    + Tambah Kendaraan
                </a>
            </div>
        </div>

        @if(isset($serviceDueVehicles) && $serviceDueVehicles->isNotEmpty())
            <div class="rounded-3xl border border-rose-500/30 bg-rose-500/10 p-5 text-white shadow-lg shadow-rose-500/10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">Pengingat Servis Baterai</h2>
                        <p class="text-sm text-slate-200 mt-1">Ada {{ $serviceDueVehicles->count() }} kendaraan yang perlu pemeriksaan servis baterai segera.</p>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm text-rose-100">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
                        Periksa jadwal servis baterai Anda
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Card Grid --}}
    @if($vehicles->isEmpty())
        <div class="bg-slate-800/40 border border-slate-700/50 rounded-[2rem] flex flex-col items-center justify-center py-20 text-center backdrop-blur-sm">
            <svg class="w-16 h-16 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM15 7l4 4v5h-4V7z"/>
            </svg>
            <h3 class="text-xl font-bold text-white mb-2">Garasi Masih Kosong</h3>
            <p class="text-slate-400 text-sm mb-6">Belum ada kendaraan EV di garasi Anda.</p>
            <a href="{{ route('rider.vehicles.create') }}"
                class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-500 transition-colors duration-150 shadow-lg shadow-blue-600/30">
                + Tambah Kendaraan Pertama
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($vehicles as $vehicle)
                @php
                    // Map merek ke nama file gambar
                    $brandImageMap = [
                        'bmw'      => 'bmw.png',
                        'byd'      => 'byd.png',
                        'chery'    => 'chery.png',
                        'denza'    => 'denza.png',
                        'gac aion' => 'gac_aion.png',
                        'geely'    => 'geely.png',
                        'gwm'      => 'gwm.png',
                        'hyundai'  => 'hyundai.png',
                        'jaecoo'   => 'jaecoo.png',
                        'kia'      => 'kia.png',
                        'mg'       => 'mg.png',
                        'neta'     => 'neta.png',
                        'toyota'   => 'toyota.png',
                        'volvo'    => 'volvo.png',
                        'wuling'   => 'wuling.png',
                        'xpeng'    => 'xpeng.png',
                    ];
                    $brandKey  = strtolower(trim($vehicle->merk));
                    $imageName = $brandImageMap[$brandKey] ?? null;
                    $imageSrc  = $imageName ? asset('images/cars/' . $imageName) : null;
                    $photoSrc  = $vehicle->photo_url;
                @endphp

                <!-- Glassmorphism Card -->
                <div class="bg-slate-800/40 rounded-3xl shadow-xl border border-slate-700/50 overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-2 hover:border-emerald-500/30 backdrop-blur-md group">

                    {{-- Car Illustration --}}
                    <div class="h-44 flex items-center justify-center bg-slate-900/60 p-6 border-b border-slate-700/50 group-hover:bg-slate-900/80 transition-colors">
                        @if($photoSrc)
                            <div class="vehicle-img-container">
                                <img src="{{ $photoSrc }}"
                                     alt="Foto {{ $vehicle->merk }}"
                                     class="vehicle-img object-cover">
                            </div>
                        @elseif($imageSrc)
                            <div class="vehicle-img-container">
                                <img src="{{ $imageSrc }}"
                                     alt="{{ $vehicle->merk }}"
                                     class="vehicle-img">
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center text-slate-600">
                                <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM15 7l4 4v5h-4V7z"/>
                                </svg>
                                <span class="text-xs font-semibold uppercase tracking-wider">No Image</span>
                            </div>
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="px-6 py-5 flex flex-col flex-1">

                        {{-- Brand & Model --}}
                        <div class="mb-4">
                            <h3 class="text-xl font-extrabold text-white leading-tight">{{ $vehicle->merk }}</h3>
                            <p class="text-sm font-medium text-slate-400 mt-1">{{ $vehicle->model }}</p>
                        </div>

                        {{-- License Plate Badge --}}
                        <div class="mb-6">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.1)]">
                                {{ strtoupper($vehicle->license_plate) }}
                            </span>
                        </div>

                        @if($vehicle->battery_percentage !== null && $vehicle->estimated_full_range_km !== null)
                            <div class="mb-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-emerald-100">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-xs uppercase tracking-wider font-semibold text-emerald-200">Perkiraan Jarak Tersisa</p>
                                        <p class="text-2xl font-bold text-white">{{ $vehicle->remaining_range }} km</p>
                                    </div>
                                    <div class="text-right text-sm text-slate-200">
                                        <p>{{ $vehicle->battery_percentage }}% baterai</p>
                                        <p class="mt-1">Jarak penuh: {{ $vehicle->estimated_full_range_km }} km</p>
                                    </div>
                                </div>
                                <div class="mt-4 h-2 rounded-full bg-slate-700 overflow-hidden">
                                    <div class="h-full rounded-full bg-emerald-400" style="width: {{ max(0, min(100, $vehicle->battery_percentage)) }}%;"></div>
                                </div>
                            </div>
                        @elseif($vehicle->battery_percentage !== null)
                            <div class="mb-6 rounded-2xl border border-slate-700/50 bg-slate-800/70 p-4 text-slate-300">
                                <p class="text-sm">Persentase baterai terdaftar: <strong>{{ $vehicle->battery_percentage }}%</strong>.</p>
                                <p class="text-sm mt-1">Tambahkan perkiraan jarak penuh kendaraan di menu edit untuk menghitung sisa jarak.</p>
                            </div>
                        @else
                            <div class="mb-6 rounded-2xl border border-slate-700/50 bg-slate-800/70 p-4 text-slate-300">
                                <p class="text-sm">Tambahkan persentase baterai dan perkiraan jarak penuh kendaraan di menu edit untuk melihat kalkulasi jarak tempuh.</p>
                            </div>
                        @endif

                        @if($vehicle->isBatteryServiceDue())
                            <div class="mb-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 p-4 text-rose-100">
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        <p class="text-xs uppercase tracking-wider font-bold text-rose-200">Pengingat Servis Baterai</p>
                                        <p class="text-sm mt-1">Jadwal servis: <strong>{{ $vehicle->battery_service_date->format('d M Y') }}</strong></p>
                                    </div>
                                    <span class="inline-flex items-center rounded-full bg-rose-500/20 px-3 py-1 text-[11px] font-semibold uppercase text-rose-100">{{ $vehicle->batteryServiceStatus() }}</span>
                                </div>
                            </div>
                        @elseif(! $vehicle->battery_service_date)
                            <div class="mb-6 rounded-2xl border border-slate-700/50 bg-slate-800/70 p-4 text-slate-300">
                                <p class="text-sm">Jadwal servis baterai belum diatur. Tambahkan tanggal servis di edit kendaraan.</p>
                            </div>
                        @endif

                        {{-- Spacer pushes buttons to bottom --}}
                        <div class="flex-1"></div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3 pt-4 border-t border-slate-700/50">
                            <a href="{{ route('rider.vehicles.edit', $vehicle->id) }}"
                               class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-blue-400 bg-blue-500/10 border border-blue-500/20 hover:bg-blue-500 hover:text-white transition-colors duration-200">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>

                            <form action="{{ route('rider.vehicles.destroy', $vehicle->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus kendaraan {{ $vehicle->merk }} dari garasi?');"
                                  class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-rose-400 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500 hover:text-white transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection