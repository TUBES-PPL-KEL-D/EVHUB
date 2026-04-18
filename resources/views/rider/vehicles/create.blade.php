@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Tambah Kendaraan EV
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Masukkan detail kendaraan listrik Anda ke dalam Garasi Digital.
            </p>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('vehicles.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-3">
                        <label for="merk" class="block text-sm font-medium text-gray-700">Merek Kendaraan</label>
                        <input type="text" name="merk" id="merk" placeholder="Contoh: Hyundai" required 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                        <input type="text" name="model" id="model" placeholder="Contoh: Ioniq 5" required 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <label for="license_plate" class="block text-sm font-medium text-gray-700">Plat Nomor</label>
                        <input type="text" name="license_plate" id="license_plate" placeholder="Contoh: D 1234 ABC" required 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('vehicles.index') }}" 
                        class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                        Batal
                    </a>
                    <button type="submit" 
                        class="bg-blue-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Kendaraan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection