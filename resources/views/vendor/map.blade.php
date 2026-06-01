@extends('layouts.app')

@section('title', 'Peta SPKLU')

@section('content')
<div class="max-w-[1400px] mx-auto relative z-10 px-4">
    <div class="mb-4">
        <h1 class="text-3xl font-bold text-white tracking-tight">Peta <span class="text-emerald-400">SPKLU</span></h1>
        <p class="text-slate-300 font-normal mt-1 text-sm">Pantau lokasi dan ketersediaan stasiun pengisian daya EV secara real-time.</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-200px)] min-h-[500px]">
        
        <div class="relative w-full lg:w-2/3 h-full bg-slate-100 rounded-[2rem] overflow-hidden shadow-sm border border-slate-700/50">
            
            <div class="absolute top-6 left-6 z-[1000] flex flex-col sm:flex-row gap-3 w-full max-w-md pr-6">
                <input type="text" id="search-spklu" placeholder="Cari lokasi, nama stasiun..." 
                    class="w-full bg-slate-900/95 backdrop-blur border border-slate-700 text-white placeholder-slate-400 text-sm rounded-full focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block py-3 px-6 shadow-lg outline-none transition-all">
                
                <select id="filter-status" 
                    class="bg-slate-900/95 backdrop-blur border border-slate-700 text-white text-sm rounded-full focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block py-3 px-6 shadow-lg outline-none cursor-pointer transition-all">
                    <option value="semua">Semua Status</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="penuh">Penuh / Dipakai</option>
                    <option value="offline">Offline / Gangguan</option>
                </select>
            </div>

            <div class="absolute top-6 right-6 z-[1000] flex flex-col gap-3">
                <button id="btn-toggle-layer" class="bg-slate-900/95 backdrop-blur p-3 rounded-full shadow-lg border border-slate-700 hover:border-emerald-400 transition-all group focus:outline-none flex items-center justify-center text-slate-300 hover:text-emerald-400" title="Ubah Tampilan Peta">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </button>

                <button id="btn-locate-me" class="bg-slate-900/95 backdrop-blur p-3 rounded-full shadow-lg border border-slate-700 hover:border-emerald-400 transition-all group focus:outline-none flex items-center justify-center text-slate-300 hover:text-emerald-400" title="Temukan Lokasi Saya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2m0 16v2M2 12h2m16 0h2" />
                    </svg>
                </button>
            </div>

            <div class="absolute bottom-6 left-6 z-[1000] bg-slate-900/95 backdrop-blur px-6 py-4 rounded-3xl shadow-lg border border-slate-700 flex flex-col gap-2">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Mesin</h3>
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm"></span>
                    <span class="text-sm font-medium text-slate-200">Tersedia</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-rose-500 shadow-sm"></span>
                    <span class="text-sm font-medium text-slate-200">Penuh / Dipakai</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-slate-500 shadow-sm"></span>
                    <span class="text-sm font-medium text-slate-200">Offline</span>
                </div>
            </div>

            <div id="map" class="w-full h-full z-0"></div>
        </div>

        <div class="w-full lg:w-1/3 h-full bg-slate-900 rounded-[2rem] shadow-sm border border-slate-700 flex flex-col overflow-hidden">
            <div class="p-6 border-b border-slate-800 flex justify-between items-center bg-slate-900 shrink-0">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h2 class="text-lg font-bold text-white">Stasiun Terdekat</h2>
                </div>
                <span id="list-count" class="text-xs font-bold px-3 py-1 bg-emerald-900/50 text-emerald-400 rounded-full border border-emerald-800">
                    Memuat...
                </span>
            </div>

            <div id="station-list" class="flex-1 overflow-y-auto p-4 flex flex-col gap-4 custom-scrollbar">
                </div>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // --- INISIALISASI PETA & PBI 50 (LAYERS) ---
        var map = L.map('map', {
            zoomControl: false 
        }).setView([-6.914744, 107.609810], 13);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Layer 1: Mode Peta Jalan (Street) - Default
        var streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Layer 2: Mode Satelit (Esri World Imagery)
        var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri'
        });

        let currentLayer = 'street';
        document.getElementById('btn-toggle-layer').addEventListener('click', function() {
            if (currentLayer === 'street') {
                map.removeLayer(streetLayer);
                satelliteLayer.addTo(map);
                currentLayer = 'satellite';
                this.classList.add('border-emerald-400', 'text-emerald-400');
            } else {
                map.removeLayer(satelliteLayer);
                streetLayer.addTo(map);
                currentLayer = 'street';
                this.classList.remove('border-emerald-400', 'text-emerald-400');
            }
        });

        let activeMarkers = {};

        const searchInput = document.getElementById('search-spklu');
        const filterStatus = document.getElementById('filter-status');
        const stationListDiv = document.getElementById('station-list');
        const listCountSpan = document.getElementById('list-count');

        // --- PBI 49: FUNGSI TOMBOL BAGIKAN (COPY URL) ---
        window.shareStation = function(stationId, stationName) {
            // Membuat URL tiruan untuk dibagikan (Bisa disesuaikan dengan route detail aktual nanti)
            let shareUrl = window.location.origin + '/spklu/detail/' + stationId;
            
            navigator.clipboard.writeText(shareUrl).then(() => {
                alert(`Link untuk stasiun ${stationName} berhasil disalin ke clipboard!`);
            }).catch(err => {
                console.error('Gagal menyalin text: ', err);
                alert('Gagal menyalin link.');
            });
        }

        // Fungsi terbang ke marker saat card di klik
        window.flyToStation = function(lat, lng, id) {
            map.flyTo([lat, lng], 16, { animate: true, duration: 1 });
            if(activeMarkers[id]) {
                setTimeout(() => activeMarkers[id].openPopup(), 1000);
            }
        }

        // --- FETCH DATA & RENDER LIST (PBI 48) ---
        function fetchAndRenderMarkers() {
            let searchValue = searchInput.value;
            let statusValue = filterStatus.value;
            
            let url = `{{ route('rider.api.spklu.markers') }}?search=${encodeURIComponent(searchValue)}&status=${statusValue}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    let fetchedIds = [];
                    let listHTML = ''; // Variabel penampung Card HTML

                    // Update Badge Jumlah Data
                    listCountSpan.innerText = `${data.length} SPKLU`;

                    if(data.length === 0) {
                        listHTML = `<div class="text-center text-slate-500 mt-10 text-sm">Tidak ada stasiun yang sesuai filter.</div>`;
                    }

                    data.forEach(spklu => {
                        if (spklu.latitude && spklu.longitude) {
                            fetchedIds.push(spklu.id);

                            let statusBadgeColor = 'bg-slate-800 text-slate-300 border-slate-600'; 
                            let statusDotColor = 'bg-slate-400';
                            
                            if (spklu.status === 'tersedia') {
                                statusBadgeColor = 'bg-emerald-900/30 text-emerald-400 border-emerald-800';
                                statusDotColor = 'bg-emerald-500';
                            }
                            if (spklu.status === 'penuh') {
                                statusBadgeColor = 'bg-rose-900/30 text-rose-400 border-rose-800';
                                statusDotColor = 'bg-rose-500';
                            }

                            // --- GENERATE LIST CARD (PBI 48) ---
                            let portsText = spklu.charger_machines && spklu.charger_machines.length > 0 
                                ? spklu.charger_machines.map(m => m.connector_type).join(', ') 
                                : 'Info port tidak tersedia';

                            listHTML += `
                                <div class="bg-slate-800 rounded-2xl p-5 border border-slate-700 hover:border-emerald-500 transition-all shadow-sm group">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-white font-bold text-base cursor-pointer group-hover:text-emerald-400 transition-colors" onclick="flyToStation(${spklu.latitude}, ${spklu.longitude}, ${spklu.id})">${spklu.name}</h3>
                                        <span class="text-[10px] font-bold px-2 py-1 rounded-full border flex items-center gap-1.5 ${statusBadgeColor} uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full ${statusDotColor}"></span>
                                            ${spklu.status}
                                        </span>
                                    </div>
                                    
                                    <p class="text-slate-400 text-xs mb-4 line-clamp-2">${spklu.address || 'Alamat tidak tersedia'}</p>
                                    
                                    <div class="grid grid-cols-2 gap-3 mb-4 text-xs">
                                        <div class="flex items-center gap-2 text-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                            <span class="truncate">${portsText}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                                            <span>${spklu.available} / ${spklu.total} Mesin</span>
                                        </div>
                                    </div>

                                    <div class="flex gap-2 mt-4 pt-4 border-t border-slate-700/50">
                                        <button onclick="flyToStation(${spklu.latitude}, ${spklu.longitude}, ${spklu.id})" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold py-2 rounded-xl transition-colors">
                                            Lihat di Peta
                                        </button>
                                        
                                        <button onclick="shareStation(${spklu.id}, '${spklu.name}')" class="p-2 bg-slate-700 hover:bg-slate-600 rounded-xl text-slate-300 hover:text-white transition-colors border border-slate-600" title="Bagikan Link">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            `;

                            // --- RENDER MAP POPUP ---
                            let popupContent = `
                                <div class="p-2 min-w-[200px] font-sans">
                                    <h4 class="text-slate-900 font-bold text-base leading-tight mb-1">${spklu.name}</h4>
                                    <div class="flex items-center gap-2 mt-2 mb-2">
                                        <span class="w-2.5 h-2.5 rounded-full ${statusDotColor} shadow-sm"></span>
                                        <span class="text-[11px] font-extrabold text-slate-700 uppercase tracking-widest">${spklu.status}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-2">Mesin Tersedia: <b class="text-slate-900">${spklu.available} / ${spklu.total}</b></p>
                                </div>
                            `;

                            if (activeMarkers[spklu.id]) {
                                activeMarkers[spklu.id].setPopupContent(popupContent);
                            } else {
                                let marker = L.marker([spklu.latitude, spklu.longitude], { id: spklu.id }).addTo(map);
                                marker.bindPopup(popupContent, { className: 'custom-popup' });
                                activeMarkers[spklu.id] = marker;
                            }
                        }
                    });

                    // Inject List ke DOM
                    stationListDiv.innerHTML = listHTML;

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
        searchInput.addEventListener('input', fetchAndRenderMarkers);
        filterStatus.addEventListener('change', fetchAndRenderMarkers);
        setInterval(fetchAndRenderMarkers, 5000);

        // --- LOKASI SAYA ---
        let userMarker = null;
        let locateBtn = document.getElementById('btn-locate-me');

        locateBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                locateBtn.classList.add('text-emerald-500', 'border-emerald-500');

                navigator.geolocation.getCurrentPosition(function(position) {
                    let userLat = position.coords.latitude;
                    let userLng = position.coords.longitude;

                    map.flyTo([userLat, userLng], 15, { animate: true, duration: 1.5 });

                    // Tetap menggunakan marker GPS default sesuai kesepakatan sebelumnya
                    let userLocationIcon = L.divIcon({
                        className: 'user-gps-marker',
                        html: `
                            <div class="relative flex items-center justify-center">
                                <div class="absolute w-8 h-8 bg-emerald-400 rounded-full opacity-40 animate-ping"></div>
                                <div class="w-4 h-4 bg-emerald-500 border-2 border-white rounded-full shadow-md"></div>
                            </div>
                        `,
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    });

                    if (userMarker) {
                        userMarker.setLatLng([userLat, userLng]);
                    } else {
                        userMarker = L.marker([userLat, userLng], {icon: userLocationIcon}).addTo(map);
                        userMarker.bindPopup('<b class="text-emerald-600">Lokasi Anda Sekarang</b>').openPopup();
                    }

                    locateBtn.classList.remove('text-emerald-500', 'border-emerald-500');
                }, function(error) {
                    alert('Gagal mendeteksi lokasi. Pastikan GPS perangkat Anda sudah aktif.');
                    locateBtn.classList.remove('text-emerald-500', 'border-emerald-500');
                }, { enableHighAccuracy: true });
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
    
    /* Styling Scrollbar khusus untuk area List View */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent; 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155; 
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #475569; 
    }
    
    /* Popup styling di area Map */
    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
        border: 1px solid #e2e8f0;
    }
    .leaflet-popup-content {
        margin: 12px;
    }
</style>
@endsection