@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[80vh] px-4">
    
    <!-- Card Utama dengan Efek Glassmorphism -->
    <div class="max-w-md w-full bg-slate-800/40 backdrop-blur-xl p-10 rounded-3xl shadow-2xl border border-slate-700/50">
        <div class="mb-10 text-center lg:text-left">
            <h1 class="text-4xl font-extrabold text-white tracking-tight uppercase leading-tight">
                Welcome <span class="text-emerald-500">Back!</span>
            </h1>
            <p class="text-slate-400 mt-2 font-medium text-lg">Login to start your journey</p>
        </div>
        
        <!-- Notifikasi Sukses -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 text-sm rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <!-- Notifikasi Error -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 text-sm rounded-xl">
                <ul class="list-disc list-inside opacity-90">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Field Email -->
            <div>
                <label class="block text-slate-300 font-semibold mb-2 ml-1 text-sm">Email</label>
                <div class="relative group">
                    <input type="email" name="email" value="{{ old('email') }}" 
                        placeholder="email@example.com"
                        class="w-full pl-4 pr-12 py-4 bg-slate-900/50 border border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white placeholder-slate-500 transition-all outline-none" 
                        required autofocus>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Field Password -->
            <div>
                <label class="block text-slate-300 font-semibold mb-2 ml-1 text-sm">Password</label>
                <div class="relative group">
                    <input type="password" name="password" 
                        placeholder="••••••••"
                        class="w-full pl-4 pr-12 py-4 bg-slate-900/50 border border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white placeholder-slate-500 transition-all outline-none" 
                        required>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26a4 4 0 015.486 5.486L7.968 6.553z" clip-rule="evenodd" />
                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tombol Login Minimalis Pop Out -->
            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-900 py-4 rounded-2xl font-bold text-xl mt-4 transition-all duration-300 shadow-[0_0_20px_rgba(16,185,129,0.4)] hover:shadow-[0_0_30px_rgba(16,185,129,0.7)] hover:-translate-y-1 active:scale-95">
                Login
            </button>
        </form>

        <p class="mt-8 text-center text-slate-400 text-sm">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-emerald-400 font-bold hover:text-emerald-300 underline underline-offset-4 decoration-emerald-500/30 transition-colors">
                Daftar Sekarang
            </a>
        </p>
    </div>
</div>
@endsection