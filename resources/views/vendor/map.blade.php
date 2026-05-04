@extends('layouts.app')

@section('title', 'Peta SPKLU')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header Halaman -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Jaringan <span class="text-emerald-500">SPKLU</span></h1>
        <p class="text-slate-500 font-medium mt-2">Pantau lokasi dan ketersediaan stasiun pengisian daya EV secara real-time.</p>
    </div>

    <!-- Card Container untuk Peta -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 relative">
        
        <!-- Status Legend (Indikator Warna) -->
        <div class="absolute top-8 right-8 z-[1000] bg-white/95 backdrop-blur px-4 py-3 rounded-xl shadow-md border border-slate-100 flex flex-col gap-2">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Mesin</h3>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm"></span>
                <span class="text-sm font-medium text-slate-700">Tersedia</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500 shadow-sm"></span>
                <span class="text-sm font-medium text-slate-700">Penuh / Dipakai</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-slate-300 shadow-sm"></span>
                <span class="text-sm font-medium text-slate-700">Offline / Gangguan</span>
            </div>
        </div>

        <!-- Wadah Peta -->
        <div id="map" class="w-full h-[600px] rounded-xl z-0 border border-slate-200"></div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Titik tengah awal peta (Diatur ke koordinat Bandung)
        var map = L.map('map', {
            zoomControl: false // Kita matikan zoom bawaan untuk dikustomisasi posisinya nanti jika perlu
        }).setView([-6.914744, 107.609810], 13);

        // Tambahkan Zoom Control di posisi bawah kanan agar tidak tertutup legend
        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);

        // Menambahkan tile layer OpenStreetMap (Desain standar)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let activeMarkers = {};

        function fetchAndRenderMarkers() {
            fetch('{{ route('rider.api.spklu.markers') }}')
                .then(response => response.json())
                .then(data => {
                    data.forEach(spklu => {
                        if (spklu.latitude && spklu.longitude) {
                            
                            // Sesuaikan warna status dengan palet desain (Emerald & Rose)
                            let statusColor = 'bg-slate-400'; 
                            let textColor = 'text-slate-700';
                            
                            if (spklu.status === 'tersedia') {
                                statusColor = 'bg-emerald-500';
                                textColor = 'text-emerald-700';
                            }
                            if (spklu.status === 'penuh') {
                                statusColor = 'bg-rose-500';
                                textColor = 'text-rose-700';
                            }

                            // Desain popup UI mengikuti gaya kartu aplikasi
                            let popupContent = `
                                <div class="p-2 min-w-[200px] font-sans">
                                    <h4 class="text-slate-900 font-bold text-lg leading-tight mb-1">${spklu.name}</h4>
                                    
                                    <div class="flex items-center gap-2 mt-3 mb-2">
                                        <span class="w-2.5 h-2.5 rounded-full ${statusColor} shadow-sm animate-pulse"></span>
                                        <span class="text-[11px] font-extrabold ${textColor} uppercase tracking-widest">
                                            ${spklu.status}
                                        </span>
                                    </div>
                                    
                                    <div class="bg-slate-50 rounded-lg p-3 mt-3 border border-slate-100">
                                        <p class="text-xs text-slate-500 mb-1 uppercase font-bold tracking-wider">Ketersediaan Mesin</p>
                                        <p class="text-base m-0 text-slate-800">
                                            <b class="text-xl ${textColor}">${spklu.available}</b> <span class="text-slate-400 font-medium text-sm">dari ${spklu.total}</span>
                                        </p>
                                    </div>
                                </div>
                            `;

                            if (activeMarkers[spklu.id]) {
                                activeMarkers[spklu.id].setPopupContent(popupContent);
                            } else {
                                let marker = L.marker([spklu.latitude, spklu.longitude]).addTo(map);
                                marker.bindPopup(popupContent, {
                                    className: 'custom-popup' // Opsi untuk styling tambahan via CSS jika diperlukan
                                });
                                activeMarkers[spklu.id] = marker;
                            }
                        }
                    });
                })
                .catch(error => console.error('Gagal mengambil data marker SPKLU:', error));
        }

        fetchAndRenderMarkers();
        setInterval(fetchAndRenderMarkers, 5000);
    });
</script>

<style>
    /* Styling khusus untuk merapikan Popup Leaflet agar menyatu dengan Tailwind */
    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
    }
    .leaflet-popup-content {
        margin: 12px;
    }
    .leaflet-container a.leaflet-popup-close-button {
        color: #94a3b8;
        padding: 8px;
    }
</style>
@endsection