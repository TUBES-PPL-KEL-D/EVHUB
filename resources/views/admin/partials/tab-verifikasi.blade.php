<div id="panel-verifikasi" class="main-panel-content space-y-6 hidden">
    <div class="bg-slate-900/40 border border-slate-700/50 rounded-[2rem] p-6 md:p-8 backdrop-blur-xl shadow-xl">
        <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            Validasi Berkas Kemitraan Baru
        </h2>
        
        @if(count($pendingVendors ?? []) == 0)
            <div class="py-12 text-center border border-dashed border-slate-700/50 rounded-2xl">
                <p class="text-slate-500 font-medium text-sm">Tidak ada berkas vendor yang mengantre verifikasi saat ini.</p>
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