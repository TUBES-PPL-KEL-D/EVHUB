@extends('layouts.app')

@section('content')
    <div id="map" class="h-screen w-full relative z-10"></div>

    <div id="spkluPanel" class="absolute top-24 left-8 z-50 hidden w-96 bg-teal-500/30 backdrop-blur-md border border-white/40 rounded-3xl p-4 shadow-2xl transition-all duration-300">
        
        <div class="bg-gradient-to-br from-teal-400 to-teal-600 rounded-2xl p-5 text-white shadow-inner relative">
            
            <button onclick="closePanel()" class="absolute top-4 right-4 text-white/70 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="flex justify-between items-start mb-4">
                <div class="flex gap-3">
                    <div class="mt-1">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <h2 id="panelName" class="text-xl font-bold tracking-wide">Memuat...</h2>
                        <p id="panelAddress" class="text-xs text-teal-100 mt-1">Silakan tunggu sebentar</p>
                    </div>
                </div>
            </div>

            <div id="panelChargersList" class="space-y-4 mt-6">
                </div>

            <button onclick="alert('Fitur Booking sedang dalam tahap pengembangan! Tunggu update selanjutnya ya 🚀')" class="w-full mt-6 bg-white text-teal-600 font-bold py-2 rounded-full shadow-md hover:bg-gray-50 transition">
                Book Now
            </button>
        </div>
    </div>

    <script>
        function openModal(spkluId) { 
            const panel = document.getElementById('spkluPanel');
            panel.classList.remove('hidden');
            
            // Set loading state
            document.getElementById('panelName').innerText = 'Memuat data...';
            document.getElementById('panelAddress').innerText = '';
            document.getElementById('panelChargersList').innerHTML = '';

            // Ambil data JSON 
            fetch(`/spklu/${spkluId}/detail`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('panelName').innerText = data.name;
                    document.getElementById('panelAddress').innerText = data.address;

                    const chargerList = document.getElementById('panelChargersList');
                    chargerList.innerHTML = ''; 
                    
                    if (data.chargers && data.chargers.length > 0) {
                        data.chargers.forEach(charger => {
                            let isAvailable = charger.status === 'Available';
                            let statusColor = isAvailable ? 'text-green-500' : 'text-red-500';
                            
                            chargerList.innerHTML += `
                                <div class="grid grid-cols-2 gap-4 text-sm border-t border-teal-300/50 pt-4 first:border-0 first:pt-0">
                                    <div>
                                        <div class="flex items-center gap-2 text-teal-100">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z"></path></svg>
                                            <p>Charger Type</p>
                                        </div>
                                        <p class="font-semibold ml-6">${charger.charger_type}</p>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 text-teal-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            <p>Capacity</p>
                                        </div>
                                        <p class="font-semibold ml-6">${charger.capacity_kw} kW</p>
                                    </div>
                                    <div class="col-span-2 flex justify-between items-center bg-teal-800/20 rounded-lg p-2">
                                        <span class="text-teal-100">Status Mesin</span>
                                        <div class="flex items-center gap-2 bg-white px-3 py-1 rounded-full">
                                            <span class="w-2 h-2 rounded-full ${isAvailable ? 'bg-green-500' : 'bg-red-500'}"></span>
                                            <span class="text-xs font-bold ${statusColor}">${charger.status}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        chargerList.innerHTML = '<p class="text-center text-teal-100 italic py-2">Belum ada mesin charger di stasiun ini.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('panelName').innerText = 'Gagal memuat data';
                });
        }

        function closePanel() {
            document.getElementById('spkluPanel').classList.add('hidden');
        }
    </script>
@endsection