@extends('layouts.app')

@section('title', 'Peta SPKLU')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Jaringan <span class="text-emerald-500">SPKLU</span></h1>
        <p class="text-slate-700 font-medium mt-2">Pantau lokasi dan ketersediaan stasiun pengisian daya EV secara real-time.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 relative">
        
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

        <button id="btn-locate-me" class="absolute top-36 left-8 z-[1000] bg-white p-3 rounded-xl shadow-md border border-slate-200 hover:bg-slate-50 transition-all group focus:outline-none flex items-center justify-center" title="Temukan Lokasi Saya">
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
        // Titik tengah awal peta (Bandung)
        var map = L.map('map', {
            zoomControl: false 
        }).setView([-6.914744, 107.609810], 13);

        // Zoom Control di bawah kanan
        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);

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
                                    let machineStatusText = '';
                                    let machineStatusClass = '';
                                    let machineStatusBadge = '';
                                    if (machine.status === 'available') {
                                        machineStatusText = 'Tersedia';
                                        machineStatusClass = 'bg-emerald-50 text-emerald-600';
                                        machineStatusBadge = 'bg-emerald-500';
                                    } else if (machine.status === 'maintenance') {
                                        machineStatusText = 'Perbaikan';
                                        machineStatusClass = 'bg-slate-100 text-slate-600';
                                        machineStatusBadge = 'bg-slate-400';
                                    } else {
                                        machineStatusText = 'Dipakai';
                                        machineStatusClass = 'bg-rose-50 text-rose-600';
                                        machineStatusBadge = 'bg-rose-500';
                                    }

                                    portContent += `
                                        <div class="flex flex-col bg-white border border-slate-100 rounded-md p-2 mb-1 shadow-sm">
                                            <div class="flex justify-between items-center mb-1.5">
                                                <span class="text-[11px] font-bold text-slate-700">${machine.connector_type}</span>
                                                <span class="text-[10px] px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded font-bold">${machine.capacity_kw} kW</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full ${machineStatusBadge}"></span>
                                                <span class="text-[9px] font-bold ${machineStatusClass} px-1.5 py-0.5 rounded uppercase tracking-widest">${machineStatusText}</span>
                                            </div>
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
                                    
                                    <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                        <p class="text-xs text-slate-700 mb-1 uppercase font-bold tracking-wider">Ketersediaan Total</p>
                                        <p class="text-base m-0 text-slate-800">
                                            <b class="text-xl ${textColor}">${spklu.available}</b> 
                                            <span class="text-slate-600 font-medium text-sm">dari ${spklu.total}</span>
                                        </p>
                                    </div>
                                </div>
                            `;

                            if (activeMarkers[spklu.id]) {
                                activeMarkers[spklu.id].setPopupContent(popupContent);
                            } else {
                                // KEMBALI MENGGUNAKAN MARKER PIN BIRU STANDAR LEAFLET
                                let marker = L.marker([spklu.latitude, spklu.longitude], {
                                    id: spklu.id // simpan ID untuk mempermudah tracking
                                }).addTo(map);
                                
                                marker.bindPopup(popupContent, {
                                    className: 'custom-popup'
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

        // --- LOGIKA UTAMA FITUR LOKASI SAYA (PBI 21) ---
        let userMarker = null;
        let locateBtn = document.getElementById('btn-locate-me');

        locateBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                // Beri efek transisi warna saat tombol ditekan
                locateBtn.classList.add('text-blue-500');

                navigator.geolocation.getCurrentPosition(function(position) {
                    let userLat = position.coords.latitude;
                    let userLng = position.coords.longitude;

                    // Terbangkan peta ke koordinat GPS pengendara
                    map.flyTo([userLat, userLng], 15, {
                        animate: true,
                        duration: 1.5
                    });

                    // Icon penanda lokasi pengguna (Titik biru berdenyut khas GPS)
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

                    // Jika marker lokasi sudah ada, update posisinya saja. Jika belum, buat baru.
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
                    enableHighAccuracy: true // Menggunakan hardware GPS agar lokasi presisi
                });
            } else {
                alert('Browser Anda tidak mendukung deteksi lokasi (Geolocation).');
            }
        });
    });
</script>

<style>
    /* Reset style divIcon bawaan Leaflet untuk marker GPS */
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