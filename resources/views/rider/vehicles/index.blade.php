@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="space-y-8 animate-fade-in-up mb-8">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Garasi <span class="text-emerald-500">Digital</span></h1>
            <p class="text-slate-500 font-medium mt-2">Kelola kendaraan EV Anda untuk kemudahan pengisian daya di SPKLU.</p>
        </div>
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0"></div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('rider.vehicles.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                    + Tambah Kendaraan
                </a>
            </div>
        </div>
    </div>

    {{-- Card Grid --}}
    @if($vehicles->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM15 7l4 4v5h-4V7z"/>
            </svg>
            <p class="text-gray-500 text-sm">Belum ada kendaraan EV di garasi Anda.</p>
            <a href="{{ route('rider.vehicles.create') }}"
               class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-150">
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
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col transition-transform duration-200 hover:-translate-y-1 hover:shadow-md">

                    {{-- Car Illustration --}}
                    <div class="h-44 flex items-center justify-center bg-gray-50 p-4">
                        @if($imageSrc)
                            <img src="{{ $imageSrc }}"
                                 alt="{{ $vehicle->merk }}"
                                 class="h-full w-full object-contain">
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-300">
                                <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM15 7l4 4v5h-4V7z"/>
                                </svg>
                                <span class="text-xs mt-1">No Image</span>
                            </div>
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="px-5 py-4 flex flex-col flex-1">

                        {{-- Brand & Model --}}
                        <div class="mb-3">
                            <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $vehicle->merk }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $vehicle->model }}</p>
                        </div>

                        {{-- License Plate Badge --}}
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold tracking-widest bg-green-100 text-green-800 border border-green-200">
                                {{ strtoupper($vehicle->license_plate) }}
                            </span>
                        </div>

                        {{-- Spacer pushes buttons to bottom --}}
                        <div class="flex-1"></div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2 pt-3 border-t border-gray-100">
                            <a href="{{ route('rider.vehicles.edit', $vehicle->id) }}"
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>

                            <form action="{{ route('rider.vehicles.destroy', $vehicle->id) }}" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus kendaraan ini?');"
                                  class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-3 py-2 rounded-lg text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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