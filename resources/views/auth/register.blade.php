@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center px-4" >
    
    <div class="max-w-md w-full bg-white/90 backdrop-blur-sm p-10 rounded-3xl shadow-2xl my-10">
        <div class="mb-8">
            <h1 class="text-4xl font-extrabold text-[#2D7A84] tracking-tight uppercase leading-tight">Join Us!</h1>
            <p class="text-xl text-[#3A6D7E] mt-1 font-medium">Create your EV-Hub account</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-700 text-sm rounded-2xl border-l-4 border-red-500">
                <p class="font-bold mb-1">Terjadi Kesalahan:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-[#3A6D7E] font-bold mb-1 ml-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" 
                    placeholder="Nama Lengkap Anda"
                    class="w-full px-4 py-3 bg-[#B8D7DB] border-none rounded-2xl focus:ring-2 focus:ring-[#2D7A84] text-gray-700 placeholder-[#5A8D9E] @error('name') ring-2 ring-red-500 @enderror" 
                    required autofocus>
            </div>

            <div class="mb-4">
                <label class="block text-[#3A6D7E] font-bold mb-1 ml-1">Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone') }}" 
                    placeholder="0812xxxx"
                    class="w-full px-4 py-3 bg-[#B8D7DB] border-none rounded-2xl focus:ring-2 focus:ring-[#2D7A84] text-gray-700 placeholder-[#5A8D9E] @error('phone') ring-2 ring-red-500 @enderror" 
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-[#3A6D7E] font-bold mb-1 ml-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                    placeholder="email@example.com"
                    class="w-full px-4 py-3 bg-[#B8D7DB] border-none rounded-2xl focus:ring-2 focus:ring-[#2D7A84] text-gray-700 placeholder-[#5A8D9E] @error('email') ring-2 ring-red-500 @enderror" 
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-[#3A6D7E] font-bold mb-1 ml-1">Password</label>
                <input type="password" name="password" 
                    placeholder="Minimal 8 karakter"
                    class="w-full px-4 py-3 bg-[#B8D7DB] border-none rounded-2xl focus:ring-2 focus:ring-[#2D7A84] text-gray-700 placeholder-[#5A8D9E] @error('password') ring-2 ring-red-500 @enderror" 
                    required>
            </div>

            <div class="mb-8">
                <label class="block text-[#3A6D7E] font-bold mb-1 ml-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" 
                    placeholder="Ulangi password Anda"
                    class="w-full px-4 py-3 bg-[#B8D7DB] border-none rounded-2xl focus:ring-2 focus:ring-[#2D7A84] text-gray-700 placeholder-[#5A8D9E]" 
                    required>
            </div>

            <button type="submit" class="w-full bg-[#4DA1A9] hover:bg-[#3D8A91] text-white py-4 rounded-2xl shadow-lg transition-all duration-200 font-bold text-xl mb-4">
                Registrasi
            </button>
        </form>

        <p class="mt-6 text-center text-[#3A6D7E] font-bold text-sm">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="underline decoration-2 underline-offset-4">Login di sini</a>
        </p>
    </div>
</div>
@endsection