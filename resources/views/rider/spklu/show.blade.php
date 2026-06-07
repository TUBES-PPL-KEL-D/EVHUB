@extends('layouts.app')
@section('title', 'Detail SPKLU - ' . $spklu->name)

@section('content')
<div class="space-y-6 animate-fade-in-up pb-10 max-w-7xl mx-auto px-4">

    @if(session('error'))
    <div class="bg-rose-500/10 border border-rose-500/20 p-4 rounded-2xl flex items-center space-x-3 shadow-sm">
        <p class="font-bold text-rose-400 text-sm">{{ session('error') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-amber-500/10 border border-amber-500/20 p-4 rounded-2xl flex flex-col space-y-2 shadow-sm">
        <p class="font-bold text-amber-400 text-sm">Gagal memproses ulasan:</p>
        <ul class="list-disc pl-5 text-xs text-amber-400/80">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-black rounded-full uppercase tracking-wider">Stasiun Aktif</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-2">{{ $spklu->name }}</h1>

            @if($spklu->reviews->count() > 0)
            <div class="flex items-center gap-2 mb-4">
                <div class="flex items-center bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-lg">
                    <svg class="w-4 h-4 text-amber-400 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <span class="text-sm font-black text-amber-400">{{ number_format($spklu->reviews->avg('rating'), 1) }}</span>
                </div>
                <span class="text-xs font-medium text-slate-400">({{ $spklu->reviews->count() }} Ulasan Pengendara)</span>
            </div>
            @endif

            <div class="flex items-start gap-2 text-slate-400">
                <svg class="h-5 w-5 mt-0.5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <p class="font-medium text-sm leading-relaxed">{{ $spklu->address ?? 'Alamat tidak tersedia' }}</p>
            </div>

            @if($spklu->vendor && $spklu->vendor->profile)
            <div class="mt-4 flex flex-wrap gap-4 text-xs font-bold tracking-wide">
                @if($spklu->vendor->profile->company_phone)
                <div class="flex items-center gap-1.5 text-slate-300 bg-slate-800/50 px-3 py-1.5 rounded-lg border border-slate-700/50">
                    <svg class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    <span>{{ $spklu->vendor->profile->company_phone }}</span>
                </div>
                @endif
                @if($spklu->vendor->profile->opens_at && $spklu->vendor->profile->closes_at)
                <div class="flex items-center gap-1.5 text-slate-300 bg-slate-800/50 px-3 py-1.5 rounded-lg border border-slate-700/50">
                    <svg class="h-4 w-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ \Carbon\Carbon::parse($spklu->vendor->profile->opens_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($spklu->vendor->profile->closes_at)->format('H:i') }} WIB</span>
                </div>
                @endif
            </div>
            @endif
        </div>

        <div class="flex flex-col gap-3 w-full md:w-auto shrink-0">
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $spklu->latitude }},{{ $spklu->longitude }}" target="_blank" class="w-full md:w-48 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500 text-white text-xs font-black uppercase tracking-widest py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-blue-500/20 text-center">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                Navigasi Map
            </a>
            <a href="{{ route('rider.map') }}" class="w-full md:w-48 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-black uppercase tracking-widest py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 transition-all text-center">
                Kembali ke Peta
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

        <div class="xl:col-span-2 space-y-6">

            <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
                <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    Ketersediaan Mesin
                </h2>

                @if($spklu->chargerMachines->isEmpty())
                <div class="border border-dashed border-slate-700/60 rounded-2xl p-8 text-center">
                    <p class="text-slate-500 font-medium text-xs uppercase tracking-wider">Belum ada mesin charger yang terdaftar.</p>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($spklu->chargerMachines as $machine)
                    <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl overflow-hidden hover:border-slate-600 transition-colors flex flex-col">
                        @if($machine->photo_path)
                        <div class="h-40 w-full relative">
                            <img src="{{ asset('storage/' . $machine->photo_path) }}" alt="{{ $machine->name }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 to-transparent"></div>
                        </div>
                        @else
                        <div class="h-40 w-full bg-slate-800/80 flex items-center justify-center border-b border-slate-700/50 relative">
                            <svg class="h-12 w-12 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 to-transparent"></div>
                        </div>
                        @endif

                        <div class="p-5 flex-grow flex flex-col -mt-12 z-10">
                            <div class="flex justify-between items-end mb-4">
                                <h3 class="text-lg font-black text-white leading-tight drop-shadow-md">{{ $machine->name ?? 'Mesin Charger' }}</h3>
                                @if(strtolower($machine->status) === 'available')
                                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 shadow-sm backdrop-blur-md">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tersedia
                                </span>
                                @elseif(strtolower($machine->status) === 'maintenance')
                                <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 shadow-sm backdrop-blur-md">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Perbaikan
                                </span>
                                @else
                                <span class="bg-rose-500/10 text-rose-400 border border-rose-500/20 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 shadow-sm backdrop-blur-md">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Dipakai
                                </span>
                                @endif
                            </div>

                            <div class="space-y-2 mb-4 mt-2">
                                <div class="flex justify-between items-center bg-slate-900/50 p-2.5 rounded-xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Konektor</span>
                                    <span class="text-xs font-black text-white">{{ $machine->connector_type }}</span>
                                </div>
                                <div class="flex justify-between items-center bg-slate-900/50 p-2.5 rounded-xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Kapasitas</span>
                                    <span class="text-xs font-black text-blue-400">{{ $machine->capacity_kw }} kW</span>
                                </div>
                                <div class="flex justify-between items-center bg-slate-900/50 p-2.5 rounded-xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Tarif Dasar</span>
                                    <span class="text-xs font-black text-emerald-400">Rp{{ number_format($machine->price_per_kwh, 0, ',', '.') }}<span class="text-slate-500">/kWh</span></span>
                                </div>
                            </div>

                            @php
                                $queueCount = \App\Models\ChargingQueue::where('charger_machine_id', $machine->id)
                                    ->where('status', 'waiting')
                                    ->count();
                            @endphp

                            @if(strtolower($machine->status) === 'available')
                                <a href="{{ route('rider.transactions.prepare', $machine->id) }}"
                                class="block w-full rounded-2xl bg-emerald-500 px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-emerald-400">
                                    Mulai Mengisi
                                </a>
                            @elseif(strtolower($machine->status) === 'unavailable' || strtolower($machine->status) === 'dipakai')
                                <form action="{{ route('rider.queues.store') }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="charger_machine_id" value="{{ $machine->id }}">

                                    <button type="submit"
                                            class="block w-full rounded-2xl bg-amber-500 px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-amber-400">
                                        Masuk Antrean Digital
                                        @if($queueCount > 0)
                                            <span class="ml-1">({{ $queueCount }} antrean)</span>
                                        @endif
                                    </button>
                                </form>
                            @else
                                <button type="button"
                                        disabled
                                        class="block w-full cursor-not-allowed rounded-2xl bg-slate-700 px-4 py-3 text-center text-sm font-bold text-slate-400">
                                    Sedang Maintenance
                                </button>
                            @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
                <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M8 20h8a2 2 0 002-2V6a2 2 0 00-2-2H8a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Potret Lokasi SPKLU
                </h2>

                @if($spklu->galleryPhotos->isEmpty())
                <div class="border border-dashed border-slate-700/60 rounded-2xl p-8 text-center">
                    <p class="text-slate-500 font-medium text-xs uppercase tracking-wider">Belum ada foto lokasi tersedia.</p>
                </div>
                @else
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($spklu->galleryPhotos as $photo)
                    <div class="group relative rounded-2xl border border-slate-700 bg-slate-950 overflow-hidden shadow-md aspect-square">
                        <img src="{{ asset('storage/' . $photo->image_path) }}" alt="Foto Stasiun" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <a href="{{ asset('storage/' . $photo->image_path) }}" target="_blank" class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                            <span class="bg-slate-900/80 text-white px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider">Lihat</span>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>

        <div class="xl:col-span-1 space-y-6">

            @if($spklu->vendor && $spklu->vendor->profile && $spklu->vendor->profile->company_description)
            <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl">
                <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Informasi Mitra</h2>
                <div class="prose prose-sm prose-invert max-w-none text-slate-300">
                    {{ $spklu->vendor->profile->company_description }}
                </div>
            </div>
            @endif

            <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-white">Ulasan Pengguna</h2>
                    @if(Auth::check())
                    <button onclick="document.getElementById('review-form').classList.toggle('hidden')" class="bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-white border border-emerald-500/20 text-xs font-black uppercase tracking-widest py-2 px-3 rounded-xl transition-colors">
                        + Beri Ulasan
                    </button>
                    @endif
                </div>

                @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl mb-6 flex items-center gap-2">
                    <span class="text-emerald-400 text-xs font-bold">{{ session('success') }}</span>
                </div>
                @endif

                @if(Auth::check())
                <div id="review-form" class="hidden bg-slate-800/50 p-5 rounded-2xl border border-slate-700/50 mb-6 transition-all">
                    <h3 class="text-sm font-bold text-white mb-4 uppercase tracking-wider">Tulis Pengalaman Anda</h3>
                    <form action="{{ route('rider.reviews.store', $spklu->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Rating <span class="text-rose-500">*</span></label>
                            <div class="flex items-center gap-3 bg-slate-900 p-3 rounded-xl border border-slate-700">
                                @for($i=1; $i<=5; $i++)
                                <label class="cursor-pointer flex items-center gap-1.5 group">
                                    <input type="radio" name="rating" value="{{ $i }}" class="text-emerald-500 focus:ring-emerald-500 bg-slate-800 border-slate-600" {{ $i==5 ? 'checked' : '' }} required>
                                    <span class="text-xs font-bold text-slate-400 group-hover:text-emerald-400 transition-colors">{{ $i }} <span class="text-[10px]">&starf;</span></span>
                                </label>
                                @endfor
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Komentar Wajib <span class="text-rose-500">*</span></label>
                            <textarea name="comment" rows="3" required class="w-full bg-slate-900 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-600 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 p-3" placeholder="Ceritakan detail pengalaman Anda..."></textarea>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-black text-xs uppercase tracking-widest py-2.5 px-6 rounded-xl transition-all shadow-lg shadow-emerald-500/20">Kirim Ulasan</button>
                        </div>
                    </form>
                </div>
                @else
                <div class="bg-blue-500/10 border border-blue-500/20 p-4 rounded-xl mb-6 text-center">
                    <p class="text-xs font-bold text-blue-400">Silakan login untuk memberikan ulasan pada stasiun ini.</p>
                </div>
                @endif

                @if($reviews->isEmpty())
                <div class="text-center py-8 border border-dashed border-slate-700/60 rounded-2xl">
                    <p class="text-slate-500 text-xs font-medium uppercase tracking-wider">Belum ada jejak ulasan.</p>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($reviews as $review)
                    <div class="bg-slate-800/30 p-4 rounded-2xl border border-slate-700/40">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-700 flex items-center justify-center text-slate-300 font-black text-sm uppercase shadow-inner">
                                    {{ substr($review->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-white text-sm leading-none mb-1">{{ $review->user->name ?? 'Pengendara EV' }}</p>
                                    <p class="text-[10px] text-slate-500 font-mono">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-lg shrink-0">
                                <svg class="w-3 h-3 text-amber-400 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <span class="text-xs font-black text-amber-400">{{ $review->rating }}</span>
                            </div>
                        </div>
                        @if($review->comment)
                        <p class="text-slate-300 text-xs mt-3 leading-relaxed bg-slate-900/50 p-3 rounded-xl border border-slate-700/30">"{{ $review->comment }}"</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-slate-700/50 flex justify-center">
                    {{ $reviews->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection