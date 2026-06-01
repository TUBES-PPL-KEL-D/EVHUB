<div id="panel-manajemen" class="main-panel-content space-y-8 hidden">
    
    <div class="flex justify-between items-center bg-slate-900/30 p-4 border border-slate-700/30 rounded-2xl">
        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider hidden sm:block">Aksi Peninjauan Komparatif:</p>
        <a href="{{ route('admin.export.spklu') }}" class="inline-flex items-center bg-emerald-500 text-slate-900 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20 w-full sm:w-auto justify-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> 
            Download Berkas Excel
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
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

        <div class="lg:col-span-2 space-y-6">
            
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