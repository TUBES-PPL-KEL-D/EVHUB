@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4 text-center text-blue-600">Peta Lokasi SPKLU EV-HUB</h1>
    
    <div id="map" class="w-full h-[500px] rounded-lg shadow-lg z-0 border border-blue-100"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Titik tengah awal peta (Diatur ke koordinat Bandung)
        var map = L.map('map').setView([-6.914744, 107.609810], 12);

        // Menambahkan tile layer OpenStreetMap (Gratis)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Mengambil data SPKLU dari sebuah backend di Laravel
        var spklus = @json($spklus);

        // Melakukan perulangan untuk menaruh marker di setiap lokasi
        spklus.forEach(function(spklu) {
            if(spklu.latitude && spklu.longitude) {
                var marker = L.marker([spklu.latitude, spklu.longitude]).addTo(map);
                
                // Menyiapkan pop-up detail saat marker diklik
                marker.bindPopup(`<div class="p-1"><b class="text-blue-700">${spklu.name}</b><br><span class="text-sm">${spklu.address}</span></div>`);
            }
        });
    });
</script>
@endsection