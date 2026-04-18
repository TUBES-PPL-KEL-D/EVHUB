<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV-HUB | @yield('title', 'Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Trik agar scrollbar estetik */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex selection:bg-emerald-200 selection:text-emerald-900">

    <div class="fixed inset-0 z-0 flex">
        <div class="w-full lg:w-[45%] h-full relative">
            
            <img src="{{ asset('images/bgr.png') }}" alt="EV Background" class="absolute inset-0 w-full h-full object-cover object-left" />
            
            <div class="absolute inset-0 bg-gradient-to-r from-black/20 via-slate-50/60 to-slate-50"></div>
        </div>
        <div class="hidden lg:block lg:w-[55%] h-full bg-slate-50"></div>
    </div>

    <div class="relative z-10 w-full min-h-screen flex flex-col">
        
        <nav class="w-full px-6 py-5 lg:px-12 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-[0_4px_15px_rgba(16,185,129,0.3)]">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="text-2xl font-extrabold tracking-tighter text-slate-900 drop-shadow-sm">EV-HUB</span>
            </div>
            
            <div class="flex items-center space-x-8 text-sm font-bold text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-emerald-600 border-b-2 border-emerald-500 pb-1' : 'hover:text-emerald-600 transition' }}">Verifikasi</a>
                <a href="{{ route('admin.stations') }}" class="{{ request()->routeIs('admin.stations') ? 'text-emerald-600 border-b-2 border-emerald-500 pb-1' : 'hover:text-emerald-600 transition' }}">Riwayat Stasiun</a>
                
                <div class="h-10 w-10 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center text-emerald-600 font-extrabold ml-4 cursor-pointer hover:bg-slate-50 transition">
                    A
                </div>
            </div>
        </nav>

        <main class="w-full lg:w-[65%] lg:ml-auto px-6 lg:px-12 py-8 flex-grow">
            @yield('content')
        </main>
        
    </div>

</body>
</html>