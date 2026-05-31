@extends('layouts.app')

@section('title', 'Konfigurasi Pengisian Daya')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-6">

    <div>
        <h1 class="text-xl font-bold text-slate-900">Konfigurasi Pengisian</h1>
        <p class="text-sm text-slate-500">
            Mesin: {{ $machine->name }} ({{ $machine->connector_type }})
        </p>
        <p class="text-sm text-emerald-600 font-semibold">
            Tarif: Rp {{ number_format($machine->price_per_kwh, 0, ',', '.') }}/kWh
        </p>
        <p class="text-sm text-slate-500">
            Daya Mesin: {{ number_format($machine->capacity_kw, 1, ',', '.') }} kW
        </p>
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
            <label class="block text-sm font-medium text-slate-700 mb-2">
                Pilih Kendaraan Anda
            </label>

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
            <label class="block text-sm font-medium text-slate-700 mb-2">
                Target Pengisian (kWh)
            </label>

            <input
                type="number"
                id="energy_target"
                name="energy_target"
                min="1"
                step="0.1"
                required
                placeholder="Contoh: 30"
                class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500"
            >
        </div>

        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 space-y-2">
            <p class="text-sm font-semibold text-slate-800">
                Estimasi Durasi Pengisian
            </p>

            <p id="estimated_duration" class="text-2xl font-bold text-emerald-700">
                -
            </p>

            <p class="text-xs text-slate-500">
                Estimasi dihitung berdasarkan target pengisian dan daya mesin sebesar
                <span class="font-semibold">{{ number_format($machine->capacity_kw, 1, ',', '.') }} kW</span>.
            </p>
        </div>

        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl text-sm transition-all shadow-md">
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