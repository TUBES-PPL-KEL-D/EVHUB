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

            <div>
                <label class="block text-slate-300 font-semibold mb-2 ml-1 text-sm">Daftar Sebagai</label>
                <select name="role" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white transition-all outline-none cursor-pointer appearance-none @error('role') border-rose-500 @enderror" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                    <option value="rider" class="bg-slate-800 text-white">Pengendara Kendaraan Listrik (Rider)</option>
                    <option value="vendor" class="bg-slate-800 text-white">Penyedia Stasiun SPKLU (Vendor/Mitra)</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white py-4 rounded-2xl shadow-lg shadow-emerald-500/20 transition-all duration-300 font-bold text-xl mt-4 hover:-translate-y-1">
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