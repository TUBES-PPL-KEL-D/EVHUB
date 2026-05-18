@extends('layouts.app')

@section('title', 'Tambah Mesin Charger')

@section('content')
<div class="vendor-scope">
    <div class="mx-auto max-w-4xl">
        
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="mt-2 text-3xl font-bold text-white drop-shadow-[0_2px_6px_rgba(0,0,0,0.45)]">Tambah Infrastruktur SPKLU & Mesin</h1>
            <p class="mt-2 text-slate-200 drop-shadow-[0_1px_3px_rgba(0,0,0,0.45)]">Tentukan lokasi di peta dan masukkan detail mesin charger baru Anda.</p>
        </div>

        <!-- Alert Errors -->
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">Ada kesalahan pada form:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Form Pendataan Infrastruktur</h2>
            </div>

            <!-- FORM START -->
            <form action="{{ route('vendor.chargers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 px-6 py-6">
                @csrf

                <!-- Peta Interaktif & Info SPKLU -->
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/30 p-5">
                    <h3 class="mb-4 text-md font-bold text-emerald-800">1. Data Stasiun & Lokasi</h3>
                    
                    <div class="grid gap-6 md:grid-cols-2 mb-6">
                        <div class="md:col-span-1">
                            <label for="spklu_name" class="mb-2 block text-sm font-medium text-slate-700">Nama Stasiun SPKLU</label>
                            <input type="text" name="spklu_name" id="spklu_name" value="{{ old('spklu_name') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="Contoh: SPKLU Rest Area KM 97" required>
                        </div>
                        <div class="md:col-span-1">
                            <label for="address" class="mb-2 block text-sm font-medium text-slate-700">Alamat Lengkap</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="Jl. Tol Cipularang..." required>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div class="mb-2">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Pilih Titik Lokasi Peta <span class="text-red-500">*</span></label>
                        <p class="mb-3 text-xs text-slate-500">Klik/Sentuh pada peta untuk meletakkan pin lokasi charger Anda secara akurat.</p>
                        
                        <!-- Peta dimuat di sini -->
                        <div id="map" class="w-full h-80 rounded-2xl border-2 border-slate-300 z-0"></div>
                        
                        <!-- Input Hidden untuk Koordinat -->
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}" required>
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}" required>
                    </div>
                </div>

                <!-- Info Mesin Charger -->
                <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-5 mt-6">
                    <h3 class="mb-4 text-md font-bold text-slate-800">2. Spesifikasi Mesin Charger</h3>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama/Model Mesin</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="Contoh: ABB Terra 54" required>
                        </div>

                        <div>
                            <label for="connector_type" class="mb-2 block text-sm font-medium text-slate-700">Tipe Konektor (Standar Internasional) <span class="text-red-500">*</span></label>
                            <select name="connector_type" id="connector_type" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" required>
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
                            <label for="capacity_kw" class="mb-2 block text-sm font-medium text-slate-700">Kapasitas (kW)</label>
                            <input type="number" name="capacity_kw" id="capacity_kw" value="{{ old('capacity_kw') }}" min="1" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="Contoh: 50" required>
                        </div>

                        <div>
                            <label for="price_per_kwh" class="mb-2 block text-sm font-medium text-slate-700">Harga per kWh (Rp)</label>
                            <input type="number" name="price_per_kwh" id="price_per_kwh" value="{{ old('price_per_kwh') }}" min="0" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="Contoh: 2466" required>
                        </div>

                        <div>
                            <label for="operational_hours" class="mb-2 block text-sm font-medium text-slate-700">Jam Operasional</label>
                            <input type="text" name="operational_hours" id="operational_hours" value="{{ old('operational_hours') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="08:00 - 22:00 atau 24 Jam" required>
                        </div>

                        <div>
                            <label for="photo" class="mb-2 block text-sm font-medium text-slate-700">Foto Mesin (JPG/PNG)</label>
                            <input type="file" name="photo" id="photo" accept="image/jpeg, image/png, image/jpg" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-2 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" required>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-end">
                    <div class="flex gap-3">
                        <a href="{{ route('vendor.chargers.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-[#34CBDA] px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-600">Simpan Infrastruktur</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Load Leaflet Styles & Scripts -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi peta ke titik awal (Misal: Bandung)
    var map = L.map('map').setView([-6.9147, 107.6098], 12);

    // Load Tile Layer dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap EV-HUB'
    }).addTo(map);

    var marker;

    // Jika form divalidasi dan error, kembalikan posisi marker sebelumnya
    var oldLat = "{{ old('latitude') }}";
    var oldLng = "{{ old('longitude') }}";

    if (oldLat && oldLng) {
        marker = L.marker([oldLat, oldLng]).addTo(map);
        map.setView([oldLat, oldLng], 15);
    }

    // Event ketika peta diklik
    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        // Hapus marker lama
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Buat marker baru
        marker = L.marker([lat, lng]).addTo(map);

        // Isi input hidden koordinat
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        // --- IMPLEMENTASI PBI: REVERSE GEOCODING (Alamat Otomatis) ---
        var addressInput = document.getElementById('address');
        addressInput.value = 'Sedang mencari alamat...'; // Indikator loading UI

        // Memanggil API Nominatim OpenStreetMap
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    // Masukkan hasil alamat lengkap ke input box
                    addressInput.value = data.display_name;
                } else {
                    addressInput.value = '';
                    alert('Alamat spesifik tidak ditemukan di titik ini. Silakan ketik manual.');
                }
            })
            .catch(error => {
                console.error('Geocoding Error:', error);
                addressInput.value = '';
                alert('Gagal mengambil data alamat karena masalah jaringan. Silakan ketik manual.');
            });
    });

    // Fix map render issue in hidden/flex layouts
    setTimeout(function(){ map.invalidateSize()}, 400);
});
</script>
@endsection