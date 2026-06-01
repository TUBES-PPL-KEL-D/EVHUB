@extends('layouts.app')

@section('title', 'Konfigurasi Pengisian Daya')

@section('content')
<div class="max-w-md mx-auto bg-slate-900/60 backdrop-blur-md rounded-2xl shadow-xl border border-slate-800 p-6 space-y-6">

    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Konfigurasi Pengisian
        </h1>
        <p class="text-sm text-slate-400 mt-1 flex flex-col">
            <div class="my-1">
                Mesin: <span class="text-slate-200 font-medium">{{ $machine->name }}</span>
            </div>
            <div class="my-1">
                Connector: <span class="text-slate-200 font-medium">{{ $machine->connector_type }}</span>
            </div>
        </p>
        
        <div class="grid grid-cols-2 gap-2 mt-3 pt-2 border-t border-slate-800/50">
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider">Tarif</p>
                <p class="text-sm text-emerald-400 font-semibold">Rp {{ number_format($machine->price_per_kwh, 0, ',', '.') }}/kWh</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider">Daya Mesin</p>
                <p class="text-sm text-slate-200 font-medium">{{ number_format($machine->capacity_kw, 1, ',', '.') }} kW</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-4 py-3 rounded-xl text-sm font-medium flex items-start gap-2.5">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('rider.transactions.start') }}" method="POST" class="space-y-5">
        @csrf

        <input type="hidden" name="charger_machine_id" value="{{ $machine->id }}">

        <div class="space-y-2">
            <label class="block text-sm font-semibold text-slate-300">
                Pilih Kendaraan Anda
            </label>
            <div class="relative">
                <select name="vehicle_id" required class="w-full appearance-none bg-slate-950/50 border border-slate-800 rounded-xl p-3 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all cursor-pointer">
                    <option value="" class="bg-slate-900 text-slate-400">-- Pilih Mobil --</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" class="bg-slate-900 text-slate-200">
                            {{ $vehicle->merk }} {{ $vehicle->model }} [{{ $vehicle->license_plate }}] - ({{ strtoupper($vehicle->connector_type) }})
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-semibold text-slate-300">
                Target Pengisian (kWh)
            </label>
            <div class="relative">
                <input
                    type="number"
                    id="energy_target"
                    name="energy_target"
                    min="1"
                    step="0.1"
                    required
                    placeholder="Contoh: 30"
                    class="w-full bg-slate-950/50 border border-slate-800 rounded-xl p-3 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all"
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-sm font-semibold text-slate-500">
                    kWh
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500/10 to-teal-500/5 border border-emerald-500/20 rounded-2xl p-4 space-y-2">
            <p class="text-xs font-semibold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Estimasi Durasi Pengisian
            </p>

            <p id="estimated_duration" class="text-2xl font-bold text-white drop-shadow-md">
                -
            </p>

            <p class="text-xs text-slate-400 leading-relaxed">
                Dihitung berdasarkan target daya dan kapasitas mesin sebesar 
                <span class="font-semibold text-slate-200">{{ number_format($machine->capacity_kw, 1, ',', '.') }} kW</span>.
            </p>
        </div>

        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 hover:from-emerald-400 hover:to-emerald-500 text-white font-bold py-3.5 rounded-xl text-sm transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Konfirmasi & Mulai Mengisi
        </button>
    </form>
</div>

<script>
    const energyInput = document.getElementById('energy_target');
    const estimatedDurationText = document.getElementById('estimated_duration');
    const machinePower = {{ (float) $machine->capacity_kw }};

    function formatDuration(totalMinutes) {
        const hours = Math.floor(totalMinutes / 60);
        const minutes = Math.round(totalMinutes % 60);

        if (hours > 0 && minutes > 0) {
            return `${hours} jam ${minutes} menit`;
        }
        if (hours > 0) {
            return `${hours} jam`;
        }
        return `${minutes} menit`;
    }

    function calculateEstimatedDuration() {
        const energyTarget = parseFloat(energyInput.value);

        if (!energyTarget || energyTarget <= 0 || machinePower <= 0) {
            estimatedDurationText.textContent = '-';
            return;
        }

        const durationHours = energyTarget / machinePower;
        const durationMinutes = durationHours * 60;

        estimatedDurationText.textContent = formatDuration(durationMinutes);
    }

    energyInput.addEventListener('input', calculateEstimatedDuration);
</script>
@endsection