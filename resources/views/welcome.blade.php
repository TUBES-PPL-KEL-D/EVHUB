<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV-HUB | Ekosistem Kendaraan Listrik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col relative selection:bg-emerald-200 selection:text-emerald-900">

    <div class="fixed inset-0 z-[-1]">
        <img src="{{ asset('images/bgr.png') }}" alt="EV Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>
    </div>

    <nav class="w-full px-6 py-6 md:px-12 flex justify-between items-center relative z-10">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <span class="text-2xl font-extrabold tracking-tighter text-white drop-shadow-md">EV-HUB</span>
        </div>
        
        <div class="flex space-x-4">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2.5 rounded-full text-sm font-bold transition-all shadow-lg">Masuk Sistem</a>
            @else
                <a href="{{ route('login') }}" class="text-white hover:text-emerald-400 font-bold px-4 py-2.5 transition-colors">Masuk</a>
                <a href="{{ route('register') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2.5 rounded-full text-sm font-bold transition-all shadow-lg">Daftar Sekarang</a>
            @endauth
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center relative z-10 px-6 text-center">
        <div class="max-w-3xl">
            <p class="text-emerald-400 font-bold tracking-[0.2em] uppercase mb-4">Command Center & Ecosystem</p>
            <h1 class="text-5xl md:text-7xl font-extrabold text-white tracking-tight mb-6">
                Masa Depan <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400">Mobilitas Elektrik</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-300 mb-10 font-medium leading-relaxed">
                Platform terpadu untuk pencarian stasiun pengisian daya (SPKLU), manajemen garasi digital, dan kemitraan vendor EV di seluruh Indonesia.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-4 rounded-full text-base font-bold transition-all shadow-[0_0_20px_rgba(16,185,129,0.4)] hover:-translate-y-1">
                    Mulai Perjalanan
                </a>
            </div>
        </div>
    </main>

    <footer class="py-6 text-center text-slate-400 text-sm relative z-10">
        &copy; 2026 EV-HUB. Kelompok D - S1 Sistem Informasi.
    </footer>

</body>
</html>