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