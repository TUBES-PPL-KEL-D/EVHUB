@extends('layouts.app')

@section('title', 'Detail SPKLU - ' . $spklu->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-600 px-4 py-3 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-amber-50 border border-amber-200 text-amber-600 px-4 py-3 rounded-xl text-sm font-medium">
            <p class="font-bold mb-1">Gagal memproses transaksi:</p>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full uppercase tracking-wider">Stasiun Aktif</span>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $spklu->name }}</h1>
            </div>
            <div class="flex items-start gap-2 mt-3 text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="font-medium">{{ $spklu->address ?? 'Alamat tidak tersedia' }}</p>
            </div>
            @if($spklu->vendor && $spklu->vendor->profile)
                <div class="mt-4 flex flex-wrap gap-4 text-sm">
                    @if($spklu->vendor->profile->company_phone)
                    <div class="flex items-center gap-1.5 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        <span>{{ $spklu->vendor->profile->company_phone }}</span>
                    </div>
                    @endif
                    @if($spklu->vendor->profile->opens_at && $spklu->vendor->profile->closes_at)
                    <div class="flex items-center gap-1.5 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ \Carbon\Carbon::parse($spklu->vendor->profile->opens_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($spklu->vendor->profile->closes_at)->format('H:i') }} WIB</span>
                    </div>
                    @endif
                </div>
            @endif
        </div>
        <div class="flex flex-col gap-3 min-w-[200px]">
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $spklu->latitude }},{{ $spklu->longitude }}" target="_blank" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl flex items-center justify-center gap-2 transition-all shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                Petunjuk Arah
            </a>
            <a href="{{ route('rider.map') }}" class="w-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold py-3 px-6 rounded-xl flex items-center justify-center gap-2 transition-all shadow-sm text-center">
                Kembali ke Peta
            </a>
        </div>
    </div>

    <!-- Fasilitas Section -->
    <div>
        <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            Daftar Mesin Charger
        </h2>

        @if($spklu->chargerMachines->isEmpty())
            <div class="bg-white rounded-xl border border-slate-100 p-8 text-center text-slate-500 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                <p>Belum ada mesin charger yang terdaftar untuk SPKLU ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($spklu->chargerMachines as $machine)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow">
                        @if($machine->photo_path)
                            <div class="h-48 w-full bg-slate-100 relative">
                                <img src="{{ asset('storage/' . $machine->photo_path) }}" alt="{{ $machine->name }}" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="h-48 w-full bg-slate-50 flex items-center justify-center border-b border-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                        @endif
                        
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-bold text-slate-800 leading-tight">{{ $machine->name ?? 'Mesin Charger' }}</h3>
                                
                                @if(strtolower($machine->status) === 'available')
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-md text-xs font-bold uppercase tracking-wider border border-emerald-100 flex items-center gap-1.5 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Tersedia
                                    </span>
                                @elseif(strtolower($machine->status) === 'maintenance')
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-xs font-bold uppercase tracking-wider border border-slate-200 flex items-center gap-1.5 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Perbaikan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-rose-50 text-rose-600 rounded-md text-xs font-bold uppercase tracking-wider border border-rose-100 flex items-center gap-1.5 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Dipakai
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                    <span class="text-sm text-slate-500 font-medium">Tipe Konektor</span>
                                    <span class="text-sm font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded">{{ $machine->connector_type }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                    <span class="text-sm text-slate-500 font-medium">Kapasitas</span>
                                    <span class="text-sm font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $machine->capacity_kw }} kW</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                    <span class="text-sm text-slate-500 font-medium">Tarif</span>
                                    <span class="text-sm font-bold text-slate-800">Rp {{ number_format($machine->price_per_kwh, 0, ',', '.') }} <span class="text-xs text-slate-500 font-normal">/ kWh</span></span>
                                </div>

                                @php
                                    $queueCount = \App\Models\ChargingQueue::where('charger_machine_id', $machine->id)
                                        ->where('status', 'waiting')
                                        ->count();
                                @endphp

                                <p class="text-sm text-slate-400">
                                    Antrean saat ini: 
                                    <span class="font-semibold text-amber-400">{{ $queueCount }} pengendara</span>
                                </p>

                                <div class="pt-2">
                                    @if(strtolower($machine->status) === 'available')
                                        <a href="{{ route('rider.transactions.prepare', $machine->id) }}"
                                        class="block w-full text-center bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
                                            Pilih & Mulai Pengisian
                                        </a>
                                    @elseif(strtolower($machine->status) === 'unavailable')
                                        <form action="{{ route('rider.queues.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="charger_machine_id" value="{{ $machine->id }}">

                                            <button type="submit"
                                                class="block w-full text-center bg-amber-500 hover:bg-amber-400 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
                                                Masuk Antrean Digital
                                            </button>
                                        </form>
                                    @else
                                        <button disabled
                                            class="block w-full text-center bg-slate-700 text-slate-400 font-semibold py-2.5 rounded-xl text-sm cursor-not-allowed">
                                            Mesin Sedang Perbaikan
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Info Vendor Section -->
    @if($spklu->vendor && $spklu->vendor->profile && $spklu->vendor->profile->company_description)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 mt-8">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Tentang Vendor</h2>
        <div class="prose prose-slate max-w-none text-slate-600">
            {{ $spklu->vendor->profile->company_description }}
        </div>
    </div>
    @endif
</div>
@endsection
