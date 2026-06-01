@extends('layouts.app') 

@section('content') 
<div class="space-y-6 animate-fade-in-up pb-10">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-2">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter">Pusat Kendali Admin</h1>
            <p class="text-slate-400 font-medium mt-1">Satu ruang terintegrasi untuk analitik, verifikasi, dan manajemen ekosistem.</p>
        </div>
        <div class="bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-full flex items-center">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2"></span>
            <span class="text-emerald-400 text-xs font-bold tracking-widest uppercase">Sistem Operasional</span>
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

    <div class="flex border-b border-slate-700/60 gap-2 mb-6">
        <button id="tabBtn-overview" onclick="switchMainTab('overview')" class="main-tab-btn px-6 py-3.5 font-black text-xs tracking-widest uppercase border-b-2 transition-all duration-200 text-emerald-400 border-emerald-500">
            Overview & Analytics
        </button>
        <button id="tabBtn-verifikasi" onclick="switchMainTab('verifikasi')" class="main-tab-btn px-6 py-3.5 font-black text-xs tracking-widest uppercase border-b-2 transition-all duration-200 text-slate-500 border-transparent hover:text-slate-300">
            Antrean Verifikasi ({{ count($pendingVendors ?? []) }})
        </button>
        <button id="tabBtn-manajemen" onclick="switchMainTab('manajemen')" class="main-tab-btn px-6 py-3.5 font-black text-xs tracking-widest uppercase border-b-2 transition-all duration-200 text-slate-500 border-transparent hover:text-slate-300">
            Manajemen Vendor & Stasiun
        </button>
    </div>

    <div id="panel-overview" class="main-panel-content space-y-6 block">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-2xl flex flex-col justify-between">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-white tracking-tight">Tren Pertumbuhan SPKLU</h2>
                        <p class="text-sm text-slate-400 mt-1">Akumulasi pendaftaran stasiun platform.</p>
                    </div>
                    <form action="{{ route('admin.dashboard') }}" method="GET">
                        <select name="year" onchange="this.form.submit()" class="bg-slate-800 text-slate-200 border border-slate-700 rounded-xl px-4 py-2 text-xs font-bold focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer">
                            @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                                <option value="{{ $y }}" {{ isset($selectedYear) && $selectedYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                            @endfor
                        </select>
                    </form>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="spkluGrowthChart"></canvas>
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <div onclick="switchMainTab('verifikasi')" class="bg-slate-900/40 border border-slate-700/50 hover:border-amber-500/40 cursor-pointer rounded-[2rem] p-6 backdrop-blur-xl shadow-xl flex-1 flex flex-col justify-center relative overflow-hidden group transition-all">
                    <div class="absolute -right-6 -top-6 text-amber-500/5 group-hover:text-amber-500/10 transition-colors">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-slate-400 text-sm font-bold tracking-widest uppercase">Berkas Masuk</h3>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-5xl font-black text-white">{{ count($pendingVendors ?? []) }}</span>
                            <span class="text-sm text-slate-500 font-medium">Perlu Tinjauan</span>
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
                            <span class="text-sm text-slate-500 font-medium">Aduan Pengendara</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="panel-verifikasi" class="main-panel-content space-y-6 hidden">
        <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                Validasi Berkas Pendaftaran Mitra Baru
            </h2>
            
            @if(count($pendingVendors ?? []) == 0)
                <div class="py-12 text-center border border-dashed border-slate-700/50 rounded-2xl">
                    <p class="text-slate-500 font-medium text-sm">Tidak ada antrean dokumen verifikasi saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($pendingVendors as $vendor)
                    <div class="flex items-center justify-between p-5 bg-slate-800/40 rounded-2xl border border-slate-700/30">
                        <div>
                            <h4 class="text-white font-bold text-base">{{ $vendor->company_name }}</h4>
                            <p class="text-slate-400 text-xs mt-1">NPWP: <span class="font-mono">{{ $vendor->npwp }}</span></p>
                        </div>
                        <button type="button" class="bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-colors border border-blue-500/20" data-id="{{ $vendor->id }}" data-name="{{ $vendor->company_name }}" data-npwp="{{ $vendor->npwp }}" data-email="{{ $vendor->user->email ?? 'Tidak ada email' }}" data-address="{{ $vendor->address ?? 'Alamat belum diisi' }}" data-pdf="{{ $vendor->legality_document_path ? asset('storage/' . $vendor->legality_document_path) : '' }}" onclick="openReviewModal(this)"> TINJAU </button> 
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div id="panel-manajemen" class="main-panel-content space-y-8 hidden">
        
        <div class="flex justify-between items-center bg-slate-900/30 p-4 border border-slate-700/30 rounded-2xl">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Aksi Audit & Ekspor Operasional:</p>
            <a href="{{ route('admin.export.spklu') }}" class="inline-flex items-center bg-emerald-500 text-slate-900 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> 
                Download Berkas Excel
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
                <h2 class="text-lg font-bold text-white flex items-center gap-2 mb-6">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    Laporan Kendala Aktif
                </h2>

                @if(!isset($recentTickets) || count($recentTickets) == 0)
                    <div class="py-8 text-center border border-dashed border-slate-700/50 rounded-2xl">
                        <p class="text-slate-500 font-medium text-sm">Tidak ada laporan kendala aktif.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentTickets as $ticket)
                        <div class="flex items-center justify-between p-4 bg-slate-800/40 rounded-2xl border border-slate-700/30">
                            <div class="pr-4">
                                <h4 class="text-white font-bold text-sm truncate max-w-[200px]">{{ $ticket->subject }}</h4>
                                <p class="text-slate-400 text-xs mt-0.5">{{ $ticket->user->name ?? 'Pengguna' }}</p>
                            </div>
                            <button type="button" 
                                class="bg-slate-700/50 text-slate-300 hover:bg-slate-600 hover:text-white px-4 py-2 rounded-xl text-xs font-black transition-colors shrink-0 border border-slate-600/50 uppercase tracking-widest"
                                data-id="{{ $ticket->id }}"
                                data-name="{{ $ticket->user->name ?? 'Pengguna' }}"
                                data-email="{{ $ticket->user->email ?? '-' }}"
                                data-subject="{{ $ticket->subject }}"
                                data-description="{{ $ticket->description ?? 'Tidak ada rincian deskripsi tertulis.' }}"
                                onclick="openTicketModal(this)">
                                TINJAU
                            </button>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl">
                    <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Daftar Vendor Aktif
                    </h3>
                    <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                        @forelse($approvedVendors as $vendor)
                        <div class="bg-slate-800/40 border border-slate-700/40 p-4 rounded-xl flex items-center justify-between gap-2">
                            <div>
                                <div class="font-bold text-white text-sm">{{ $vendor->company_name }}</div>
                                <span class="text-[10px] text-slate-400 font-mono block mt-0.5">{{ $vendor->user->email ?? '-' }}</span>
                            </div>
                            @if(isset($vendor->warnings) && count($vendor->warnings) > 0)
                                <button onclick="toggleWarningLog('log-{{ $vendor->id }}')" class="bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[9px] px-2 py-0.5 rounded-full font-bold">
                                    {{ count($vendor->warnings) }}/3 WARN
                                </button>
                            @endif
                        </div>
                        @empty
                        <p class="text-slate-500 text-xs italic text-center py-4">Belum ada vendor aktif</p>
                        @endforelse
                    </div>
                </div>
            </div>
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
                <div id="pdfContainer" class="flex-grow w-full min-h-[50vh] bg-slate-800/50 rounded-2xl border border-slate-700 overflow-hidden relative"></div>
            </div>
        </div>
        <div class="p-6 border-t border-slate-800 bg-slate-900/90 flex justify-end gap-4">
            <form id="formReject" action="#" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="bg-rose-500/10 text-rose-500 border border-rose-500/20 hover:bg-rose-500 hover:text-white px-8 py-3 rounded-2xl font-black tracking-widest uppercase text-sm"> Tolak Vendor </button>
            </form>
            <form id="formApprove" action="#" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="bg-emerald-500 text-slate-900 hover:bg-emerald-400 px-8 py-3 rounded-2xl font-black tracking-widest uppercase text-sm"> Setujui Kemitraan </button>
            </form>
        </div>
    </div>
</div>

<div id="ticketModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/90 backdrop-blur-md opacity-0 transition-opacity duration-300">
    <div id="ticketModalContent" class="bg-slate-900 border border-slate-700/80 w-11/12 max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col transform scale-95 transition-transform duration-300">
        <div class="flex justify-between items-center p-6 border-b border-slate-800">
            <h2 class="text-2xl font-black text-white flex items-center gap-3">
                <div class="p-2 bg-rose-500/10 text-rose-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                Rincian Laporan Kendala
            </h2>
            <button onclick="closeTicketModal()" class="text-slate-500 hover:text-white bg-slate-800 hover:bg-rose-500 p-2 rounded-full transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-6 space-y-4 overflow-y-auto">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-700/50">
                    <p class="text-xs text-slate-400 mb-1">Nama Pelapor</p>
                    <p id="ticketModalName" class="text-white font-bold text-sm">-</p>
                </div>
                <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-700/50">
                    <p class="text-xs text-slate-400 mb-1">Email Akun</p>
                    <p id="ticketModalEmail" class="text-rose-400 font-mono text-sm">-</p>
                </div>
            </div>
            <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-700/50">
                <p class="text-xs text-slate-400 mb-1">Subjek Masalah</p>
                <p id="ticketModalSubject" class="text-white font-extrabold text-base">-</p>
            </div>
            <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-700/50">
                <p class="text-xs text-slate-400 mb-1">Isi Laporan / Deskripsi Kendala</p>
                <p id="ticketModalDescription" class="text-slate-300 text-sm leading-relaxed whitespace-pre-wrap">-</p>
            </div>
        </div>
        <div class="p-6 border-t border-slate-800 bg-slate-900/90 flex justify-end gap-3">
            <button type="button" onclick="closeTicketModal()" class="bg-slate-800 text-slate-400 border border-slate-700 hover:text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-colors">Kembali</button>
            <form id="formResolveTicket" action="#" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="bg-rose-500 text-white hover:bg-rose-400 px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-sm">Tandai Selesai</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function switchMainTab(tabId) {
        document.querySelectorAll('.main-panel-content').forEach(panel => {
            panel.classList.remove('block'); panel.classList.add('hidden');
        });
        document.querySelectorAll('.main-tab-btn').forEach(btn => {
            btn.classList.remove('text-emerald-400', 'border-emerald-500'); btn.classList.add('text-slate-500', 'border-transparent');
        });
        document.getElementById('panel-' + tabId).classList.remove('hidden');
        document.getElementById('panel-' + tabId).classList.add('block');
        document.getElementById('tabBtn-' + tabId).classList.remove('text-slate-500', 'border-transparent');
        document.getElementById('tabBtn-' + tabId).classList.add('text-emerald-400', 'border-emerald-500');
        sessionStorage.setItem('activeAdminTab', tabId);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const savedTab = sessionStorage.getItem('activeAdminTab');
        if (savedTab && document.getElementById('panel-' + savedTab)) {
            switchMainTab(savedTab);
        }
    });

    // LOGIKA MODAL ADUAN KENDALA (BARU)
    function openTicketModal(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const email = btn.getAttribute('data-email');
        const subject = btn.getAttribute('data-subject');
        const description = btn.getAttribute('data-description');

        document.getElementById('ticketModalName').innerText = name;
        document.getElementById('ticketModalEmail').innerText = email;
        document.getElementById('ticketModalSubject').innerText = subject;
        document.getElementById('ticketModalDescription').innerText = description;

        // Pasang Aksi Route Update Form Dinamis
        document.getElementById('formResolveTicket').action = `/admin/tickets/${id}/resolve`;

        const modal = document.getElementById('ticketModal');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        setTimeout(() => { modal.classList.remove('opacity-0'); document.getElementById('ticketModalContent').classList.remove('scale-95'); }, 10);
    }

    function closeTicketModal() {
        const modal = document.getElementById('ticketModal');
        modal.classList.add('opacity-0'); document.getElementById('ticketModalContent').classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
    }

    function toggleWarningLog(id) {
        const log = document.getElementById(id);
        log.classList.contains('hidden') ? log.classList.remove('hidden') : log.classList.add('hidden');
    }

    function openReviewModal(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const npwp = btn.getAttribute('data-npwp');
        const email = btn.getAttribute('data-email');
        const address = btn.getAttribute('data-address');
        const pdfUrl = btn.getAttribute('data-pdf');

        document.getElementById('modalCompanyName').innerText = name;
        document.getElementById('modalNpwp').innerText = npwp;
        document.getElementById('modalEmail').innerText = email;
        document.getElementById('modalAddress').innerText = address;

        const container = document.getElementById('pdfContainer');
        container.innerHTML = pdfUrl ? `<iframe src="${pdfUrl}" class="w-full h-full border-0 absolute inset-0"></iframe>` : `<div class="absolute inset-0 flex items-center justify-center text-slate-500 font-bold text-sm uppercase">Dokumen Kosong</div>`;

        document.getElementById('formApprove').action = `/admin/vendors/${id}/approve`;
        document.getElementById('formReject').action = `/admin/vendors/${id}/reject`;

        const modal = document.getElementById('reviewModal');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        setTimeout(() => { modal.classList.remove('opacity-0'); document.getElementById('reviewModalContent').classList.remove('scale-95'); }, 10);
    }

    function closeReviewModal() {
        const modal = document.getElementById('reviewModal');
        modal.classList.add('opacity-0'); document.getElementById('reviewModalContent').classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); container.innerHTML = ''; }, 300);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('spkluGrowthChart').getContext('2d');
        let gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#10b981', backgroundColor: gradient, borderWidth: 3, fill: true, tension: 0.4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    });
</script>
@endsection