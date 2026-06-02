@extends('layouts.app') 
@section('title', 'Tambah Mesin Charger') 

@section('content') 
<div class="space-y-6 animate-fade-in-up pb-10"> 
    
    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-400">Form Infrastruktur</p>
            <h1 class="mt-2 text-3xl font-black text-white tracking-tight">Tambah SPKLU & Mesin Baru</h1>
            <p class="mt-1 text-sm text-slate-400">Tentukan lokasi koordinat peta dan masukkan spesifikasi teknis mesin charger Anda.</p>
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
        <form action="{{ route('vendor.chargers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6"> 
            @csrf 
            
            <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl p-6"> 
                <h3 class="mb-5 text-sm font-bold uppercase tracking-wider text-emerald-400 border-b border-slate-700/50 pb-3">1. Data Stasiun & Lokasi SPKLU</h3> 
                
                <div class="grid gap-6 md:grid-cols-2 mb-6"> 
                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Nama Stasiun SPKLU</label> 
                        <input type="text" name="spklu_name" id="spklu_name" value="{{ old('spklu_name') }}" placeholder="Contoh: SPKLU Rest Area KM 97" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors"> 
                    </div> 
                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Alamat Lengkap</label> 
                        <input type="text" name="address" id="address" value="{{ old('address') }}" placeholder="Jl. Tol Cipularang..." required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors"> 
                    </div> 
                </div> 

                <div> 
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1 ml-0.5">Pilih Titik Lokasi Peta <span class="text-rose-500">*</span></label> 
                    <p class="mb-3 text-[10px] text-slate-500">Klik/Sentuh pada peta untuk meletakkan pin lokasi stasiun Anda secara akurat untuk fitur navigasi.</p> 
                    
                    <div id="map" class="w-full h-80 rounded-2xl border border-slate-700 z-0 bg-slate-900"></div> 
                    
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}" required> 
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}" required> 
                </div> 
            </div> 

            <div class="bg-slate-800/30 border border-slate-700/40 rounded-2xl p-6 mt-6"> 
                <h3 class="mb-5 text-sm font-bold uppercase tracking-wider text-emerald-400 border-b border-slate-700/50 pb-3">2. Spesifikasi Teknis Mesin Charger</h3> 
                
                <div class="grid gap-6 md:grid-cols-2"> 
                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Nama/Model Mesin</label> 
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: ABB Terra 54" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors"> 
                    </div> 
                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Tipe Konektor <span class="text-rose-500">*</span></label> 
                        <select name="connector_type" id="connector_type" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white focus:outline-none focus:border-emerald-500 transition-colors"> 
                            <option value="" disabled {{ old('connector_type') ? '' : 'selected' }}>-- Pilih Standar Konektor --</option> 
                            <option value="Type 1" {{ old('connector_type') == 'Type 1' ? 'selected' : '' }}>Type 1 (J1772) - AC</option> 
                            <option value="Type 2" {{ old('connector_type') == 'Type 2' ? 'selected' : '' }}>Type 2 (Mennekes) - AC</option> 
                            <option value="CCS1" {{ old('connector_type') == 'CCS1' ? 'selected' : '' }}>CCS1 - DC Fast Charging</option> 
                            <option value="CCS2" {{ old('connector_type') == 'CCS2' ? 'selected' : '' }}>CCS2 - DC Fast Charging</option> 
                            <option value="CHAdeMO" {{ old('connector_type') == 'CHAdeMO' ? 'selected' : '' }}>CHAdeMO - DC Fast Charging</option> 
                            <option value="GB/T" {{ old('connector_type') == 'GB/T' ? 'selected' : '' }}>GB/T - Standar China (AC/DC)</option> 
                            <option value="NACS" {{ old('connector_type') == 'NACS' ? 'selected' : '' }}>NACS (Tesla) - AC/DC</option> 
                        </select> 
                    </div> 
                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Kapasitas (kW)</label> 
                        <input type="number" name="capacity_kw" id="capacity_kw" value="{{ old('capacity_kw') }}" min="1" placeholder="Contoh: 50" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors"> 
                    </div> 
                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Harga per kWh (Rp)</label> 
                        <input type="number" name="price_per_kwh" id="price_per_kwh" value="{{ old('price_per_kwh') }}" min="0" placeholder="Contoh: 2466" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors"> 
                    </div> 
                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Jam Operasional <span class="text-rose-500">*</span></label> 
                        <div class="flex items-center gap-3"> 
                            <input type="time" name="open_time" id="open_time" value="{{ old('open_time') }}" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white focus:outline-none focus:border-emerald-500 transition-colors color-scheme-dark"> 
                            <span class="text-slate-500 font-bold text-xs">S/D</span> 
                            <input type="time" name="close_time" id="close_time" value="{{ old('close_time') }}" required class="w-full px-4 py-3 text-sm border border-slate-700 rounded-xl bg-slate-950 text-white focus:outline-none focus:border-emerald-500 transition-colors color-scheme-dark"> 
                        </div> 
                    </div> 
                    <div> 
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-0.5">Foto Mesin (Wajib)</label> 
                        <input type="file" name="photo" id="photo" accept="image/jpeg, image/png, image/jpg" required class="w-full px-4 py-2 text-sm border border-slate-700 rounded-xl bg-slate-950 text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20 focus:outline-none transition-colors"> 
                    </div> 
                </div> 
            </div> 

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 mt-6"> 
                <a href="{{ route('vendor.chargers.index') }}" class="text-center border border-slate-700 text-slate-300 hover:text-white hover:bg-slate-800 px-6 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest transition-colors w-full sm:w-auto">Batal</a> 
                <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-slate-900 px-8 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/10">Simpan Infrastruktur</button> 
            </div> 
        </form> 
    </div> 
</div> 

<style>
    /* Mengubah jam pop-up picker browser menjadi dark mode */
    .color-scheme-dark { color-scheme: dark; }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" /> 
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script> 
<script> 
    document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('map').setView([-6.9147, 107.6098], 12); 
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, 
            attribution: '© OpenStreetMap EV-HUB' 
        }).addTo(map); 

        var marker; 
        var oldLat = "{{ old('latitude') }}"; 
        var oldLng = "{{ old('longitude') }}"; 

        if (oldLat && oldLng) {
            marker = L.marker([oldLat, oldLng]).addTo(map); 
            map.setView([oldLat, oldLng], 15); 
        } 

        map.on('click', function(e) {
            var lat = e.latlng.lat; 
            var lng = e.latlng.lng; 
            if (marker) { map.removeLayer(marker); } 
            marker = L.marker([lat, lng]).addTo(map); 
            
            document.getElementById('latitude').value = lat; 
            document.getElementById('longitude').value = lng; 
            
            var addressInput = document.getElementById('address'); 
            addressInput.value = 'Sedang mencari koordinat...'; 
            
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`) 
                .then(response => response.json()) 
                .then(data => {
                    if (data && data.display_name) {
                        addressInput.value = data.display_name; 
                    } else {
                        addressInput.value = ''; 
                        alert('Alamat spesifik tidak ditemukan di titik ini. Silakan ketik manual.'); 
                    } 
                }) 
                .catch(error => {
                    console.error('Geocoding Error:', error); 
                    addressInput.value = ''; 
                    alert('Gagal mengambil data alamat. Silakan ketik manual.'); 
                }); 
        }); 

        setTimeout(function(){ map.invalidateSize() }, 400); 
    }); 
</script> 
@endsection