<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV-HUB | @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex selection:bg-emerald-200 selection:text-emerald-900">

    <!-- THE MERGING BACKGROUND TRICK -->
    <div class="fixed inset-0 z-0 flex">
        <!-- Foto di kiri (Lebar 45%) -->
        <div class="w-full lg:w-[45%] h-full relative">
            <img src="{{ asset('images/bgr.png') }}" alt="EV Background" class="absolute inset-0 w-full h-full object-cover object-left" />
            <div class="absolute inset-0 bg-gradient-to-r from-black/20 via-slate-50/60 to-slate-50"></div>
        </div>
        <!-- Area solid di kanan -->
        <div class="hidden lg:block lg:w-[55%] h-full bg-slate-50"></div>
    </div>

    <!-- Main App Container -->
    <div class="relative z-10 w-full min-h-screen flex flex-col">
        
        <!-- Navbar Premium -->
        <nav class="w-full px-6 py-5 lg:px-12 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-[0_4px_15px_rgba(16,185,129,0.3)]">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="text-2xl font-extrabold tracking-tighter text-slate-900 drop-shadow-sm">EV-HUB</span>
            </div>
            
            <div class="flex items-center space-x-8 text-sm font-bold text-slate-500">
                
                <!-- MENU INI SEMENTARA DITAMPILKAN SECARA PAKSA UNTUK TESTING -->
                <a href="{{ route('rider.map') }}" class="{{ request()->routeIs('rider.map') ? 'text-emerald-600 border-b-2 border-emerald-500 pb-1' : 'hover:text-emerald-600 transition' }}">Peta SPKLU</a>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-emerald-600 border-b-2 border-emerald-500 pb-1' : 'hover:text-emerald-600 transition' }}">Verifikasi</a>
                <a href="{{ route('admin.stations') }}" class="{{ request()->routeIs('admin.stations') ? 'text-emerald-600 border-b-2 border-emerald-500 pb-1' : 'hover:text-emerald-600 transition' }}">Riwayat Stasiun</a>
                
                <!-- DROPDOWN PROFIL INTERAKTIF (BYPASS AUTH) -->
                <div class="relative group">
                    <div class="h-10 w-10 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center text-emerald-600 font-extrabold ml-4 cursor-pointer hover:bg-slate-50 transition">
                        <!-- Jika Login: Huruf Awal Nama. Jika Tidak: 'T' (Tester) -->
                        {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'T' }}
                    </div>
                    
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-slate-100 mb-1">
                                <p class="text-xs text-slate-400">Status Akses:</p>
                                <!-- Jika Login: Nama Asli. Jika Tidak: Tulisan Tester Mode -->
                                <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::check() ? Auth::user()->name : 'Tester Mode' }}</p>
                            </div>
                            
                            @auth
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 font-bold transition">Pengaturan Akun</a>
                            <div class="border-t border-slate-100 my-1"></div>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 font-bold transition">Keluar Sistem</button>
                            </form>
                            @else
                            <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-emerald-600 hover:bg-emerald-50 font-bold transition">Login Sekarang</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Area -->
        <main class="w-full lg:w-[65%] lg:ml-auto px-6 lg:px-12 py-8 flex-grow">
            @yield('content')
        </main>
        
    </div>

</body>
</html>