<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV-HUB | Ekosistem Kendaraan Listrik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fallback Tailwind CDN just in case Vite isn't running yet -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #059669; }

        /* Scroll Reveal Animation Classes */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Delay utilities for grid items */
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col relative selection:bg-emerald-500 selection:text-white bg-slate-900 overflow-x-hidden">

    <!-- Fixed Background -->
    <div class="fixed inset-0 z-[-1]">
        <img src="{{ asset('images/bgr.png') }}" alt="EV Background" class="w-full h-full object-cover opacity-40 mix-blend-overlay">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-slate-900/95 to-slate-900"></div>
    </div>

    <!-- Navigation -->
    <nav class="w-full px-6 py-6 md:px-12 flex justify-between items-center sticky top-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-slate-800/50 transition-all">
        <div class="flex items-center space-x-3 group cursor-pointer">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:shadow-emerald-500/50 transition-all duration-300">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <span class="text-2xl font-extrabold tracking-tighter text-white drop-shadow-md">EV-HUB</span>
        </div>
        
        <div class="flex items-center space-x-4">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2.5 rounded-full text-sm font-bold transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40">Masuk Sistem</a>
            @else
                <a href="{{ route('login') }}" class="text-slate-300 hover:text-emerald-400 font-bold px-4 py-2.5 transition-colors">Masuk</a>
                <a href="{{ route('register') }}" class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white px-6 py-2.5 rounded-full text-sm font-bold transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5">Daftar Sekarang</a>
            @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative min-h-[90vh] flex items-center justify-center px-6 text-center z-10 pt-10 pb-20">
        <div class="max-w-4xl reveal active">
            <div class="inline-flex items-center space-x-2 bg-slate-800/50 border border-slate-700/50 rounded-full px-4 py-1.5 mb-8 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-slate-300 text-sm font-semibold tracking-wide">Platform Pengisian Daya Pintar No. 1</span>
            </div>
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold text-white tracking-tight mb-8 leading-tight">
                Masa Depan <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-cyan-400 to-blue-500">Mobilitas Elektrik</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 mb-10 font-medium leading-relaxed max-w-2xl mx-auto">
                Platform terpadu untuk pencarian stasiun pengisian daya (SPKLU), manajemen garasi digital, dan kemitraan ekosistem EV di Bandung.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto bg-white text-slate-900 hover:bg-slate-100 px-8 py-4 rounded-full text-base font-bold transition-all hover:-translate-y-1">
                    Mulai Perjalanan
                </a>
                <a href="#features" class="w-full sm:w-auto bg-slate-800/50 hover:bg-slate-800 text-white border border-slate-700 px-8 py-4 rounded-full text-base font-bold transition-all hover:-translate-y-1 backdrop-blur-sm">
                    Pelajari Fitur ↓
                </a>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-24 px-6 md:px-12 relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 reveal">
                <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">Infrastruktur Lengkap dalam Satu Genggaman</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">Nikmati kemudahan mengelola kendaraan listrik Anda, dari pencarian stasiun hingga pemantauan daya secara real-time.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="bg-slate-800/40 border border-slate-700/50 p-8 rounded-3xl backdrop-blur-sm hover:bg-slate-800/60 transition-colors group reveal delay-100">
                    <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Peta SPKLU Cerdas</h3>
                    <p class="text-slate-400 leading-relaxed">Temukan stasiun pengisian daya terdekat lengkap dengan status ketersediaan mesin dan spesifikasi konektor.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-slate-800/40 border border-slate-700/50 p-8 rounded-3xl backdrop-blur-sm hover:bg-slate-800/60 transition-colors group reveal delay-200">
                    <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Garasi Digital</h3>
                    <p class="text-slate-400 leading-relaxed">Simpan dan kelola data kendaraan EV Anda. Sistem akan mencocokkan tipe konektor mobil Anda dengan SPKLU secara otomatis.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-slate-800/40 border border-slate-700/50 p-8 rounded-3xl backdrop-blur-sm hover:bg-slate-800/60 transition-colors group reveal delay-300">
                    <div class="w-14 h-14 bg-purple-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Manajemen Vendor</h3>
                    <p class="text-slate-400 leading-relaxed">Punya SPKLU? Daftarkan bisnis Anda sebagai mitra vendor. Kelola mesin, atur status operasional, dan jangkau lebih banyak pengendara.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats/Impact Section -->
    <section class="py-20 relative z-10 border-y border-slate-800/80 bg-slate-900/50">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="reveal">
                    <div class="text-4xl md:text-5xl font-extrabold text-white mb-2">500+</div>
                    <div class="text-emerald-400 font-semibold tracking-wider text-sm uppercase">Titik SPKLU</div>
                </div>
                <div class="reveal delay-100">
                    <div class="text-4xl md:text-5xl font-extrabold text-white mb-2">10k+</div>
                    <div class="text-emerald-400 font-semibold tracking-wider text-sm uppercase">Pengguna Aktif</div>
                </div>
                <div class="reveal delay-200">
                    <div class="text-4xl md:text-5xl font-extrabold text-white mb-2">50+</div>
                    <div class="text-emerald-400 font-semibold tracking-wider text-sm uppercase">Mitra Vendor</div>
                </div>
                <div class="reveal delay-300">
                    <div class="text-4xl md:text-5xl font-extrabold text-white mb-2">24/7</div>
                    <div class="text-emerald-400 font-semibold tracking-wider text-sm uppercase">Sistem Monitoring</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 px-6 relative z-10 text-center">
        <div class="max-w-4xl mx-auto bg-gradient-to-br from-emerald-900/40 to-slate-800/40 border border-emerald-500/20 rounded-[3rem] p-10 md:p-16 backdrop-blur-md reveal">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Siap Beralih ke Ekosistem Digital?</h2>
            <p class="text-slate-300 text-lg mb-10 max-w-2xl mx-auto">Bergabunglah bersama ribuan pengendara EV lainnya. Daftarkan kendaraan Anda dan nikmati perjalanan tanpa batas kecemasan jarak tempuh.</p>
            <a href="{{ route('register') }}" class="inline-block bg-emerald-500 hover:bg-emerald-400 text-slate-900 px-10 py-4 rounded-full text-lg font-bold transition-all shadow-[0_0_30px_rgba(16,185,129,0.3)] hover:shadow-[0_0_40px_rgba(16,185,129,0.5)] hover:-translate-y-1">
                Buat Akun Gratis
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-800 bg-slate-950/80 pt-12 pb-8 px-6 relative z-10">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span class="text-xl font-bold text-white">EV-HUB</span>
            </div>
            <div class="text-slate-400 text-sm font-medium">
                &copy; 2026 EV-HUB. All rights reserved.
            </div>
            <div class="flex space-x-4">
                <a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Kebijakan Privasi</a>
                <a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Syarat & Ketentuan</a>
            </div>
        </div>
    </footer>

    <!-- Intersection Observer Script for Scroll Animations -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        // Optional: unobserve after animating once
                        // observer.unobserve(entry.target); 
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach(element => {
                observer.observe(element);
            });
        });
    </script>
</body>
</html>