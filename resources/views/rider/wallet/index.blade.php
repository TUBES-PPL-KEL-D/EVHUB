@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 border-b border-slate-800/60 pb-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Dompet Digital EV-Pay</h1>
            <p class="text-slate-400 text-sm mt-1">Kelola saldo simulasi untuk kemudahan pengisian daya kendaraan listrik Anda.</p>
        </div>
        <div class="flex-shrink-0">
            <a href="javascript:history.back()" class="flex py-2.5 px-6 rounded-xl items-center text-sm font-bold bg-slate-800 text-white border border-slate-700 hover:bg-slate-700 hover:border-emerald-500/50 transition-all duration-300 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl text-sm flex items-center space-x-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-4 py-3 rounded-xl text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-6">
            
            <div class="relative bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 border border-slate-800 shadow-xl overflow-hidden group">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>
                
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Total Saldo Aktif</p>
                        <h2 class="text-4xl font-extrabold text-white mt-2 tracking-tight">
                            Rp{{ number_format($user->balance, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="bg-emerald-500/10 p-3 rounded-xl border border-emerald-500/20 text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-between items-center text-xs text-slate-500 border-t border-slate-800/60 pt-4">
                    <span>Pemilik: {{ $user->name }}</span>
                    <!-- <span class="bg-slate-800 px-2 py-1 rounded text-slate-400">Simulasi Sandbox</span> -->
                </div>
            </div>

            <div class="bg-slate-950/40 border border-slate-800/80 backdrop-blur-sm rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">Isi Ulang Saldo Instan</h3>
                
                <form action="{{ route('rider.wallet.topup') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="amount" class="block text-xs font-medium text-slate-400 mb-2">Masukkan Nominal Top-Up (Rupiah)</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-slate-500 font-semibold text-sm">Rp</span>
                            </div>
                            <input type="number" name="amount" id="amount" min="10000" placeholder="Contoh: 50000" required
                                class="block w-full pl-10 pr-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm transition-all">
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs font-medium text-slate-500 mb-2">Pilihan Cepat</span>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="setAmount(25000)" class="py-2 px-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 rounded-lg text-xs font-medium transition-colors">Rp25.000</button>
                            <button type="button" onclick="setAmount(50000)" class="py-2 px-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 rounded-lg text-xs font-medium transition-colors">Rp50.000</button>
                            <button type="button" onclick="setAmount(100000)" class="py-2 px-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 rounded-lg text-xs font-medium transition-colors">Rp100.000</button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:to-emerald-500 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 flex justify-center items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="curentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Konfirmasi Top-Up</span>
                    </button>
                </form>
            </div>

        </div>

        <div class="md:col-span-1">
            <div class="bg-slate-950/40 border border-slate-800/80 backdrop-blur-sm rounded-2xl p-6 shadow-xl h-full flex flex-col">
                <h3 class="text-md font-bold text-white mb-4 flex items-center justify-between">
                    <span>Aktivitas Dompet</span>
                    <span class="text-xs font-normal text-slate-500">Terbaru</span>
                </h3>

                <div class="space-y-4 overflow-y-auto max-h-[350px] pr-1 flex-grow">
                    @forelse($histories as $log)
                        <div class="flex justify-between items-center bg-slate-900/50 border border-slate-800/50 p-3 rounded-xl text-xs">
                            <div class="space-y-1">
                                <p class="font-semibold text-white capitalize">
                                    {{ $log->type == 'topup' ? 'Isi Saldo (Top Up)' : 'Pembayaran Daya' }}
                                </p>
                                <p class="text-[10px] text-slate-500">{{ $log->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="font-bold {{ $log->type == 'topup' ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $log->type == 'topup' ? '+' : '-' }}Rp{{ number_format($log->amount, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 my-auto">
                            <svg class="w-8 h-8 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            <p class="text-slate-600 text-xs">Belum ada riwayat transaksi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi pembantu tombol cepat nominal
    function setAmount(value) {
        document.getElementById('amount').value = value;
    }
</script>
@endsection