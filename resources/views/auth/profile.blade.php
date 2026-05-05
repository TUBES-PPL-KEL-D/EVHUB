@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Pengaturan <span class="text-emerald-500">Profil</span></h1>
            <p class="text-slate-400 mt-1">Kelola informasi akun Anda di platform EV-HUB.</p>
        </div>
        <a href="javascript:history.back()" class="flex py-2.5 px-6 rounded-xl items-center text-sm font-bold bg-slate-800 text-white border border-slate-700 hover:bg-slate-700 hover:border-emerald-500/50 transition-all duration-300 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 rounded-xl shadow-sm backdrop-blur-md">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="space-y-8">
        <div class="bg-slate-800/40 backdrop-blur-xl rounded-3xl shadow-2xl border border-slate-700/50 overflow-hidden">
            <div class="p-8">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-slate-300 mb-2 ml-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white placeholder-slate-500 p-3.5 transition-all outline-none @error('name') border-rose-500 @enderror">
                            @error('name') <p class="mt-2 text-xs text-rose-400 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-slate-300 mb-2 ml-1">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white placeholder-slate-500 p-3.5 transition-all outline-none @error('email') border-rose-500 @enderror">
                            @error('email') <p class="mt-2 text-xs text-rose-400 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-slate-300 mb-2 ml-1">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                                class="w-full bg-slate-900/50 border border-slate-600 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white p-3.5 outline-none transition-all">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-slate-300 mb-2 ml-1 text-opacity-60">Role Akun</label>
                            <div class="w-full bg-slate-900/80 border border-slate-700/50 rounded-2xl p-3.5 text-emerald-500 font-bold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                {{ strtoupper($user->role) }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 pt-8 border-t border-slate-700/50">
                        <h3 class="text-xs font-black text-emerald-500 mb-6 uppercase tracking-[0.2em]">Keamanan (Opsional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-slate-300 mb-2 ml-1">Password Baru</label>
                                <input type="password" name="password" placeholder="Isi jika ingin ganti"
                                    class="w-full bg-slate-900/50 border border-slate-600 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white p-3.5 outline-none transition-all">
                                @error('password') <p class="mt-2 text-xs text-rose-400 ml-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-300 mb-2 ml-1">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                                    class="w-full bg-slate-900/50 border border-slate-600 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-white p-3.5 outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-end">
                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-black py-4 px-10 rounded-2xl shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all duration-300 transform hover:-translate-y-1 active:scale-95 cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-rose-500/5 backdrop-blur-xl rounded-3xl shadow-2xl border border-rose-500/20 overflow-hidden">
            <div class="p-8">
                <h3 class="text-xs font-black text-rose-500 mb-8 uppercase tracking-[0.2em]">Danger Zone</h3>
                
                <div class="space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between p-6 bg-slate-900/40 rounded-2xl border border-slate-700/50 gap-4">
                        <div>
                            <h4 class="text-lg font-bold text-white">Keluar Sesi</h4>
                            <p class="text-sm text-slate-400 mt-1">Keluar dari akun Anda di perangkat ini.</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" 
                              onsubmit="return confirm('Apakah Anda yakin ingin logout?')">
                            @csrf
                            <button type="submit" class="w-full md:w-40 bg-slate-800 hover:bg-slate-700 text-white border border-slate-600 font-bold py-3 px-4 rounded-xl transition-all duration-300 cursor-pointer">
                                Logout
                            </button>
                        </form>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between p-6 bg-rose-500/10 rounded-2xl border border-rose-500/20 gap-4">
                        <div>
                            <h4 class="text-lg font-bold text-rose-400">Nonaktifkan Akun</h4>
                            <p class="text-sm text-slate-400 mt-1">Setelah dinonaktifkan, akun tidak dapat diakses untuk sementara waktu.</p>
                        </div>
                        <form action="{{ route('profile.destroy') }}" method="POST" 
                              onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menonaktifkan akun?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full md:w-40 bg-rose-600 hover:bg-rose-500 text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 shadow-lg shadow-rose-900/20 cursor-pointer">
                                Nonaktifkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection