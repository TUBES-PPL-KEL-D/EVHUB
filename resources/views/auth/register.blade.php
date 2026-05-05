@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[80vh] px-4">
    <div class="max-w-md w-full bg-slate-800/40 backdrop-blur-xl p-10 rounded-3xl shadow-2xl border border-slate-700/50 my-10">
        
        <div class="mb-8 text-center lg:text-left">
            <h1 class="text-4xl font-extrabold text-white tracking-tight uppercase leading-tight">
                Join <span class="text-emerald-500">Us!</span>
            </h1>
            <p class="text-slate-400 mt-2 font-medium">Create your EV-Hub account</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border-l-4 border-rose-500 text-rose-400 text-sm rounded-xl">
                <p class="font-bold mb-1">Terjadi Kesalahan:</p>
                <ul class="list-disc list-inside opacity-90">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-slate-300 font-semibold mb-2 ml-1 text-sm">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" 
                    placeholder="Nama Lengkap Anda"
                    class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white placeholder-slate-500 transition-all outline-none @error('name') border-rose-500 @enderror" 
                    required autofocus>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-2 ml-1 text-sm">Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone') }}" 
                    placeholder="0812xxxx"
                    class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white placeholder-slate-500 transition-all outline-none @error('phone') border-rose-500 @enderror" 
                    required>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-2 ml-1 text-sm">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                    placeholder="email@example.com"
                    class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white placeholder-slate-500 transition-all outline-none @error('email') border-rose-500 @enderror" 
                    required>
            </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-2 ml-1 text-sm">Password</label>
                    <input type="password" name="password" 
                        placeholder="Minimal 8 karakter"
                        class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white placeholder-slate-500 transition-all outline-none @error('password') border-rose-500 @enderror" 
                        required>
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-2 ml-1 text-sm">Konfirmasi</label>
                    <input type="password" name="password_confirmation" 
                        placeholder="Ulangi password"
                        class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white placeholder-slate-500 transition-all outline-none" 
                        required>
                </div>

            <button type="submit" class="w-full bg-emerald-600 from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white py-4 rounded-2xl shadow-lg shadow-emerald-500/20 transition-all duration-300 font-bold text-xl mt-4 hover:-translate-y-1">
                Registrasi
            </button>
        </form>

        <p class="mt-8 text-center text-slate-400 text-sm">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-emerald-400 font-bold hover:text-emerald-300 underline underline-offset-4 decoration-emerald-500/30 transition-colors">
                Masuk di sini
            </a>
        </p>
    </div>
</div>
@endsection