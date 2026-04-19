@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 relative h-screen">
    <h1 class="text-2xl font-bold mb-4">Peta Lokasi SPKLU</h1>
    
    <div id="map" class="w-full h-[80vh] rounded-xl shadow-md z-0"></div>

    <div id="spkluModal" class="fixed inset-0 z-50 hidden bg-black/20 backdrop-blur-md flex items-end sm:items-center justify-center transition-opacity duration-300">        <div class="bg-white w-full sm:w-[400px] rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl relative transform transition-transform duration-300 translate-y-full sm:translate-y-0" id="modalContent">
            
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-4 sm:hidden"></div>

            <div class="flex justify-between items-start mb-1">
                <h2 id="modalName" class="text-2xl font-extrabold text-gray-900 leading-tight">Nama SPKLU</h2>
                <button onclick="closeModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full p-2 ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="flex items-center space-x-2 mb-3">
                <span class="flex h-3 w-3 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-sm font-bold text-green-600">Tersedia</span>
            </div>

            <p id="modalAddress" class="text-sm text-gray-500 mb-5 line-clamp-2">Alamat detail SPKLU...</p>
            
            <hr class="border-gray-100 mb-5">

            <h3 class="text-lg font-bold text-gray-800 mb-3">Pilih Tipe Charger</h3>
            <div id="modalChargers" class="space-y-3 max-h-64 overflow-y-auto pr-1 pb-2">
                </div>

            <div class="mt-6">
                <button onclick="alert('Sabar ya, fitur Booking masih dalam pengembangan!')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-200 transition-all">
                    Pilih & Lanjut Booking
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi Peta (Titik tengah Bandung)
        var map = L.map('map').setView([-6.914744, 107.609810], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Fetch data API
        fetch('/api/spklus')
            .then(response => response.json())
            .then(data => {
                data.forEach(spklu => {
                    var marker = L.marker([spklu.latitude, spklu.longitude]).addTo(map);
                    
                    marker.on('click', function() {
                        openModal(spklu);
                    });
                });
            });
    });

    function openModal(spklu) {
        const modal = document.getElementById('spkluModal');
        const modalContent = document.getElementById('modalContent');
        
        // Tampilkan backgroud overlay
        modal.classList.remove('hidden');
        
        // Animasi slide up (khusus mobile) delay sedikit biar mulus
        setTimeout(() => {
            modalContent.classList.remove('translate-y-full');
        }, 10);

        document.getElementById('modalName').innerText = spklu.name;
        document.getElementById('modalAddress').innerText = spklu.address;

        let chargerList = document.getElementById('modalChargers');
        chargerList.innerHTML = ''; // Reset list

        if(spklu.chargers && spklu.chargers.length > 0) {
            spklu.chargers.forEach(charger => {
                // Tentukan warna status
                let statusColor = charger.status === 'Available' ? 'text-green-700 bg-green-100 border-green-200' : 'text-orange-700 bg-orange-100 border-orange-200';
                let statusText = charger.status === 'Available' ? 'Tersedia' : 'Maintenance';

                // Bikin bentuk Card persis Mockup
                let cardHTML = `
                    <div class="border border-gray-200 rounded-xl p-4 flex justify-between items-center hover:border-blue-500 hover:shadow-md cursor-pointer transition-all bg-white group">
                        <div>
                            <h4 class="font-extrabold text-gray-800 text-base group-hover:text-blue-600 transition-colors">${charger.charger_type}</h4>
                            <p class="text-sm font-semibold text-gray-500 mt-0.5">${charger.capacity_kw} kW</p>
                            <span class="inline-block mt-2 px-2.5 py-1 text-xs font-bold rounded-lg border ${statusColor}">
                                ${statusText}
                            </span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-full group-hover:bg-blue-50 transition-colors">
                            <svg class="w-8 h-8 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                `;
                chargerList.insertAdjacentHTML('beforeend', cardHTML);
            });
        } else {
            chargerList.innerHTML = '<div class="text-center text-gray-500 text-sm py-4">Belum ada mesin charger.</div>';
        }
    }

    function closeModal() {
        const modalContent = document.getElementById('modalContent');
        const modal = document.getElementById('spkluModal');
        
        // Animasi slide down
        modalContent.classList.add('translate-y-full');
        
        // Sembunyikan modal setelah animasi selesai
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endsection