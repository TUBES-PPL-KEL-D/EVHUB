@extends('layouts.app')

@section('content')

<div class="flex items-center justify-center px-4 bg-gray-800 bg-transparent">
    
    <div class="max-w-md w-full bg-white/90 backdrop-blur-sm p-10 rounded-3xl shadow-2xl">
        <div class="mb-10">
            <h1 class="text-4xl font-extrabold text-[#2D7A84] tracking-tight uppercase">Welcome!</h1>
            <p class="text-2xl text-[#3A6D7E] mt-1 font-medium">Login To Start</p>
        </div>
        
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Notifikasi Error Umum (Misal: Akun Tidak Aktif / Email Salah) --}}
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            
            <div class="mb-5">
                <label class="block text-[#3A6D7E] font-bold mb-2 ml-1">Email</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email') }}" 
                        placeholder="Masukkan email Anda"
                        class="w-full pl-4 pr-12 py-4 bg-[#B8D7DB] border-none rounded-2xl focus:ring-2 focus:ring-[#2D7A84] text-gray-700 placeholder-[#5A8D9E]" 
                        required autofocus>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#2D7A84]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="mb-2">
                <label class="block text-[#3A6D7E] font-bold mb-2 ml-1">Password</label>
                <div class="relative">
                    <input type="password" name="password" 
                        placeholder="Masukkan password Anda"
                        class="w-full pl-4 pr-12 py-4 bg-[#B8D7DB] border-none rounded-2xl focus:ring-2 focus:ring-[#2D7A84] text-gray-700 placeholder-[#5A8D9E]" 
                        required>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#2D7A84]" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- <div class="flex justify-end mb-8">
                <a href="#" class="text-[#3A6D7E] text-sm font-bold underline">Forgot Password?</a>
            </div> -->

            <button type="submit" class="w-full bg-[#4DA1A9] hover:bg-[#3D8A91] text-white py-4 rounded-2xl shadow-lg transition-all duration-200 font-bold text-xl mb-4 mt-8">
                Login
            </button>
        </form>

        <p class="mt-6 text-center text-gray-600 text-sm">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">
                Daftar Sekarang
            </a>
        </p>
    </div>
</div>
@endsection