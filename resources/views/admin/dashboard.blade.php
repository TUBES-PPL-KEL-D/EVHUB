@extends('layouts.app') 

@section('content') 
<div class="space-y-6 animate-fade-in-up pb-10">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-2">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter">Overview</h1>
            <p class="text-slate-400 font-medium mt-1">Pantau analitik dan status ekosistem EVHUB.</p>
        </div>
        <div class="bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-full flex items-center">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2"></span>
            <span class="text-emerald-400 text-xs font-bold tracking-widest uppercase">Sistem Stabil</span>
        </div>
    </div>

    @if(session('success')) 
    <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl flex items-center space-x-3 shadow-sm"> 
        <div class="bg-emerald-500 p-1.5 rounded-full text-white"> 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> 
        </div> 
        <p class="font-bold text-emerald-400 text-sm">{{ session('success') }}</p> 
    </div> 
    @endif 

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-2xl flex flex-col justify-between">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">Tren Pertumbuhan SPKLU</h2>
                    <p class="text-sm text-slate-400 mt-1">Akumulasi pendaftaran stasiun tahun ini.</p>
                </div>
                <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-2xl border border-emerald-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                </div>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="spkluGrowthChart"></canvas>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl flex-1 flex flex-col justify-center relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 text-amber-500/5 group-hover:text-amber-500/10 transition-colors">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-slate-400 text-sm font-bold tracking-widest uppercase">Antrean Vendor</h3>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-5xl font-black text-white">{{ count($pendingVendors ?? []) }}</span>
                        <span class="text-sm text-slate-500 font-medium">Menunggu</span>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl flex-1 flex flex-col justify-center relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 text-rose-500/5 group-hover:text-rose-500/10 transition-colors">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-slate-400 text-sm font-bold tracking-widest uppercase">Laporan Kendala</h3>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-5xl font-black text-white">{{ count($recentTickets ?? []) }}</span>
                        <span class="text-sm text-slate-500 font-medium">Belum Diulas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    Verifikasi Masuk
                </h2>
                <a href="{{ route('admin.stations') }}" class="text-xs font-bold text-slate-400 hover:text-white uppercase tracking-widest transition-colors">Kelola Vendor</a>
            </div>
            
            @if(count($pendingVendors ?? []) == 0)
                <div class="py-8 text-center border border-dashed border-slate-700/50 rounded-2xl">
                    <p class="text-slate-500 font-medium text-sm">Tidak ada antrean dokumen.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach(collect($pendingVendors)->take(4) as $vendor)
                    <div class="flex items-center justify-between p-4 bg-slate-800/40 rounded-2xl border border-slate-700/30">
                        <div>
                            <h4 class="text-white font-bold text-sm">{{ $vendor->company_name }}</h4>
                            <p class="text-slate-400 text-xs mt-0.5">{{ $vendor->npwp }}</p>
                        </div>
                        <button type="button" 
                            class="bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-colors border border-blue-500/20"
                            data-id="{{ $vendor->id }}"
                            data-name="{{ $vendor->company_name }}"
                            data-npwp="{{ $vendor->npwp }}"
                            data-email="{{ $vendor->user->email ?? 'Tidak ada email' }}"
                            data-address="{{ $vendor->address ?? 'Alamat belum diisi' }}"
                            data-pdf="{{ $vendor->legality_document_path ? asset('storage/' . $vendor->legality_document_path) : '' }}"
                            onclick="openReviewModal(this)">
                            TINJAU
                        </button>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    Laporan Terbaru
                </h2>
                <a href="#" class="text-xs font-bold text-slate-400 hover:text-white uppercase tracking-widest transition-colors">Semua Tiket</a>
            </div>

            @if(!isset($recentTickets) || count($recentTickets) == 0)
                <div class="py-8 text-center border border-dashed border-slate-700/50 rounded-2xl">
                    <p class="text-slate-500 font-medium text-sm">Tidak ada laporan kendala.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach(collect($recentTickets)->take(4) as $ticket)
                    <div class="flex items-center justify-between p-4 bg-slate-800/40 rounded-2xl border border-slate-700/30">
                        <div class="pr-4">
                            <h4 class="text-white font-bold text-sm truncate max-w-[200px]">{{ $ticket->subject }}</h4>
                            <p class="text-slate-400 text-xs mt-0.5">{{ $ticket->user->name ?? 'Pengguna' }}</p>
                        </div>
                        <button class="bg-slate-700/50 text-slate-300 hover:bg-slate-600 hover:text-white px-4 py-2 rounded-xl text-xs font-black transition-colors shrink-0 border border-slate-600/50">DETAIL</button>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

