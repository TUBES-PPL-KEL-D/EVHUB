@extends('layouts.app')

@section('title', 'Konfigurasi Pengisian Daya')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Konfigurasi Pengisian</h1>
        <p class="text-sm text-slate-500">Mesin: {{ $machine->name }} ({{ $machine->connector_type }})</p>
        <p class="text-sm text-emerald-600 font-semibold">Tarif: Rp {{ number_format($machine->price_per_kwh, 0, ',', '.') }}/kWh</p>
    </div>

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-600 px-4 py-3 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('rider.transactions.start') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="charger_machine_id" value="{{ $machine->id }}">

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Kendaraan Anda</label>
            <select name="vehicle_id" required class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">-- Pilih Mobil --</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}">
                        {{ $vehicle->merk }} {{ $vehicle->model }} [{{ $vehicle->license_plate }}] - ({{ $vehicle->connector_type }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Target Pengisian (kWh)</label>
            <input type="number" name="energy_target" min="1" step="0.1" required placeholder="Contoh: 30"
                   class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl text-sm transition-all shadow-md">
            Konfirmasi & Mulai Mengisi
        </button>
    </form>
</div>
@endsection