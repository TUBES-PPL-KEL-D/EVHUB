@extends('layouts.app')

@section('title', 'Edit Mesin Charger')

@php
    // Memecah string "08:00 - 17:00" menjadi array berdasarkan delimiter " - "
    $hours = explode(' - ', $charger->operational_hours);
    $openTime = isset($hours[0]) ? trim($hours[0]) : '';
    $closeTime = isset($hours[1]) ? trim($hours[1]) : '';
@endphp

@section('content')
<div class="vendor-scope">
    <div class="mx-auto max-w-4xl">
        
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="mt-2 text-3xl font-bold text-white drop-shadow-[0_2px_6px_rgba(0,0,0,0.45)]">Edit Mesin Charger</h1>
            <p class="mt-2 text-slate-200 drop-shadow-[0_1px_3px_rgba(0,0,0,0.45)]">Perbarui detail spesifikasi atau status operasional mesin Anda.</p>
        </div>

        <!-- Alert Errors -->
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
                <p class="font-semibold">Ada kesalahan pada form:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Form Update Infrastruktur</h2>
            </div>

            <!-- FORM START -->
            <form action="{{ route('vendor.chargers.update', $charger->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 px-6 py-6">
                @csrf
                @method('PUT')

                <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-5">
                    <div class="grid gap-6 md:grid-cols-2">
                        
                        <!-- Info SPKLU (Read Only - Tidak bisa diubah) -->
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-700">Lokasi SPKLU Terhubung</label>
                            <input type="text" value="{{ $charger->spklu->name ?? 'Tidak diketahui' }} - {{ $charger->spklu->address ?? '' }}" class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-500 outline-none cursor-not-allowed" disabled>
                            <p class="mt-1 text-xs text-slate-400">Lokasi SPKLU terikat secara permanen dan tidak dapat diubah di halaman ini.</p>
                        </div>

                        <!-- Info Mesin -->
                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama/Model Mesin</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $charger->name) }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" required>
                        </div>

                        <div>
                            <label for="connector_type" class="mb-2 block text-sm font-medium text-slate-700">Tipe Konektor (Standar Internasional) <span class="text-red-500">*</span></label>
                            <select name="connector_type" id="connector_type" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" required>
                                <option value="" disabled>-- Pilih Standar Konektor --</option>
                                @php $currentConnector = old('connector_type', $charger->connector_type); @endphp
                                <option value="Type 1" {{ $currentConnector == 'Type 1' ? 'selected' : '' }}>Type 1 (J1772) - AC</option>
                                <option value="Type 2" {{ $currentConnector == 'Type 2' ? 'selected' : '' }}>Type 2 (Mennekes) - AC</option>
                                <option value="CCS1" {{ $currentConnector == 'CCS1' ? 'selected' : '' }}>CCS1 - DC Fast Charging</option>
                                <option value="CCS2" {{ $currentConnector == 'CCS2' ? 'selected' : '' }}>CCS2 - DC Fast Charging</option>
                                <option value="CHAdeMO" {{ $currentConnector == 'CHAdeMO' ? 'selected' : '' }}>CHAdeMO - DC Fast Charging</option>
                                <option value="GB/T" {{ $currentConnector == 'GB/T' ? 'selected' : '' }}>GB/T - Standar China (AC/DC)</option>
                                <option value="NACS" {{ $currentConnector == 'NACS' ? 'selected' : '' }}>NACS (Tesla) - AC/DC</option>
                            </select>
                        </div>

                        <div>
                            <label for="capacity_kw" class="mb-2 block text-sm font-medium text-slate-700">Kapasitas (kW)</label>
                            <input type="number" name="capacity_kw" id="capacity_kw" value="{{ old('capacity_kw', $charger->capacity_kw) }}" min="1" step="0.01" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" required>
                        </div>

                        <div>
                            <label for="price_per_kwh" class="mb-2 block text-sm font-medium text-slate-700">Harga per kWh (Rp)</label>
                            <input type="number" name="price_per_kwh" id="price_per_kwh" value="{{ old('price_per_kwh', $charger->price_per_kwh) }}" min="0" step="0.01" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" required>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Jam Operasional <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2">
                                <input type="time" name="open_time" id="open_time" value="{{ old('open_time', $openTime) }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" required>
                                <span class="text-slate-500 font-medium">s/d</span>
                                <input type="time" name="close_time" id="close_time" value="{{ old('close_time', $closeTime) }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" required>
                            </div>
                        </div>

                        <!-- Status Mesin (PBI 17) -->
                        <div>
                            <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status Operasional <span class="text-red-500">*</span></label>
                            <select name="status" id="status" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" required>
                                <option value="available" {{ old('status', $charger->status) == 'available' ? 'selected' : '' }}>🟢 Available (Tersedia)</option>
                                <option value="unavailable" {{ old('status', $charger->status) == 'unavailable' ? 'selected' : '' }}>🔴 Unavailable (Tidak Tersedia)</option>
                                <option value="maintenance" {{ old('status', $charger->status) == 'maintenance' ? 'selected' : '' }}>🟡 Maintenance (Perawatan)</option>
                            </select>
                        </div>

                        <!-- Foto Mesin -->
                        <div class="md:col-span-2">
                            <label for="photo" class="mb-2 block text-sm font-medium text-slate-700">Update Foto Mesin (Opsional)</label>
                            
                            @if($charger->photo_path)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $charger->photo_path) }}" alt="Foto Saat Ini" class="h-32 w-48 rounded-xl object-cover border border-slate-200 shadow-sm">
                            </div>
                            @endif
                            
                            <input type="file" name="photo" id="photo" accept="image/jpeg, image/png, image/jpg" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-2 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                            <p class="mt-1 text-xs text-slate-500">Biarkan kosong jika Anda tidak ingin mengubah foto mesin saat ini.</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-end">
                    <div class="flex gap-3">
                        <a href="{{ route('vendor.chargers.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-[#34CBDA] px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-600">Update Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection