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