<div id="reviewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/90 backdrop-blur-md opacity-0 transition-opacity duration-300">
    <div id="reviewModalContent" class="bg-slate-900 border border-slate-700/80 w-11/12 max-w-6xl rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col transform scale-95 transition-transform duration-300 max-h-[90vh]">
        
        <div class="flex justify-between items-center p-6 border-b border-slate-800">
            <h2 class="text-2xl font-black text-white flex items-center gap-3">
                <div class="p-2 bg-blue-500/10 text-blue-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                Review Kemitraan Vendor
            </h2>
            <button onclick="closeReviewModal()" class="text-slate-500 hover:text-white bg-slate-800 hover:bg-rose-500 p-2 rounded-full transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 p-6 gap-8 overflow-y-auto">
            
            <div class="lg:col-span-1 space-y-6">
                <div>
                    <h3 class="text-xs font-black tracking-widest text-slate-500 uppercase mb-4">Informasi Entitas</h3>
                    <div class="space-y-4">
                        <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-700/50">
                            <p class="text-xs text-slate-400 mb-1">Nama Perusahaan</p>
                            <p id="modalCompanyName" class="text-white font-bold text-lg">-</p>
                        </div>
                        <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-700/50">
                            <p class="text-xs text-slate-400 mb-1">Nomor NPWP</p>
                            <p id="modalNpwp" class="text-white font-mono text-sm tracking-widest">-</p>
                        </div>
                        <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-700/50">
                            <p class="text-xs text-slate-400 mb-1">Email Penanggung Jawab</p>
                            <p id="modalEmail" class="text-emerald-400 font-bold text-sm">-</p>
                        </div>
                        <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-700/50">
                            <p class="text-xs text-slate-400 mb-1">Alamat Operasional</p>
                            <p id="modalAddress" class="text-white text-sm leading-relaxed">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 flex flex-col">
                <h3 class="text-xs font-black tracking-widest text-slate-500 uppercase mb-4">Dokumen Legalitas</h3>
                <div id="pdfContainer" class="flex-grow w-full min-h-[50vh] bg-slate-800/50 rounded-2xl border border-slate-700 overflow-hidden relative">
                    </div>
            </div>
        </div>

        <div class="p-6 border-t border-slate-800 bg-slate-900/90 flex justify-end gap-4">
            <form id="formReject" action="#" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="bg-rose-500/10 text-rose-500 border border-rose-500/20 hover:bg-rose-500 hover:text-white px-8 py-3 rounded-2xl font-black tracking-widest uppercase transition-all shadow-lg text-sm">
                    Tolak Vendor
                </button>
            </form>
            <form id="formApprove" action="#" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="bg-emerald-500 text-slate-900 hover:bg-emerald-400 hover:shadow-emerald-500/20 px-8 py-3 rounded-2xl font-black tracking-widest uppercase transition-all shadow-lg text-sm">
                    Setujui Kemitraan
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // LOGIKA MODAL VERIFIKASI
    function openReviewModal(btn) {
        // Ambil data dari atribut tombol
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const npwp = btn.getAttribute('data-npwp');
        const email = btn.getAttribute('data-email');
        const address = btn.getAttribute('data-address');
        const pdfUrl = btn.getAttribute('data-pdf');

        // Suntikkan teks ke dalam modal
        document.getElementById('modalCompanyName').innerText = name;
        document.getElementById('modalNpwp').innerText = npwp;
        document.getElementById('modalEmail').innerText = email;
        document.getElementById('modalAddress').innerText = address;

        // Render PDF Viewer (Iframe)
        const pdfContainer = document.getElementById('pdfContainer');
        if (pdfUrl && pdfUrl !== '') {
            pdfContainer.innerHTML = `<iframe src="${pdfUrl}" class="w-full h-full border-0 absolute inset-0" title="Dokumen Legalitas"></iframe>`;
        } else {
            pdfContainer.innerHTML = `
                <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-500">
                    <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="font-bold tracking-widest uppercase text-sm">Dokumen Tidak Dilampirkan</span>
                </div>
            `;
        }

        // Update URL Form Submit secara dinamis berdasarkan ID
        document.getElementById('formApprove').action = `/admin/vendors/${id}/approve`;
        document.getElementById('formReject').action = `/admin/vendors/${id}/reject`;

        // Animasi Tampil Modal
        const modal = document.getElementById('reviewModal');
        const modalContent = document.getElementById('reviewModalContent');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Timeout kecil memicu reflow agar animasi CSS jalan
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function closeReviewModal() {
        const modal = document.getElementById('reviewModal');
        const modalContent = document.getElementById('reviewModalContent');

        // Animasi Sembunyi Modal
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            // Bersihkan iframe agar tidak terus memuat di latar belakang
            document.getElementById('pdfContainer').innerHTML = ''; 
        }, 300);
    }

    // LOGIKA GRAFIK CHART.JS
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('spkluGrowthChart').getContext('2d');
        const labels = {!! json_encode($chartLabels) !!};
        const dataPoints = {!! json_encode($chartData) !!};

        let gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); 
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'SPKLU Baru',
                    data: dataPoints,
                    borderColor: '#10b981', 
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#0f172a',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)', 
                        titleColor: '#94a3b8', 
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(51, 65, 85, 0.2)', 
                            drawBorder: false,
                            borderDash: [4, 4]
                        },
                        ticks: { color: '#64748b', stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endsection