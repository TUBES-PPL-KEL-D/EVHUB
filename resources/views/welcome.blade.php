@extends('layouts.app')

@section('content')
<style>
    main { padding: 0 !important; margin: 0 !important; }
    .container { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
    body { overflow: hidden; } /* Biar ga ada scrollbar ganda di web */
    
    #sidePanel::-webkit-scrollbar { width: 5px; }
    #sidePanel::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<div class="relative w-full" style="height: calc(100vh - 64px);">
    
    <div id="map" class="absolute inset-0 z-0"></div>

    <div id="sidePanel" class="absolute left-4 top-4 bottom-4 w-[calc(100%-32px)] md:w-[420px] bg-white/95 backdrop-blur-xl shadow-2xl z-10 flex flex-col rounded-3xl border border-gray-200/50 overflow-hidden transition-all duration-300">
        
        <div class="p-5 border-b border-gray-100 bg-white/80">
            <div class="relative group">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" placeholder="Cari SPKLU..." class="w-full pl-12 pr-4 py-3 bg-gray-100/50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none font-semibold transition-all shadow-inner">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto relative">
            
            <div id="listView" class="p-5 animate-fadeIn">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="font-black text-gray-800 text-sm uppercase tracking-wider">Stasiun Terdekat</h3>
                    <span class="text-[11px] font-black text-blue-700 bg-blue-100 px-3 py-1 rounded-md" id="totalStations">0 LOKASI</span>
                </div>
                
                <div id="panelList" class="space-y-3">
                    </div>
            </div>

            <div id="detailView" class="hidden p-5 animate-fadeIn min-h-full">
                <button onclick="showList()" class="flex items-center space-x-2 text-gray-400 hover:text-blue-600 transition-all mb-6 group w-fit">
                    <div class="bg-gray-100 p-2 rounded-full group-hover:bg-blue-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </div>
                    <span class="font-black text-[11px] uppercase tracking-widest">Kembali</span>
                </button>

                <h2 id="panelName" class="text-2xl font-black text-gray-900 mb-2 leading-tight">Nama SPKLU</h2>
                <p id="panelAddress" class="text-sm text-gray-500 mb-6 leading-relaxed">Alamat lengkap stasiun pengisian...</p>

                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1.5">Status</p>
                        <p class="text-sm font-bold text-green-600 flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>Buka 24 Jam
                        </p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1.5">Harga / kWh</p>
                        <p class="text-sm font-bold text-gray-800">Rp 2.466</p>
                    </div>
                </div>

                <h3 class="text-sm font-black text-gray-800 mb-4 uppercase tracking-wider">Daftar Charger</h3>
                <div id="panelChargers" class="space-y-3">
                    </div>

                <div class="mt-8 pb-4">
                    <button onclick="alert('Sabar ya, fitur Booking masih dalam pengembangan!')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-blue-200 transition-all">
                        Pilih & Lanjut Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    window.spkluData = [];
    var map;

    document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi Peta
        map = L.map('map', { zoomControl: false }).setView([-6.914744, 107.609810], 12);
        
        // Pindah zoom control ke kanan atas/bawah biar gak ketutup panel
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Fetch data API
        fetch('/api/spklus')
            .then(response => response.json())
            .then(data => {
                window.spkluData = data;
                document.getElementById('totalStations').innerText = `${data.length} LOKASI`;
                
                renderListView(data);
                
                data.forEach(spklu => {
                    var marker = L.marker([spklu.latitude, spklu.longitude]).addTo(map);
                    marker.on('click', () => selectStation(spklu.id));
                });
            });
    });

    function renderListView(data) {
        const listContainer = document.getElementById('panelList');
        listContainer.innerHTML = '';
        data.forEach(spklu => {
            let totalChargers = spklu.chargers ? spklu.chargers.length : 0;
            let cardHTML = `
                <div onclick="selectStation(${spklu.id})" class="p-4 rounded-2xl bg-white border border-gray-100 hover:border-blue-500 hover:shadow-lg cursor-pointer transition-all group">
                    <h4 class="font-black text-gray-800 text-sm group-hover:text-blue-600">${spklu.name}</h4>
                    <p class="text-[11px] text-gray-400 mt-1 line-clamp-1">${spklu.address}</p>
                    <div class="flex items-center mt-3 space-x-2 text-[10px] font-black uppercase">
                        <span class="bg-gray-50 text-gray-500 px-2 py-1 rounded-md border border-gray-100">
                            ${totalChargers} Mesin
                        </span>
                        <span class="text-green-600">Buka 24 Jam</span>
                    </div>
                </div>`;
            listContainer.insertAdjacentHTML('beforeend', cardHTML);
        });
    }

    function selectStation(id) {
        const spklu = window.spkluData.find(s => s.id === id);
        if (spklu) {
            document.getElementById('listView').classList.add('hidden');
            document.getElementById('detailView').classList.remove('hidden');
            document.getElementById('panelName').innerText = spklu.name;
            document.getElementById('panelAddress').innerText = spklu.address;

            let chargerList = document.getElementById('panelChargers');
            chargerList.innerHTML = '';
            
            if (spklu.chargers && spklu.chargers.length > 0) {
                spklu.chargers.forEach(charger => {
                    chargerList.insertAdjacentHTML('beforeend', `
                        <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100 flex justify-between items-center group hover:bg-white hover:border-blue-500 transition-all">
                            <div>
                                <p class="text-xs font-black text-gray-800 uppercase">${charger.charger_type}</p>
                                <div class="flex items-center text-[11px] font-bold text-gray-500 mt-0.5">
                                    <svg class="w-3 h-3 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"></path></svg>
                                    ${charger.capacity_kw} kW
                                </div>
                            </div>
                            <span class="text-[9px] font-black px-2.5 py-1.5 rounded-md bg-white border border-gray-100 text-blue-600 uppercase tracking-widest">${charger.status}</span>
                        </div>`);
                });
            } else {
                chargerList.innerHTML = '<p class="text-[11px] text-gray-400 italic text-center py-4 bg-gray-50 rounded-xl">Data mesin tidak tersedia.</p>';
            }
            
            // Offset peta supaya pin stasiun agak geser ke kanan (biar gak ketutup panel)
            map.flyTo([spklu.latitude, spklu.longitude], 15);
        }
    }

    function showList() {
        document.getElementById('detailView').classList.add('hidden');
        document.getElementById('listView').classList.remove('hidden');
        map.flyTo([-6.914744, 107.609810], 12);
    }
</script>
@endsection