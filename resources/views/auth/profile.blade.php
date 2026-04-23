@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengaturan Profil</h1>
            <p class="text-gray-600">Kelola informasi akun Anda di platform EV-HUB.</p>
        </div>
        <a href="javascript:history.back()" class="flex py-2 px-5 rounded-md items-center text-sm font-medium bg-green-700 text-white hover:text-white hover:bg-green-800 transition duration-200">
            Kembali
        </a>

    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 border @error('name') border-red-500 @enderror">
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 border @error('email') border-red-500 @enderror">
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 border">
                        </div>

                        
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role Akun</label>
                            <input type="text" value="{{ strtoupper($user->role) }}" disabled
                                class="w-full bg-gray-50 border-gray-200 rounded-md p-2.5 text-gray-500 cursor-not-allowed border">
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wider">Keamanan (Opsional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                <input type="password" name="password" placeholder="Isi hanya jika ingin ganti"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 border">
                                @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 border">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="bg-green-700 hover:bg-green-800 text-white font-bold py-2.5 px-6 rounded-md shadow transition duration-200 cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <div class="bg-white shadow rounded-lg overflow-hidden border border-red-100">
            <div class="p-6 flex flex-col gap-5">
                <div class="flex items-center justify-between gap-5">
                    <div>
                        <h3 class="text-lg font-bold text-red-600">Logout</h3>
                        <p class="text-sm text-gray-500 mt-1">Setelah akun dilogout, Anda tidak akan keluar dari akses layanan EVHUB.</p>
                    </div>
                    <div>
                        <form action="{{ route('logout') }}" method="POST" 
                              onsubmit="return confirm('Apakah Anda yakin ingin logout? Anda akan segera dikeluarkan dari sistem.')">
                            @csrf
                            @method('POST')
                            <button type="submit" class="w-40 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-semibold py-2 px-4 rounded-md transition duration-200 cursor-pointer">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-5">
                    <div>
                        <h3 class="text-lg font-bold text-red-600">Nonaktifkan Akun</h3>
                        <p class="text-sm text-gray-500 mt-1">Setelah akun dinonaktifkan, Anda tidak akan bisa mengakses layanan EV-HUB untuk sementara waktu.</p>
                    </div>
                    <div>
                        <form action="{{ route('profile.destroy') }}" method="POST" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan akun? Anda akan segera dikeluarkan dari sistem.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-40 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-semibold py-2 px-4 rounded-md transition duration-200 cursor-pointer">
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