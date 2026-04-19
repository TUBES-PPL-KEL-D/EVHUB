@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-left text-blue-600 mb-6">Daftar Akun Baru</h2>
        
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                <p class="font-bold">Terjadi Kesalahan!</p>
                <ul class="list-disc ml-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                @error('phone')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Register
            </button>
        </form>

        <p class="mt-6 text-center text-gray-600">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">
                Login di sini
            </a>
        </p>
    </div>
</div>
@endsection