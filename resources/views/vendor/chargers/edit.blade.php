@extends('layouts.app') 
@section('title', 'Edit Mesin Charger') 

@php 
    $hours = explode(' - ', $charger->operational_hours); 
    $openTime = isset($hours[0]) ? trim($hours[0]) : ''; 
    $closeTime = isset($hours[1]) ? trim($hours[1]) : ''; 
@endphp 

@section('content') 
<div class="space-y-6 animate-fade-in-up pb-10"> 
    
    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-amber-400">Pembaruan Data</p>
            <h1 class="mt-2 text-3xl font-black text-white tracking-tight">Edit Detail Mesin Charger</h1>
            <p class="mt-1 text-sm text-slate-400">Perbarui spesifikasi teknis, harga, atau status operasional mesin Anda saat ini.</p>
        </div>
        <a href="{{ route('vendor.chargers.index') }}" class="bg-slate-800 text-slate-200 border border-slate-700 rounded-xl px-5 py-3 text-xs font-black uppercase tracking-widest hover:bg-slate-700 transition-colors shrink-0">
            Kembali
        </a>
    </div>

    @if ($errors->any()) 
    <div class="bg-rose-500/10 border border-rose-500/20 p-5 rounded-2xl shadow-sm"> 
        <p class="font-bold text-rose-400 text-sm mb-2">Ada kesalahan pada formulir:</p> 
        <ul class="list-disc pl-5 text-xs text-rose-400/80 space-y-1"> 
            @foreach ($errors->all() as $error) 
                <li>{{ $error }}</li> 
            @endforeach 
        </ul> 
    </div> 
    @endif 

    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl"> 
        <form action="{{ route('vendor.chargers.update', $charger->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6"> 
            @csrf 
            @method('PUT') 
            
            <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl p-6"> 
                <div class="grid gap-6 md:grid-cols-2"> 
                    
                    <div class="md:col-span-2"> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Lokasi SPKLU Terhubung</label> 
                        <input type="text" value="{{ $charger->spklu->name ?? 'Tidak diketahui' }} - {{ $charger->spklu->address ?? '' }}" disabled class="w-full px-4 py-3 text-sm border border-slate-800 rounded-xl bg-slate-900/50 text-slate-500 cursor-not-allowed"> 
                        <p class="mt-2 text-[10px] text-slate-500 font-medium">Lokasi SPKLU terikat secara permanen di database dan tidak dapat diubah dari panel ini.</p> 
                    </div> 

                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Nama/Model Mesin</label> 
                        <input type="text" name="name" id="name" value="{{ old('name', $charger->name) }}" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors"> 
                    </div> 

                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Tipe Konektor <span class="text-rose-500">*</span></label> 
                        <select name="connector_type" id="connector_type" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white focus:outline-none focus:border-emerald-500 transition-colors"> 
                            <option value="" disabled>-- Pilih Standar Konektor --</option> 
                            @php $currentConnector = old('connector_type', $charger->connector_type); @endphp 
                            <option value="Type 1" {{ $currentConnector == 'Type 1' ? 'selected' : '' }}>Type 1 (J1772) - AC</option> 
                            <option value="Type 2" {{ $currentConnector == 'Type 2' ? 'selected' : '' }}>Type 2 (Mennekes) - AC</option> 
                            <option value="CCS1" {{ $currentConnector == 'CCS1' ? 'selected' : '' }}>CCS1 - DC Fast Charging</option> 
                            <option value="CCS2" {{ $currentConnector == 'CCS2' ? 'selected' : '' }}>CCS2 - DC Fast Charging</option> 
                            <option value="CHAdeMO" {{ $currentConnector == 'CHAdeMO' ? 'selected' : '' }}>CHAdeMO - DC Fast Charging</option> 
                            <option value="GB/T" {{ $currentConnector == 'GB/T' ? 'selected' : '' }}>GB/T - Standar China (AC/DC)</option> 
                            <option value="NACS" {{ $currentConnector == 'NACS' ? 'selected' : '' }}>NACS (Tesla) - AC/DC</option> 
                        </select> 
                    </div> 

                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Kapasitas (kW)</label> 
                        <input type="number" name="capacity_kw" id="capacity_kw" value="{{ old('capacity_kw', $charger->capacity_kw) }}" min="1" step="0.01" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors"> 
                    </div> 

                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Harga per kWh (Rp)</label> 
                        <input type="number" name="price_per_kwh" id="price_per_kwh" value="{{ old('price_per_kwh', $charger->price_per_kwh) }}" min="0" step="0.01" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors"> 
                    </div> 

                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Jam Operasional <span class="text-rose-500">*</span></label> 
                        <div class="flex items-center gap-3"> 
                            <input type="time" name="open_time" id="open_time" value="{{ old('open_time', $openTime) }}" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white focus:outline-none focus:border-emerald-500 transition-colors color-scheme-dark"> 
                            <span class="text-slate-500 font-bold text-xs">S/D</span> 
                            <input type="time" name="close_time" id="close_time" value="{{ old('close_time', $closeTime) }}" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white focus:outline-none focus:border-emerald-500 transition-colors color-scheme-dark"> 
                        </div> 
                    </div> 

                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Status Operasional <span class="text-rose-500">*</span></label> 
                        <select name="status" id="status" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white focus:outline-none focus:border-emerald-500 transition-colors"> 
                            <option value="available" {{ old('status', $charger->status) == 'available' ? 'selected' : '' }}>Tersedia (Available)</option> 
                            <option value="unavailable" {{ old('status', $charger->status) == 'unavailable' ? 'selected' : '' }}>Mati (Unavailable)</option> 
                            <option value="maintenance" {{ old('status', $charger->status) == 'maintenance' ? 'selected' : '' }}>Perawatan (Maintenance)</option> 
                        </select> 
                    </div> 

                    <div class="md:col-span-2"> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Update Foto Mesin (Opsional)</label> 
                        @if($charger->photo_path) 
                            <div class="mb-4"> 
                                <img src="{{ asset('storage/' . $charger->photo_path) }}" alt="Foto Saat Ini" class="h-32 w-48 rounded-xl object-cover border border-slate-700 shadow-sm"> 
                            </div> 
                        @endif 
                        <input type="file" name="photo" id="photo" accept="image/jpeg, image/png, image/jpg" class="w-full px-4 py-2 text-sm border border-slate-700 rounded-xl bg-slate-950 text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20 focus:outline-none transition-colors"> 
                        <p class="mt-2 text-[10px] text-slate-500">Biarkan kosong jika Anda tidak ingin mengubah gambar mesin saat ini.</p> 
                    </div> 

                </div> 
            </div> 

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 mt-6"> 
                <a href="{{ route('vendor.chargers.index') }}" class="text-center border border-slate-700 text-slate-300 hover:text-white hover:bg-slate-800 px-6 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest transition-colors w-full sm:w-auto">Batal</a> 
                <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-slate-900 px-8 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/10">Perbarui Data</button> 
            </div> 
        </form> 
    </div> 
</div> 

<style>
    .color-scheme-dark { color-scheme: dark; }
</style>
@endsection