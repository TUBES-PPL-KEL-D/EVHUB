@extends('layouts.app')

@section('title', 'Peta SPKLU')

@section('content')
<div class="max-w-[1400px] mx-auto relative z-10 px-4 overflow-hidden">
    <div class="mb-4">
        <h1 class="text-3xl font-bold text-white tracking-tight">Jaringan <span class="text-emerald-400">SPKLU</span></h1>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                </button>
                <button id="btn-locate-me" class="bg-slate-900/95 backdrop-blur p-3 rounded-full shadow-lg border border-slate-700 hover:border-emerald-400 transition-all group focus:outline-none flex items-center justify-center text-slate-300 hover:text-emerald-400" title="Temukan Lokasi Saya (Reset)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2m0 16v2M2 12h2m16 0h2" /></svg>
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
            </div>

            <div id="route-info" class="hidden absolute bottom-6 right-6 z-[1000] bg-slate-900/95 backdrop-blur pl-6 pr-4 py-3 rounded-2xl shadow-2xl border border-blue-500/50 flex items-center gap-5 transition-all">
                <div class="flex items-center gap-2 text-blue-400 bg-blue-900/30 p-2 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>
                </div>
                <div class="flex flex-col">
                    <span id="route-time" class="text-lg font-extrabold text-white leading-tight">-- Menit</span>
                    <span class="text-xs text-slate-300 font-medium">Jarak: <span id="route-distance" class="text-blue-400 font-bold">-- km</span> • <span id="route-dest">Tujuan</span></span>
                </div>
                <div class="h-8 w-px bg-slate-700 mx-1"></div>
                <button onclick="clearRoute()" class="p-2 bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-500 rounded-full transition-colors" title="Tutup Rute">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
            </div>

            <div id="map" class="w-full h-full z-0"></div>
        </div>

        <div class="w-full lg:w-1/3 h-full bg-slate-900 rounded-[2rem] shadow-sm border border-slate-700 flex flex-col overflow-hidden">
            <div class="p-6 border-b border-slate-800 flex justify-between items-center bg-slate-900 shrink-0">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
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
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        var map = L.map('map', { zoomControl: false }).setView([-6.914744, 107.609810], 13);
        L.control.zoom({ position: 'topleft' }).addTo(map);

        var streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
        var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });

        let currentLayer = 'street';
        document.getElementById('btn-toggle-layer').addEventListener('click', function() {
            if (currentLayer === 'street') {
                map.removeLayer(streetLayer); satelliteLayer.addTo(map); currentLayer = 'satellite';
                this.classList.add('border-emerald-400', 'text-emerald-400');
            } else {
                map.removeLayer(satelliteLayer); streetLayer.addTo(map); currentLayer = 'street';
                this.classList.remove('border-emerald-400', 'text-emerald-400');
            }
        });

        // Icon Default Google Maps Merah
        const redIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });

        // --- VARIABEL LOKASI & ROUTING ---
        let activeMarkers = {};
        let userMarker = null;
        let currentUserLat = null;
        let currentUserLng = null;
        let routingControl = null;

        const searchInput = document.getElementById('search-spklu');
        const filterStatus = document.getElementById('filter-status');
        const stationListDiv = document.getElementById('station-list');
        const listCountSpan = document.getElementById('list-count');

        // Kalkulasi Jarak Haversine
        function calculateDistance(lat1, lon1, lat2, lon2) {
            var R = 6371; 
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLon = (lon2 - lon1) * Math.PI / 180;
            var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
            return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)));
        }

        // --- PEMBUATAN IKON LOKASI MODERN PENGGUNA ---
        const createModernUserIcon = () => L.divIcon({
            className: 'user-gps-marker',
            html: `
                <div class="relative flex items-center justify-center w-12 h-12">
                    <div class="absolute w-full h-full bg-blue-500 rounded-full opacity-20 animate-ping"></div>
                    <div class="absolute w-7 h-7 bg-blue-500/40 rounded-full animate-pulse"></div>
                    <div class="w-4 h-4 bg-blue-600 border-[3px] border-white rounded-full shadow-[0_0_12px_rgba(59,130,246,0.9)] z-10"></div>
                </div>
            `,
            iconSize: [48, 48],
            iconAnchor: [24, 24]
        });

        // --- FUNGSI RENDER LOKASI PENGGUNA & POPUP 5 DETIK ---
        function renderUserLocation(lat, lng, panTo = false, showPopup = false) {
            if (userMarker) {
                userMarker.setLatLng([lat, lng]);
            } else {
                userMarker = L.marker([lat, lng], {icon: createModernUserIcon()}).addTo(map);
                userMarker.bindPopup('<div class="text-center font-bold text-blue-600 px-3 py-1">Lokasi Anda Saat Ini</div>', {
                    closeButton: false,
                    className: 'modern-user-popup',
                    offset: [0, -10]
                });
            }

            if (panTo) {
                map.flyTo([lat, lng], 15, { animate: true, duration: 1.5 });
            }

            if (showPopup) {
                userMarker.openPopup();
                // Pop-up hilang otomatis setelah 5 detik
                setTimeout(() => {
                    if (userMarker && userMarker.isPopupOpen()) {
                        userMarker.closePopup();
                    }
                }, 5000);
            }
        }

        // --- INIT LOKASI OTOMATIS SAAT HALAMAN DIBUKA ---
        function initUserLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        currentUserLat = position.coords.latitude;
                        currentUserLng = position.coords.longitude;
                        // Langsung muncul dan pan ke pengguna otomatis saat load
                        renderUserLocation(currentUserLat, currentUserLng, true, true);
                        fetchAndRenderMarkers(); 
                    },
                    (error) => { console.log("Lokasi gagal ditarik otomatis."); },
                    { enableHighAccuracy: true }
                );

                navigator.geolocation.watchPosition(
                    (position) => {
                        currentUserLat = position.coords.latitude;
                        currentUserLng = position.coords.longitude;
                        // Render pergerakan tanpa auto-pan agar tidak mengganggu jika pengguna sedang melihat area lain
                        renderUserLocation(currentUserLat, currentUserLng, false, false);
                    },
                    (error) => { console.log("Gagal melacak lokasi."); },
                    { enableHighAccuracy: true }
                );
            }
        }
        initUserLocation();

        // --- TOMBOL LOKASI SAYA (FUNGSI RESET TITIK) ---
        let locateBtn = document.getElementById('btn-locate-me');
        locateBtn.addEventListener('click', function() {
            if (currentUserLat && currentUserLng) {
                renderUserLocation(currentUserLat, currentUserLng, true, true);
            } else {
                if (navigator.geolocation) {
                    locateBtn.classList.add('animate-pulse', 'text-blue-500');
                    navigator.geolocation.getCurrentPosition(function(position) {
                        currentUserLat = position.coords.latitude;
                        currentUserLng = position.coords.longitude;
                        renderUserLocation(currentUserLat, currentUserLng, true, true);
                        locateBtn.classList.remove('animate-pulse', 'text-blue-500');
                    }, function(error) {
                        alert('Gagal mendeteksi lokasi. Pastikan GPS perangkat Anda sudah aktif.');
                        locateBtn.classList.remove('animate-pulse', 'text-blue-500');
                    }, { enableHighAccuracy: true });
                }
            }
        });

        // --- FUNGSI MENGGAMBAR RUTE BIRU ---
        window.clearRoute = function() {
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }
            document.getElementById('route-info').classList.add('hidden');
        };

        window.getRoute = function(destLat, destLng, destName) {
            if (!currentUserLat || !currentUserLng) {
                alert("Sistem belum mendeteksi lokasi Anda. Harap tunggu sebentar atau klik tombol 'Temukan Lokasi Saya'.");
                return;
            }
            window.clearRoute();

            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(currentUserLat, currentUserLng),
                    L.latLng(destLat, destLng)
                ],
                router: L.Routing.osrmv1({ language: 'id', profile: 'driving' }),
                lineOptions: {
                    styles: [{color: '#3b82f6', opacity: 0.5, weight: 6}] // WARNA BIRU 50%
                },
                createMarker: function() { return null; },
                show: false, 
                addWaypoints: false,
                draggableWaypoints: false,
                fitSelectedRoutes: true,
            }).addTo(map);

            routingControl.on('routesfound', function(e) {
                var summary = e.routes[0].summary;
                var distanceKm = (summary.totalDistance / 1000).toFixed(1);
                var totalMinutes = Math.round(summary.totalTime / 60);
                
                var timeText = totalMinutes > 60 
                    ? Math.floor(totalMinutes / 60) + " Jam " + (totalMinutes % 60) + " Mnt" 
                    : totalMinutes + " Menit";

                document.getElementById('route-info').classList.remove('hidden');
                document.getElementById('route-time').innerText = timeText;
                document.getElementById('route-distance').innerText = distanceKm;
                document.getElementById('route-dest').innerText = destName;
            });
        };

        window.flyToStation = function(lat, lng, id) {
            map.flyTo([lat, lng], 16, { animate: true, duration: 1 });
            if(activeMarkers[id]) setTimeout(() => activeMarkers[id].openPopup(), 1000);
        }

        function fetchAndRenderMarkers() {
            let searchValue = searchInput.value;
            let statusValue = filterStatus.value;
            let url = `{{ route('rider.api.spklu.markers') }}?search=${encodeURIComponent(searchValue)}&status=${statusValue}`;

            fetch(url).then(response => response.json()).then(data => {
                let fetchedIds = [];
                let listHTML = ''; 
                listCountSpan.innerText = `${data.length} SPKLU`;

                if(data.length === 0) {
                    listHTML = `<div class="text-center text-slate-500 mt-10 text-sm">Tidak ada stasiun yang sesuai.</div>`;
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

                        let portsText = spklu.charger_machines && spklu.charger_machines.length > 0 
                            ? spklu.charger_machines.map(m => m.connector_type).join(', ') : 'Port tidak tersedia';

                        let distanceHTML = '';
                        if (currentUserLat && currentUserLng) {
                            let dist = calculateDistance(currentUserLat, currentUserLng, spklu.latitude, spklu.longitude);
                            let estTime = Math.round(dist * 2); 
                            distanceHTML = `
                                <div class="flex items-center gap-2 mb-3 bg-slate-900/50 rounded-lg p-2 border border-slate-700/50 w-fit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span class="text-xs text-slate-300"><span class="font-bold text-white">~${estTime} Mnt</span> (${dist.toFixed(1)} km)</span>
                                </div>
                            `;
                        }

                        listHTML += `
                            <div class="bg-slate-800 rounded-2xl p-5 border border-slate-700 hover:border-emerald-500 transition-all shadow-sm group">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1 pr-3">
                                        <h3 class="text-white font-bold text-base cursor-pointer group-hover:text-emerald-400 transition-colors mb-1" onclick="flyToStation(${spklu.latitude}, ${spklu.longitude}, ${spklu.id})">${spklu.name}</h3>
                                        <p class="text-slate-400 text-[11px] line-clamp-1 mb-2">${spklu.address || '-'}</p>
                                    </div>
                                    <span class="text-[9px] font-bold px-2 py-1 rounded-full border flex items-center gap-1.5 ${statusBadgeColor} uppercase tracking-wider shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full ${statusDotColor}"></span>${spklu.status}
                                    </span>
                                </div>
                                
                                ${distanceHTML}

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

                                <div class="flex gap-2 mt-2 pt-4 border-t border-slate-700/50">
                                    <button onclick="flyToStation(${spklu.latitude}, ${spklu.longitude}, ${spklu.id})" class="p-2.5 bg-slate-700 hover:bg-slate-600 rounded-xl text-slate-300 hover:text-white transition-colors border border-slate-600" title="Lihat di Peta">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </button>
                                    
                                    <button onclick="getRoute(${spklu.latitude}, ${spklu.longitude}, '${spklu.name}')" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                                        Gas Ke Sini
                                    </button>
                                </div>
                            </div>
                        `;

                        let popupContent = `
                            <div class="p-2 min-w-[200px] font-sans">
                                <h4 class="text-slate-900 font-bold text-base leading-tight mb-1">${spklu.name}</h4>
                                <div class="flex items-center gap-2 mt-2 mb-2">
                                    <span class="w-2.5 h-2.5 rounded-full ${statusDotColor} shadow-sm"></span>
                                    <span class="text-[11px] font-extrabold text-slate-700 uppercase tracking-widest">${spklu.status}</span>
                                </div>
                                <div class="flex justify-between items-center mt-3 pt-3 border-t border-slate-200">
                                    <p class="text-xs text-slate-600 m-0">Tersedia: <b class="text-slate-900">${spklu.available}/${spklu.total}</b></p>
                                    <button onclick="getRoute(${spklu.latitude}, ${spklu.longitude}, '${spklu.name}')" class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg hover:bg-emerald-200 transition-colors">
                                        Gas Ke Sini
                                    </button>
                                </div>
                            </div>
                        `;

                        if (activeMarkers[spklu.id]) {
                            activeMarkers[spklu.id].setPopupContent(popupContent);
                        } else {
                            let marker = L.marker([spklu.latitude, spklu.longitude], { id: spklu.id, icon: redIcon }).addTo(map);
                            marker.bindPopup(popupContent, { className: 'custom-popup' });
                            activeMarkers[spklu.id] = marker;
                        }
                    }
                });

                stationListDiv.innerHTML = listHTML;

                for (let id in activeMarkers) {
                    if (!fetchedIds.includes(parseInt(id))) {
                        map.removeLayer(activeMarkers[id]); delete activeMarkers[id];
                    }
                }
            }).catch(error => console.error('Gagal mengambil data:', error));
        }

        fetchAndRenderMarkers();
        searchInput.addEventListener('input', fetchAndRenderMarkers);
        filterStatus.addEventListener('change', fetchAndRenderMarkers);
        setInterval(fetchAndRenderMarkers, 10000); 
    });
</script>

<style>
    .user-gps-marker { background: transparent; border: none; }
    
    .modern-user-popup .leaflet-popup-content-wrapper {
        border-radius: 9999px; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #bfdbfe; 
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
    }
    .modern-user-popup .leaflet-popup-tip {
        background: rgba(255, 255, 255, 0.95);
    }
    
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
    .leaflet-popup-content-wrapper { border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2); border: 1px solid #e2e8f0; }
    .leaflet-popup-content { margin: 12px; }
    
    .leaflet-routing-container { display: none !important; }
</style>
@endsection