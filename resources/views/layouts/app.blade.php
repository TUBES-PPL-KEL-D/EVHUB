<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV-HUB | Ekosistem Kendaraan Listrik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #059669; }
    </style>
</head>
<body class="text-slate-300 antialiased min-h-screen flex flex-col relative selection:bg-emerald-500 selection:text-white bg-slate-900 overflow-x-hidden">

    <!-- Fixed Background -->
    <div class="fixed inset-0 z-[-1]">
        <img src="{{ asset('images/bgr.png') }}" alt="EV Background" class="w-full h-full object-cover opacity-30 mix-blend-overlay">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/90 via-slate-900/95 to-slate-900"></div>
    </div>

    @php
        // Trik Pre-Production: Anggap 'logged in' jika benar-benar Auth::check() 
        // ATAU jika URL saat ini bukan root ('/')
        $isSimulatedLogin = auth()->check() || !request()->is('/');
    @endphp

    <!-- Navigation Bar -->
    <nav class="w-full px-6 py-4 flex justify-between items-center sticky top-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-slate-800/50 transition-all">
        <!-- Bagian Kiri: Logo -->
        <a href="/" class="flex items-center space-x-3 group cursor-pointer z-20">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:shadow-emerald-500/50 transition-all duration-300">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <span class="text-xl font-bold tracking-tighter text-white drop-shadow-md">EV-HUB</span>
        </a>
        
        <!-- Bagian Tengah: Menu Navigasi (Muncul jika simulated login aktif) -->
        @if($isSimulatedLogin)
        <div class="hidden lg:flex items-center space-x-8 absolute left-1/2 -translate-x-1/2 z-10">
            <a href="{{ url('admin/dashboard') }}" class="text-slate-300 hover:text-emerald-400 font-medium transition-colors text-sm">Dashboard Admin</a>
            <a href="{{ url('rider/peta') }}" class="text-slate-300 hover:text-emerald-400 font-medium transition-colors text-sm">Peta SPKLU</a>
            <a href="{{ url('rider/vehicles') }}" class="text-slate-300 hover:text-emerald-400 font-medium transition-colors text-sm">Garasi Digital</a>
            <a href="{{ url('rider/transactions') }}" class="text-slate-300 hover:text-emerald-400 font-medium transition-colors text-sm">Riwayat Transaksi</a>
        </div>
        @endif

        <!-- Bagian Kanan: Aksi Akun -->
        <div class="flex items-center space-x-4 z-20">
            @if($isSimulatedLogin)
                <!-- Menu jika simulasi login -->
                <a href="{{ url('profile') }}" class="text-slate-300 hover:text-emerald-400 font-semibold px-4 py-2 transition-colors text-sm">Profil</a>
                
                <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 inline">
                    @csrf
                    <button type="button" onclick="window.location.href='/'" class="bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white px-5 py-2 rounded-full text-sm font-bold transition-all border border-rose-500/20 shadow-lg hover:shadow-rose-500/20">
                        Keluar
                    </button>
                </form>
            @else
                <!-- Menu jika di halaman utama (Guest) -->
                <a href="{{ route('login') }}" class="text-slate-300 hover:text-emerald-400 font-bold px-4 py-2.5 transition-colors text-sm">Masuk</a>
                <a href="{{ route('register') }}" class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white px-6 py-2.5 rounded-full text-sm font-bold transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5">Daftar Sekarang</a>
            @endif
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow w-full max-w-7xl mx-auto px-6 py-8 relative z-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950/50 py-6 text-center text-slate-500 text-sm mt-auto relative z-10 backdrop-blur-sm">
        &copy; 2026 EV-HUB. Kelompok D - S1 Sistem Informasi.
    </footer>

</body>
</html>