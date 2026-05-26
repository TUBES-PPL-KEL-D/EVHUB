@extends('layouts.app')

@section('title', 'Peta SPKLU')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Jaringan <span class="text-blue-600">SPKLU</span></h1>
        <p class="text-slate-700 font-medium mt-2">Pantau lokasi dan ketersediaan stasiun pengisian daya EV secara real-time.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 relative">
        
        <div class="absolute top-8 left-8 z-[1000] flex gap-3">
            <div class="relative">
                <input type="text" id="search-spklu" placeholder="Cari nama atau alamat..." 
                    class="w-64 bg-white/95 backdrop-blur border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 block p-3 shadow-md outline-none transition-all">
            </div>
            <select id="filter-status" 
                class="bg-white/95 backdrop-blur border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 block p-3 shadow-md outline-none cursor-pointer transition-all">
                <option value="semua">Semua Status</option>
                <option value="tersedia">Tersedia</option>
                <option value="penuh">Penuh / Dipakai</option>
                <option value="offline">Offline / Gangguan</option>
            </select>
        </div>

        <div class="absolute top-8 right-8 z-[1000] bg-white/95 backdrop-blur px-4 py-3 rounded-xl shadow-md border border-slate-100 flex flex-col gap-2">
            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status Mesin</h3>
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

        <button id="btn-locate-me" class="absolute top-28 left-8 z-[1000] bg-white/95 backdrop-blur p-3 rounded-xl shadow-md border border-slate-200 hover:border-blue-300 transition-all group focus:outline-none flex items-center justify-center mt-2" title="Temukan Lokasi Saya">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-600 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2m0 16v2M2 12h2m16 0h2" />
            </svg>
        </button>

        <div id="map" class="w-full h-[600px] rounded-xl z-0 border border-slate-200"></div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var map = L.map('map', {
            zoomControl: false 
        }).setView([-6.914744, 107.609810], 13);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let activeMarkers = {};

        // DOM Elements pencarian & filter
        const searchInput = document.getElementById('search-spklu');
        const filterStatus = document.getElementById('filter-status');

        function fetchAndRenderMarkers() {
            // Mengambil value dari input pencarian dan dropdown filter
            let searchValue = searchInput.value;
            let statusValue = filterStatus.value;
            
            // Menyusun URL dengan Query Parameters
            let url = `{{ route('rider.api.spklu.markers') }}?search=${encodeURIComponent(searchValue)}&status=${statusValue}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    let fetchedIds = [];

                    data.forEach(spklu => {
                        if (spklu.latitude && spklu.longitude) {
                            fetchedIds.push(spklu.id);

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

                            let machines = spklu.charger_machines || []; 
                            let portContent = '';

                            if (machines.length > 0) {
                                machines.forEach(machine => {
                                    portContent += `
                                        <div class="flex justify-between items-center bg-white border border-slate-100 rounded-md px-2 py-1.5 mb-1 shadow-sm">
                                            <span class="text-[11px] font-bold text-slate-700">${machine.connector_type}</span>
                                            <span class="text-[10px] px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded font-bold">${machine.capacity_kw} kW</span>
                                        </div>
                                    `;
                                });
                            } else {
                                portContent = '<p class="text-[11px] text-slate-400 italic">Informasi port belum tersedia</p>';
                            }

                            let popupContent = `
                                <div class="p-2 min-w-[220px] font-sans">
                                    <h4 class="text-slate-900 font-bold text-lg leading-tight mb-1">${spklu.name}</h4>
                                    
                                    <div class="flex items-center gap-2 mt-2 mb-3">
                                        <span class="w-2.5 h-2.5 rounded-full ${statusColor} shadow-sm animate-pulse"></span>
                                        <span class="text-[11px] font-extrabold ${textColor} uppercase tracking-widest">
                                            ${spklu.status}
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <p class="text-[10px] text-slate-500 mb-1.5 uppercase font-bold tracking-wider">Tipe Port & Detail</p>
                                        <div class="max-h-[120px] overflow-y-auto pr-1">
                                            ${portContent}
                                        </div>
                                    </div>
                                    
                                    <div class="bg-slate-50 rounded-lg p-3 border border-slate-100 mb-2">
                                        <p class="text-xs text-slate-700 mb-1 uppercase font-bold tracking-wider">Ketersediaan Total</p>
                                        <p class="text-base m-0 text-slate-800">
                                            <b class="text-xl ${textColor}">${spklu.available}</b> 
                                            <span class="text-slate-600 font-medium text-sm">dari ${spklu.total}</span>
                                        </p>
                                    </div>
                                    
                                    <a href="/rider/spklu/${spklu.id}" class="block w-full bg-emerald-500 hover:bg-emerald-600 text-white text-center font-bold text-xs py-2 px-4 rounded-lg transition-colors mt-1">
                                        Lihat Fasilitas SPKLU
                                    </a>
                                </div>
                            `;

                            if (activeMarkers[spklu.id]) {
                                activeMarkers[spklu.id].setPopupContent(popupContent);
                            } else {
                                let marker = L.marker([spklu.latitude, spklu.longitude], {
                                    id: spklu.id
                                }).addTo(map);
                                
                                marker.bindPopup(popupContent, {
                                    className: 'custom-popup'
                                });
                                activeMarkers[spklu.id] = marker;
                            }
                        }
                    });

                    // Logika menghapus marker yang tidak ada di hasil pencarian/filter
                    for (let id in activeMarkers) {
                        if (!fetchedIds.includes(parseInt(id))) {
                            map.removeLayer(activeMarkers[id]);
                            delete activeMarkers[id];
                        }
                    }
                })
                .catch(error => console.error('Gagal mengambil data marker SPKLU:', error));
        }

        fetchAndRenderMarkers();
        
        // Panggil ulang pencarian saat pengguna mengetik atau mengubah status
        searchInput.addEventListener('input', fetchAndRenderMarkers);
        filterStatus.addEventListener('change', fetchAndRenderMarkers);

        // Pertahankan interval auto-refresh untuk data ketersediaan real-time
        setInterval(fetchAndRenderMarkers, 5000);

        // --- LOKASI SAYA ---
        let userMarker = null;
        let locateBtn = document.getElementById('btn-locate-me');

        locateBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                locateBtn.classList.add('text-blue-500');

                navigator.geolocation.getCurrentPosition(function(position) {
                    let userLat = position.coords.latitude;
                    let userLng = position.coords.longitude;

                    map.flyTo([userLat, userLng], 15, {
                        animate: true,
                        duration: 1.5
                    });

                    let userLocationIcon = L.divIcon({
                        className: 'user-gps-marker',
                        html: `
                            <div class="relative flex items-center justify-center">
                                <div class="absolute w-8 h-8 bg-blue-400 rounded-full opacity-40 animate-ping"></div>
                                <div class="w-4 h-4 bg-blue-600 border-2 border-white rounded-full shadow-md"></div>
                            </div>
                        `,
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    });

                    if (userMarker) {
                        userMarker.setLatLng([userLat, userLng]);
                    } else {
                        userMarker = L.marker([userLat, userLng], {icon: userLocationIcon}).addTo(map);
                        userMarker.bindPopup('<b class="text-blue-600">Lokasi Anda Sekarang</b>');
                    }

                    locateBtn.classList.remove('text-blue-500');
                }, function(error) {
                    alert('Gagal mendeteksi lokasi. Pastikan GPS perangkat Anda sudah aktif.');
                    locateBtn.classList.remove('text-blue-500');
                }, {
                    enableHighAccuracy: true 
                });
            } else {
                alert('Browser Anda tidak mendukung deteksi lokasi (Geolocation).');
            }
        });
    });
</script>

<style>
    .user-gps-marker {
        background: transparent;
        border: none;
    }
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