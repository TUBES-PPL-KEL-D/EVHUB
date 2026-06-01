@extends('layouts.app') 

@section('content') 
<div class="space-y-6 animate-fade-in-up pb-10">
    
    <!-- HEADER UTAMA DASHBOARD -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-2">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter">Pusat Kendali Admin</h1>
            <p class="text-slate-400 font-medium mt-1">Satu ruang terintegrasi untuk analitik, verifikasi berkas, dan manajemen operasional.</p>
        </div>
        <div class="bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-full flex items-center">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2"></span>
            <span class="text-emerald-400 text-xs font-bold tracking-widest uppercase">Sistem Operasional</span>
        </div>
    </div>

    <!-- NOTIFIKASI AKSI BERHASIL -->
    @if(session('success')) 
    <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl flex items-center space-x-3 shadow-sm"> 
        <div class="bg-emerald-500 p-1.5 rounded-full text-white"> 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> 
        </div> 
        <p class="font-bold text-emerald-400 text-sm">{{ session('success') }}</p> 
    </div> 
    @endif 

    <!-- SUB-NAVIGASI UTAMA (TABS SYSTEM) -->
    <div class="flex border-b border-slate-700/60 gap-2 mb-6 overflow-x-auto">
        <button id="tabBtn-overview" onclick="switchMainTab('overview')" class="main-tab-btn px-6 py-3.5 font-black text-xs tracking-widest uppercase border-b-2 transition-all duration-200 text-emerald-400 border-emerald-500 shrink-0">
            Overview & Analytics
        </button>
        <button id="tabBtn-verifikasi" onclick="switchMainTab('verifikasi')" class="main-tab-btn px-6 py-3.5 font-black text-xs tracking-widest uppercase border-b-2 transition-all duration-200 text-slate-500 border-transparent hover:text-slate-300 shrink-0">
            Antrean Verifikasi ({{ count($pendingVendors ?? []) }})
        </button>
        <button id="tabBtn-manajemen" onclick="switchMainTab('manajemen')" class="main-tab-btn px-6 py-3.5 font-black text-xs tracking-widest uppercase border-b-2 transition-all duration-200 text-slate-500 border-transparent hover:text-slate-300 shrink-0">
            Manajemen Vendor & Stasiun
        </button>
    </div>

    <!-- ========================================== -->
    <!-- PANEL CONTENT 1: OVERVIEW & ANALYTICS      -->
    <!-- ========================================== -->
    <div id="panel-overview" class="main-panel-content space-y-6 block">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-2xl flex flex-col justify-between">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-white tracking-tight">Tren Pertumbuhan SPKLU</h2>
                        <p class="text-sm text-slate-400 mt-1">Akumulasi pendaftaran stasiun pengisian daya.</p>
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

                <div onclick="switchMainTab('manajemen')" class="bg-slate-900/40 border border-slate-700/50 hover:border-rose-500/40 cursor-pointer rounded-[2rem] p-6 backdrop-blur-xl shadow-xl flex-1 flex flex-col justify-center relative overflow-hidden group transition-all">
                    <div class="absolute -right-6 -top-6 text-rose-500/5 group-hover:text-rose-500/10 transition-colors">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-slate-400 text-sm font-bold tracking-widest uppercase">Laporan Masuk</h3>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-5xl font-black text-white">{{ count($recentTickets ?? []) }}</span>
                            <span class="text-sm text-slate-500 font-medium">Aduan Pengendara</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PANEL CONTENT 2: ANTREAN VERIFIKASI        -->
    <!-- ========================================== -->
    <div id="panel-verifikasi" class="main-panel-content space-y-6 hidden">
        <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                Validasi Berkas Kemitraan Baru
            </h2>
            
            @if(count($pendingVendors ?? []) == 0)
                <div class="py-12 text-center border border-dashed border-slate-700/50 rounded-2xl">
                    <p class="text-slate-500 font-medium text-sm">Tidak ada berkas vendor yang mengantre verifikasi.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($pendingVendors as $vendor)
                    <div class="flex items-center justify-between p-5 bg-slate-800/40 rounded-2xl border border-slate-700/30">
                        <div>
                            <h4 class="text-white font-bold text-base">{{ $vendor->company_name }}</h4>
                            <p class="text-slate-400 text-xs mt-1">NPWP: <span class="font-mono tracking-wider">{{ $vendor->npwp }}</span></p>
                        </div>
                        <button type="button" class="bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-colors border border-blue-500/20" data-id="{{ $vendor->id }}" data-name="{{ $vendor->company_name }}" data-npwp="{{ $vendor->npwp }}" data-email="{{ $vendor->user->email ?? 'Tidak ada email' }}" data-address="{{ $vendor->address ?? 'Alamat belum diisi' }}" data-pdf="{{ $vendor->legality_document_path ? asset('storage/' . $vendor->legality_document_path) : '' }}" onclick="openReviewModal(this)"> TINJAU </button> 
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PANEL CONTENT 3: MANAJEMEN VENDOR & STASIUN-->
    <!-- ========================================== -->
    <div id="panel-manajemen" class="main-panel-content space-y-8 hidden">
        
        <div class="flex justify-between items-center bg-slate-900/30 p-4 border border-slate-700/30 rounded-2xl">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider hidden sm:block">Aksi Peninjauan Komparatif:</p>
            <a href="{{ route('admin.export.spklu') }}" class="inline-flex items-center bg-emerald-500 text-slate-900 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20 w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> 
                Download Berkas Excel
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <!-- BLOK KIRI: ADUAN KENDALA OPERASIONAL -->
            <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 backdrop-blur-xl shadow-xl lg:col-span-1">
                <h2 class="text-lg font-bold text-white flex items-center gap-2 mb-6">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                    Laporan Kendala Aktif
                </h2>
                @if(!isset($recentTickets) || count($recentTickets) == 0)
                    <div class="py-10 text-center border border-dashed border-slate-700/50 rounded-2xl">
                        <p class="text-slate-500 font-medium text-xs uppercase tracking-wider">Aman Terkendali</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentTickets as $ticket)
                        <div class="p-4 bg-slate-800/40 rounded-2xl border border-slate-700/30 flex flex-col xl:flex-row xl:items-center justify-between gap-3">
                            <div class="truncate">
                                <h4 class="text-white font-bold text-sm truncate max-w-[150px]">{{ $ticket->subject }}</h4>
                                <p class="text-slate-400 text-xs mt-0.5">{{ $ticket->user->name ?? 'Pengguna' }}</p>
                            </div>
                            <button type="button" class="bg-slate-700/50 text-slate-300 hover:bg-slate-600 hover:text-white px-4 py-2 rounded-xl text-xs font-black transition-colors border border-slate-600/50 uppercase tracking-widest shrink-0" data-id="{{ $ticket->id }}" data-name="{{ $ticket->user->name ?? 'Pengguna' }}" data-email="{{ $ticket->user->email ?? '-' }}" data-subject="{{ $ticket->subject }}" data-description="{{ $ticket->description ?? 'Tidak ada deskripsi tertulis.' }}" onclick="openTicketModal(this)">TINJAU</button>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- BLOK KANAN: KONTROL OPERASIONAL PENUH -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- 1: VENDOR AKTIF -->
                <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl">
                    <div class="flex items-center space-x-3 mb-6"> 
                        <div class="w-9 h-9 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-400"> 
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> 
                        </div> 
                        <h3 class="text-lg font-bold text-white tracking-tight">Daftar Vendor Aktif</h3> 
                    </div> 

                    <div class="space-y-4"> 
                        @forelse($approvedVendors as $vendor) 
                        <div class="bg-slate-800/40 border border-slate-700/40 p-5 rounded-2xl flex flex-col justify-between gap-4"> 
                            <div class="flex flex-col xl:flex-row justify-between items-center gap-4 w-full">
                                <div class="flex items-center space-x-4 w-full xl:w-auto"> 
                                    <div class="w-11 h-11 bg-emerald-500/10 rounded-xl flex items-center justify-center font-black text-emerald-400 text-base flex-shrink-0"> 
                                        {{ substr($vendor->company_name, 0, 1) }} 
                                    </div> 
                                    <div> 
                                        <div class="font-extrabold text-white text-base">{{ $vendor->company_name }}</div> 
                                        <div class="flex flex-wrap items-center gap-3 mt-1"> 
                                            <span class="text-xs text-slate-400 font-medium">{{ $vendor->user->email ?? '-' }}</span> 
                                            @if(isset($vendor->warnings_count) && $vendor->warnings_count > 0) 
                                                <button onclick="toggleWarningLog('log-{{ $vendor->id }}')" class="bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20 text-[10px] px-2 py-0.5 rounded-full font-bold transition-colors cursor-pointer flex items-center gap-1"> 
                                                    {{ $vendor->warnings_count }}/3 WARNING (LIHAT LOG)
                                                </button> 
                                            @endif 
                                        </div> 
                                    </div> 
                                </div> 

                                <!-- Form Penindakan -->
                                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full xl:w-auto"> 
                                    <form action="{{ route('admin.vendors.warning', $vendor->id) }}" method="POST" class="flex w-full sm:w-auto shadow-sm"> 
                                        @csrf 
                                        <input type="text" name="message" placeholder="Alasan peringatan..." required class="text-xs px-3 py-2 border border-slate-600 rounded-l-xl focus:outline-none focus:border-amber-500 w-full sm:w-44 bg-slate-950/40 text-white placeholder-slate-500"> 
                                        <button type="submit" class="bg-amber-500 text-slate-900 px-4 py-2 rounded-r-xl text-xs font-black uppercase tracking-widest hover:bg-amber-400 transition-colors">WARN</button> 
                                    </form> 

                                    <form action="{{ route('admin.vendors.suspend', $vendor->id) }}" method="POST" class="w-full sm:w-auto"> 
                                        @csrf @method('PATCH') 
                                        <button type="submit" class="bg-rose-500/10 text-rose-400 px-5 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white border border-rose-500/20 transition-colors w-full h-full" onclick="return confirm('Apakah Anda yakin ingin membekukan vendor ini secara paksa?')">SUSPEND</button> 
                                    </form> 
                                </div> 
                            </div>

                            @if(isset($vendor->warnings) && count($vendor->warnings) > 0)
                            <div id="log-{{ $vendor->id }}" class="hidden border-t border-slate-700/50 pt-4 mt-1 bg-slate-950/30 p-4 rounded-xl space-y-2">
                                <p class="text-[10px] font-black uppercase tracking-widest text-amber-400/80 mb-1">Pesan Pelanggaran Terbuku:</p>
                                @foreach($vendor->warnings as $index => $warning)
                                <div class="text-xs text-slate-300 flex justify-between items-start gap-4 border-b border-slate-800/60 pb-2 last:border-none last:pb-0">
                                    <p class="font-medium"><span class="text-slate-500 mr-1">#{{ $index + 1 }}</span> {{ $warning->message }}</p>
                                    <span class="text-slate-500 shrink-0 font-mono text-[10px]">{{ $warning->created_at ? $warning->created_at->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div> 
                        @empty 
                        <div class="text-center py-8 bg-slate-800/20 rounded-2xl border border-dashed border-slate-700 text-slate-500 font-bold text-xs uppercase tracking-widest">Belum ada vendor aktif</div> 
                        @endforelse 
                    </div> 
                </div>

                <!-- 2: DAFTAR BLOKIR/SUSPEND -->
                <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl"> 
                    <div class="flex items-center space-x-3 mb-6"> 
                        <div class="w-9 h-9 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-400"> 
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> 
                        </div> 
                        <h3 class="text-lg font-bold text-white tracking-tight">Daftar Akun Dibekukan</h3> 
                    </div> 
                    <div class="space-y-4"> 
                        @forelse($suspendedVendors as $vendor) 
                        <div class="bg-rose-950/10 border border-rose-500/20 p-5 rounded-2xl flex flex-col md:flex-row justify-between items-center gap-4"> 
                            <div class="flex items-center space-x-4 w-full md:w-auto"> 
                                <div class="w-11 h-11 bg-rose-500/20 text-rose-400 rounded-xl flex items-center justify-center font-black text-base flex-shrink-0">!</div> 
                                <div> 
                                    <div class="font-bold text-white text-base italic">{{ $vendor->company_name }}</div> 
                                    <div class="text-[10px] text-rose-400 font-black tracking-widest uppercase mt-0.5">Status Penangguhan</div> 
                                </div> 
                            </div> 
                            <div class="flex space-x-2 w-full md:w-auto"> 
                                <form action="{{ route('admin.vendors.activate', $vendor->id) }}" method="POST" class="flex-1 md:flex-none"> 
                                    @csrf @method('PATCH') 
                                    <button type="submit" class="w-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-colors whitespace-nowrap">RE_ACTIVATE</button> 
                                </form> 
                                <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" onsubmit="return confirm('Hapus PERMANEN vendor ini beserta seluruh dokumen penunjangnya?');" class="flex-1 md:flex-none"> 
                                    @csrf @method('DELETE') 
                                    <button type="submit" class="w-full bg-slate-800 text-slate-400 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-colors shadow-sm">HAPUS</button> 
                                </form> 
                            </div> 
                        </div> 
                        @empty 
                        <div class="text-center py-8 bg-slate-800/20 rounded-2xl border border-dashed border-slate-700 text-slate-500 font-bold text-xs uppercase tracking-widest">Tidak ada akun yang dibekukan</div> 
                        @endforelse 
                    </div> 
                </div>

                <!-- 3: RIWAYAT PENOLAKAN -->
                <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl"> 
                    <div class="flex items-center space-x-3 mb-6"> 
                        <div class="w-9 h-9 bg-slate-700/50 rounded-xl flex items-center justify-center text-slate-400"> 
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> 
                        </div> 
                        <h3 class="text-lg font-bold text-white tracking-tight">Riwayat Penolakan Berkas</h3> 
                    </div> 
                    <div class="space-y-4"> 
                        @forelse($rejectedVendors as $vendor) 
                        <div class="bg-slate-800/30 border border-slate-700/50 p-5 rounded-2xl flex flex-col md:flex-row justify-between items-center gap-4 hover:bg-slate-800/50 transition-colors"> 
                            <div class="w-full md:w-auto"> 
                                <div class="font-bold text-white text-base">{{ $vendor->company_name }}</div> 
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $vendor->user->email ?? '-' }}</div> 
                            </div> 
                            <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" onsubmit="return confirm('Bersihkan rekam data pendaftaran yang ditolak ini dari sistem?');" class="w-full md:w-auto"> 
                                @csrf @method('DELETE') 
                                <button type="submit" class="w-full bg-rose-500/10 text-rose-400 border border-rose-500/20 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-colors">HAPUS PERMANEN</button> 
                            </form> 
                        </div> 
                        @empty 
                        <div class="text-center py-8 bg-slate-800/20 rounded-2xl border border-dashed border-slate-700 text-slate-500 font-bold text-xs uppercase tracking-widest">Belum ada riwayat penolakan</div> 
                        @endforelse 
                    </div> 
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL POPUP PREVIEW VERIFIKASI VENDOR      -->
<!-- ========================================== -->
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

<!-- ========================================== -->
<!-- MODAL POPUP DETAIL TIKET KENDALA ADUAN     -->
<!-- ========================================== -->
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

<!-- PEMANGGILAN SCRIPT -->
@include('admin.partials.scripts')

@endsection