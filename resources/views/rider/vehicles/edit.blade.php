@extends('layouts.app')

@section('title', 'Edit Kendaraan')

@section('content')
<style>
    .brand-card {
        cursor: pointer;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 12px;
        text-align: center;
        transition: all 0.2s ease;
        background: white;
        position: relative;
        overflow: hidden;
    }
    .brand-card:hover {
        border-color: #10b981;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16,185,129,0.15);
    }
    .brand-card.selected {
        border-color: #10b981;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        box-shadow: 0 6px 20px rgba(16,185,129,0.2);
        transform: translateY(-2px);
    }
    .brand-card .check-icon {
        display: none;
        position: absolute;
        top: 8px;
        right: 8px;
        width: 20px;
        height: 20px;
        background: #10b981;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
    }
    .brand-card.selected .check-icon {
        display: flex;
    }
    .brand-img {
        width: 100%;
        height: 64px;
        object-fit: contain;
        object-position: center;
        margin-bottom: 8px;
        transition: transform 0.2s ease;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.10));
    }
    .brand-card:hover .brand-img {
        transform: scale(1.07);
    }
    .brand-card.selected .brand-img {
        filter: drop-shadow(0 4px 8px rgba(16,185,129,0.25));
    }
    .model-chip {
        cursor: pointer;
        padding: 8px 18px;
        border-radius: 999px;
        border: 2px solid #e2e8f0;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        background: white;
        transition: all 0.15s ease;
        display: inline-block;
    }
    .model-chip:hover {
        border-color: #10b981;
        color: #10b981;
        background: #f0fdf4;
    }
    .model-chip.selected {
        border-color: #10b981;
        background: #10b981;
        color: white;
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }
    .step-indicator {
        display: flex;
        align-items: center;
        gap: 0;
        margin-bottom: 28px;
    }
    .step {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
    }
    .step-num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        transition: all 0.3s;
    }
    .step-line {
        flex: 1;
        height: 2px;
        background: #e2e8f0;
        margin: 0 8px;
        transition: all 0.3s;
    }
    .step-line.done { background: #10b981; }
    .step.active .step-num { background: #10b981; color: white; }
    .step.done .step-num { background: #10b981; color: white; }
    .step.pending .step-num { background: #e2e8f0; color: #94a3b8; }
    .step.active .step-label { color: #10b981; }
    .step.done .step-label { color: #10b981; }
    .step.pending .step-label { color: #94a3b8; }
    .plate-input {
        font-family: 'Plus Jakarta Sans', monospace;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: 4px;
        text-align: center;
        text-transform: uppercase;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 20px;
        width: 100%;
        transition: border-color 0.2s;
    }
    .plate-input:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16,185,129,0.15);
    }
    #submit-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    #submit-btn:not(:disabled) {
        background: linear-gradient(135deg, #10b981, #059669);
        box-shadow: 0 6px 20px rgba(16,185,129,0.35);
    }
    .summary-box {
        background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
        border: 1.5px solid #a7f3d0;
        border-radius: 12px;
        padding: 14px 18px;
    }
</style>

<div class="max-w-3xl mx-auto py-10 sm:px-6">
    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-emerald-600 transition mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Garasi
        </a>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Edit Kendaraan EV</h1>
        <p class="mt-1 text-slate-500">Ubah merek, model, atau plat nomor kendaraan Anda.</p>
    </div>

    {{-- Step Indicator --}}
    <div class="step-indicator">
        <div class="step done" id="step1-indicator">
            <div class="step-num">✓</div>
            <span class="step-label">Pilih Merek</span>
        </div>
        <div class="step-line done" id="line1"></div>
        <div class="step done" id="step2-indicator">
            <div class="step-num">✓</div>
            <span class="step-label">Pilih Model</span>
        </div>
        <div class="step-line done" id="line2"></div>
        <div class="step active" id="step3-indicator">
            <div class="step-num">3</div>
            <span class="step-label">Plat Nomor</span>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-slate-100">
        <div class="p-6 sm:p-8">
            <form action="{{ route('vehicles.update', $vehicle->id) }}" method="POST" id="vehicle-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="merk" id="merk-hidden" value="{{ $vehicle->merk }}">
                <input type="hidden" name="model" id="model-hidden" value="{{ $vehicle->model }}">

                {{-- Step 1: Brand Selection --}}
                <div id="brand-section">
                    <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Pilih Merek Kendaraan</h2>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-3" id="brand-grid">
                        @php
                        $brands = [
                            ['name' => 'Hyundai',  'img' => 'hyundai'],
                            ['name' => 'BYD',      'img' => 'byd'],
                            ['name' => 'Wuling',   'img' => 'wuling'],
                            ['name' => 'Toyota',   'img' => 'toyota'],
                            ['name' => 'Kia',      'img' => 'kia'],
                            ['name' => 'MG',       'img' => 'mg'],
                            ['name' => 'BMW',      'img' => 'bmw'],
                            ['name' => 'Neta',     'img' => 'neta'],
                            ['name' => 'Chery',    'img' => 'chery'],
                            ['name' => 'Volvo',    'img' => 'volvo'],
                            ['name' => 'GWM',      'img' => 'gwm'],
                            ['name' => 'XPeng',    'img' => 'xpeng'],
                            ['name' => 'Geely',    'img' => 'geely'],
                            ['name' => 'GAC Aion', 'img' => 'gac_aion'],
                            ['name' => 'DENZA',    'img' => 'denza'],
                            ['name' => 'Jaecoo',   'img' => 'jaecoo'],
                        ];
                        $currentMerk = $vehicle->merk;
                        $currentModel = $vehicle->model;
                        @endphp

                        @foreach($brands as $brand)
                        <div class="brand-card {{ $currentMerk === $brand['name'] ? 'selected' : '' }}"
                             id="brand-{{ Str::slug($brand['name']) }}"
                             onclick="selectBrand('{{ $brand['name'] }}', this)">
                            <div class="check-icon">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <img src="{{ asset('images/cars/' . $brand['img'] . '.png') }}" alt="{{ $brand['name'] }}" class="brand-img">
                            <div class="text-xs font-bold text-slate-700">{{ $brand['name'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Step 2: Model Selection --}}
                <div id="models-section" class="mt-6">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Pilih Model — <span id="selected-brand-label" class="text-emerald-600">{{ $currentMerk }}</span></h2>
                        <button type="button" onclick="resetBrand()" class="text-xs text-slate-400 hover:text-red-500 transition flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Ganti Merek
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2" id="model-chips">
                        {{-- Populated by JS on load --}}
                    </div>
                </div>

                {{-- Step 3: License Plate --}}
                <div id="plate-section" class="mt-6">
                    <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Masukkan Plat Nomor</h2>
                    <input type="text" name="license_plate" id="license_plate"
                        placeholder="D 1234 ABC" required maxlength="12"
                        value="{{ old('license_plate', $vehicle->license_plate) }}"
                        class="plate-input"
                        oninput="this.value = this.value.toUpperCase(); checkForm()">
                    @error('license_plate')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Summary --}}
                    <div class="summary-box mt-4" id="summary-box">
                        <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-2">Ringkasan Kendaraan</p>
                        <div class="flex gap-6 text-sm">
                            <div><span class="text-slate-400">Merek</span><br><strong id="sum-merk" class="text-slate-800">{{ $currentMerk }}</strong></div>
                            <div><span class="text-slate-400">Model</span><br><strong id="sum-model" class="text-slate-800">{{ $currentModel }}</strong></div>
                            <div><span class="text-slate-400">Plat</span><br><strong id="sum-plate" class="text-slate-800">{{ $vehicle->license_plate }}</strong></div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('vehicles.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" id="submit-btn"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-white transition-all duration-200">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const evModels = {
    'Hyundai':  ['Ioniq 5', 'Ioniq 6', 'Kona Electric'],
    'BYD':      ['Dolphin', 'Atto 3', 'Seal', 'M6', 'Sealion 7', 'Han EV'],
    'Wuling':   ['Air EV Lite Standard Range', 'Air EV Long Range', 'BinguoEV', 'Cloud EV'],
    'Toyota':   ['bZ4X', 'RAV4 PHEV', 'Prius', 'Urban Cruiser EV'],
    'Kia':      ['EV6', 'EV9'],
    'MG':       ['ZS EV', 'MG 4 EV'],
    'BMW':      ['i4', 'iX'],
    'Neta':     ['Neta V-II', 'Neta X'],
    'Chery':    ['J6', 'Omoda E5', 'Tiggo 9 CSH', 'Tiggo 8 CSH'],
    'Volvo':    ['C40 Recharge', 'XC40 Recharge'],
    'GWM':      ['Tank 300 HEV', 'Tank 500 HEV', 'Haval H6 HEV', 'Ora Good Cat'],
    'XPeng':    ['P7', 'G6', 'G9', 'X9'],
    'Geely':    ['EX5', 'Galaxy L7', 'Galaxy E8'],
    'GAC Aion': ['Aion S', 'Aion Y', 'Aion LX Plus', 'Aion V'],
    'DENZA':    ['Denza D9'],
    'Jaecoo':   ['J5', 'J5 EV', 'J7', 'J8'],
};

// Pre-load existing values from server
let selectedBrand = '{{ $currentMerk }}';
let selectedModel = '{{ $currentModel }}';

function renderModels(brand, activeModel) {
    const chipsContainer = document.getElementById('model-chips');
    chipsContainer.innerHTML = '';
    if (!evModels[brand]) return;
    evModels[brand].forEach(model => {
        const chip = document.createElement('div');
        chip.className = 'model-chip' + (model === activeModel ? ' selected' : '');
        chip.textContent = model;
        chip.onclick = () => selectModel(model, chip);
        chipsContainer.appendChild(chip);
    });
}

function selectBrand(brand, el) {
    selectedBrand = brand;
    selectedModel = null;

    document.querySelectorAll('.brand-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');

    document.getElementById('merk-hidden').value = brand;
    document.getElementById('model-hidden').value = '';
    document.getElementById('selected-brand-label').textContent = brand;

    setStep(2);
    renderModels(brand, null);
    document.getElementById('models-section').style.display = 'block';
    document.getElementById('plate-section').style.display = 'none';
    checkForm();
}

function resetBrand() {
    selectedBrand = null;
    selectedModel = null;
    document.querySelectorAll('.brand-card').forEach(c => c.classList.remove('selected'));
    document.getElementById('merk-hidden').value = '';
    document.getElementById('model-hidden').value = '';
    document.getElementById('models-section').style.display = 'none';
    document.getElementById('plate-section').style.display = 'none';
    setStep(1);
    checkForm();
}

function selectModel(model, el) {
    selectedModel = model;
    document.querySelectorAll('.model-chip').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');

    document.getElementById('model-hidden').value = model;
    document.getElementById('plate-section').style.display = 'block';
    setStep(3);
    checkForm();
}

function setStep(step) {
    const steps = [
        { id: 'step1-indicator' },
        { id: 'step2-indicator' },
        { id: 'step3-indicator' },
    ];
    steps.forEach(({ id }, i) => {
        const el = document.getElementById(id);
        el.className = 'step';
        const numEl = el.querySelector('.step-num');
        if (i + 1 < step) {
            el.classList.add('done');
            numEl.textContent = '✓';
        } else if (i + 1 === step) {
            el.classList.add('active');
            numEl.textContent = i + 1;
        } else {
            el.classList.add('pending');
            numEl.textContent = i + 1;
        }
    });
    ['line1', 'line2'].forEach((id, i) => {
        const el = document.getElementById(id);
        el.className = 'step-line' + (i + 1 < step ? ' done' : '');
    });
}

function checkForm() {
    const plate = document.getElementById('license_plate').value.trim();
    const merk = document.getElementById('merk-hidden').value;
    const model = document.getElementById('model-hidden').value;
    const btn = document.getElementById('submit-btn');

    if (merk && model && plate.length >= 4) {
        btn.disabled = false;
        btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        btn.style.boxShadow = '0 6px 20px rgba(16,185,129,0.35)';
        btn.style.cursor = 'pointer';

        document.getElementById('sum-merk').textContent = merk;
        document.getElementById('sum-model').textContent = model;
        document.getElementById('sum-plate').textContent = plate;
        document.getElementById('summary-box').style.display = 'block';
    } else {
        btn.disabled = true;
        btn.style.background = '#cbd5e1';
        btn.style.boxShadow = 'none';
        btn.style.cursor = 'not-allowed';
    }
}

// Initialize on page load with existing vehicle data
window.addEventListener('DOMContentLoaded', () => {
    if (selectedBrand) {
        document.getElementById('models-section').style.display = 'block';
        document.getElementById('plate-section').style.display = 'block';
        renderModels(selectedBrand, selectedModel);
        setStep(3);
        checkForm();
    }
});
</script>
@endsection
