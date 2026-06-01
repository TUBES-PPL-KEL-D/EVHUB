@extends('layouts.app')

@section('title', 'Tambah Kendaraan')

@section('content')
<style>
    /* Dark Mode Custom Styles */
    .brand-card {
        cursor: pointer;
        border: 1px solid rgba(51, 65, 85, 0.5); /* border-slate-700/50 */
        border-radius: 1rem;
        padding: 16px 12px;
        text-align: center;
        transition: all 0.3s ease;
        background: rgba(15, 23, 42, 0.5); /* bg-slate-900/50 */
        position: relative;
        overflow: hidden;
    }
    .brand-card:hover {
        border-color: rgba(16, 185, 129, 0.5); /* emerald-500/50 */
        background: rgba(30, 41, 59, 0.6); /* bg-slate-800/60 */
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    }
    .brand-card.selected {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.1);
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
        transform: translateY(-2px);
    }
    .brand-card .check-icon {
        display: none;
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        background: #10b981;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.5);
    }
    .brand-card.selected .check-icon {
        display: flex;
    }
    .brand-img-container {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 2px solid rgba(16, 185, 129, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
        transition: all 0.3s ease;
        box-shadow: 0 8px 16px rgba(0,0,0,0.4), inset 0 1px 0 rgba(16,185,129,0.1);
        overflow: hidden;
    }
    .brand-card:hover .brand-img-container {
        border-color: rgba(16, 185, 129, 0.6);
        box-shadow: 0 12px 24px rgba(16,185,129,0.15), inset 0 1px 0 rgba(16,185,129,0.2);
        transform: translateY(-2px);
    }
    .brand-card.selected .brand-img-container {
        border-color: rgba(16, 185, 129, 1);
        background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05));
        box-shadow: 0 0 20px rgba(16,185,129,0.4), inset 0 1px 0 rgba(16,185,129,0.3);
    }
    .brand-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.3s ease;
        border-radius: 50%;
    }
    .brand-card:hover .brand-img {
        transform: scale(1.1);
    }
    .model-chip {
        cursor: pointer;
        padding: 10px 20px;
        border-radius: 999px;
        border: 1px solid rgba(51, 65, 85, 0.8);
        font-size: 14px;
        font-weight: 600;
        color: #94a3b8; /* slate-400 */
        background: rgba(15, 23, 42, 0.4);
        transition: all 0.2s ease;
        display: inline-block;
    }
    .model-chip:hover {
        border-color: rgba(16, 185, 129, 0.5);
        color: #e2e8f0; /* slate-200 */
        background: rgba(30, 41, 59, 0.6);
    }
    .model-chip.selected {
        border-color: #10b981;
        background: #10b981;
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }
    
    /* Stepper Styling */
    .step-indicator {
        display: flex;
        align-items: center;
        gap: 0;
        margin-bottom: 32px;
    }
    .step {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .step-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        transition: all 0.4s;
    }
    .step-line {
        flex: 1;
        height: 2px;
        background: rgba(51, 65, 85, 0.5);
        margin: 0 12px;
        transition: all 0.4s;
    }
    .step-line.done { background: #10b981; }
    
    .step.active .step-num { background: #10b981; color: white; box-shadow: 0 0 15px rgba(16,185,129,0.4); }
    .step.done .step-num { background: #10b981; color: white; }
    .step.pending .step-num { background: rgba(30, 41, 59, 0.8); color: #64748b; border: 1px solid rgba(51, 65, 85, 0.8); }
    
    .step.active .step-label { color: #34d399; }
    .step.done .step-label { color: #10b981; }
    .step.pending .step-label { color: #64748b; }
    
    #models-section, #connector-section, #plate-section { display: none; }
    
    /* Input Form Styling */
    .plate-input {
        font-family: 'Plus Jakarta Sans', monospace;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: 6px;
        text-align: center;
        text-transform: uppercase;
        background: rgba(15, 23, 42, 0.5);
        border: 2px solid rgba(51, 65, 85, 0.8);
        color: white;
        border-radius: 1rem;
        padding: 16px 24px;
        width: 100%;
        transition: all 0.3s;
    }
    .plate-input:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16,185,129,0.15);
        background: rgba(15, 23, 42, 0.8);
    }
    
    /* Button Styling */
    #submit-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #334155;
        color: #94a3b8;
        box-shadow: none;
    }
    #submit-btn:not(:disabled) {
        background: linear-gradient(135deg, #10b981, #059669);
        box-shadow: 0 8px 25px rgba(16,185,129,0.3);
    }
    #submit-btn:not(:disabled):hover {
        transform: translateY(-2px);
    }
    
    /* Summary Box */
    .summary-box {
        background: rgba(16, 185, 129, 0.05);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 1rem;
        padding: 20px;
        display: none;
        backdrop-filter: blur(8px);
    }
</style>

<div class="max-w-4xl mx-auto py-10 sm:px-6">
    {{-- Header --}}
    <div class="mb-10 text-center">
        <a href="{{ route('rider.vehicles.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-400 hover:text-emerald-400 transition mb-6 bg-slate-800/50 px-4 py-2 rounded-full border border-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Garasi
        </a>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-4">Tambah <span class="text-emerald-500">Kendaraan</span></h1>
        <p class="text-lg text-slate-400 max-w-2xl mx-auto">Pilih merek dan model kendaraan listrik Anda, lalu masukkan plat nomor dengan benar.</p>
    </div>

    <!-- Main Card Container -->
    <div class="bg-slate-800/40 shadow-2xl rounded-[2.5rem] overflow-hidden border border-slate-700/50 backdrop-blur-md">
        <div class="p-8 md:p-12">
            
            {{-- Step Indicator --}}
            <div class="step-indicator">
                <div class="step active" id="step1-indicator">
                    <div class="step-num">1</div>
                    <span class="step-label hidden sm:block">Pilih Merek</span>
                </div>
                <div class="step-line" id="line1"></div>
                <div class="step pending" id="step2-indicator">
                    <div class="step-num">2</div>
                    <span class="step-label hidden sm:block">Pilih Model</span>
                </div>
                <div class="step-line" id="line2"></div>
                <div class="step pending" id="step3-indicator">
                    <div class="step-num">3</div>
                    <span class="step-label hidden sm:block">Konektor</span>
                </div>
                <div class="step-line" id="line3"></div>
                <div class="step pending" id="step4-indicator">
                    <div class="step-num">4</div>
                    <span class="step-label hidden sm:block">Plat Nomor</span>
                </div>
            </div>

            <form action="{{ route('rider.vehicles.store') }}" method="POST" enctype="multipart/form-data" id="vehicle-form">
                @csrf

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-rose-500/40 bg-rose-500/10 p-4 text-sm text-rose-200">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <input type="hidden" name="merk" id="merk-hidden" value="{{ old('merk') }}">
                <input type="hidden" name="model" id="model-hidden" value="{{ old('model') }}">

                {{-- Step 1: Brand Selection --}}
                <div id="brand-section">
                    <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Katalog Merek
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" id="brand-grid">
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
                        @endphp

                        @foreach($brands as $brand)
                        <div class="brand-card" id="brand-{{ Str::slug($brand['name']) }}" onclick="selectBrand('{{ $brand['name'] }}', this)">
                            <div class="check-icon">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="brand-img-container">
                                <img src="{{ asset('images/cars/' . $brand['img'] . '.png') }}" alt="{{ $brand['name'] }}" class="brand-img">
                            </div>
                            <div class="text-sm font-bold text-slate-300">{{ $brand['name'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Step 2: Model Selection --}}
                <div id="models-section" class="mt-10 pt-8 border-t border-slate-700/50">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                        <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Varian Model <span class="mx-2">—</span> <span id="selected-brand-label" class="text-white bg-slate-700 px-3 py-1 rounded-md"></span>
                        </h2>
                        <button type="button" onclick="resetBrand()" class="text-xs font-bold text-rose-400 hover:text-rose-300 transition flex items-center gap-1.5 bg-rose-500/10 px-3 py-1.5 rounded-lg border border-rose-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Ganti Merek
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-3" id="model-chips">
                        {{-- Populated by JS --}}
                    </div>
                </div>

                {{-- Step 3: Connector Type (Optional) --}}
                <div id="connector-section" class="mt-10 pt-8 border-t border-slate-700/50">
                    <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Tipe Konektor <span class="text-xs bg-slate-700 text-slate-300 px-2 py-1 rounded ml-auto">Opsional</span>
                    </h2>
                    
                    <div class="max-w-2xl">
                        <p class="text-slate-400 text-sm mb-6">Pilih tipe konektor pengisian daya kendaraan Anda (bisa diatur kemudian):</p>
                        
                        @php
                            $connectors = [
                                'CCS' => 'CCS (Combined Charging System)',
                                'CHAdeMO' => 'CHAdeMO',
                                'Type2' => 'Type 2 / Mennekes',
                                'GB/T' => 'GB/T',
                                'Tesla' => 'Tesla Connector',
                            ];
                        @endphp
                        
                        <div class="space-y-3">
                            @foreach($connectors as $value => $label)
                            <label class="flex items-center gap-4 p-4 rounded-lg border border-slate-700 hover:border-slate-600 hover:bg-slate-700/30 cursor-pointer transition {{ old('connector_type') === $value ? 'bg-emerald-500/10 border-emerald-500' : 'bg-slate-800/30' }}">
                                <input type="radio" name="connector_type" value="{{ $value }}" 
                                    {{ old('connector_type') === $value ? 'checked' : '' }}
                                    onchange="checkForm()" class="w-4 h-4 rounded-full accent-emerald-500">
                                <div class="flex-1">
                                    <div class="font-semibold text-white">{{ $label }}</div>
                                </div>
                                @if($value === 'CCS')
                                    <span class="text-xs bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded">Populer</span>
                                @endif
                            </label>
                            @endforeach
                        </div>

                        @error('connector_type')
                            <p class="mt-4 text-sm text-rose-500 bg-rose-500/10 py-2 px-3 rounded-lg">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Step 4: License Plate --}}
                <div id="plate-section" class="mt-10 pt-8 border-t border-slate-700/50">
                    <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Identitas Kendaraan
                    </h2>
                    
                    <div class="max-w-xl mx-auto">
                        <input type="text" name="license_plate" id="license_plate" placeholder="D 1234 ABC"
                            required maxlength="12"
                            class="plate-input mb-2"
                            value="{{ old('license_plate') }}"
                            oninput="this.value = this.value.toUpperCase(); checkForm()">
                        <p class="text-center text-slate-500 text-sm mb-4">Pastikan plat nomor sesuai dengan STNK kendaraan Anda.</p>

                        <label class="block text-sm font-semibold text-slate-200 mb-2" for="battery_service_date">Jadwal Servis Baterai Berikutnya</label>
                        <input type="date" name="battery_service_date" id="battery_service_date"
                            class="w-full rounded-2xl border border-slate-700/70 bg-slate-900/70 px-4 py-3 text-sm text-slate-100 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 mb-4"
                            value="{{ old('battery_service_date') }}">
                        <p class="text-center text-slate-500 text-sm mb-6">Masukkan tanggal servis untuk mendapatkan pengingat baterai di garasi.</p>

                        <label class="block text-sm font-semibold text-slate-200 mb-2" for="battery_percentage">Persentase Baterai Saat Ini</label>
                        <input type="number" name="battery_percentage" id="battery_percentage"
                            min="0" max="100" step="1"
                            class="w-full rounded-2xl border border-slate-700/70 bg-slate-900/70 px-4 py-3 text-sm text-slate-100 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 mb-4"
                            value="{{ old('battery_percentage') }}"
                            placeholder="Contoh: 65">
                        <p class="text-center text-slate-500 text-sm mb-6">Nilai persentase baterai digunakan untuk menghitung jarak tempuh tersisa.</p>

                        <label class="block text-sm font-semibold text-slate-200 mb-2" for="estimated_full_range_km">Perkiraan Jarak Penuh Kendaraan (km)</label>
                        <input type="number" name="estimated_full_range_km" id="estimated_full_range_km"
                            min="1" step="1"
                            class="w-full rounded-2xl border border-slate-700/70 bg-slate-900/70 px-4 py-3 text-sm text-slate-100 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 mb-4"
                            value="{{ old('estimated_full_range_km') }}"
                            placeholder="Contoh: 320">
                        <p class="text-center text-slate-500 text-sm mb-8">Masukkan estimasi jarak maksimum tempuh kendaraan untuk menghitung sisa jarak berdasarkan baterai.</p>

                        <label class="block text-sm font-semibold text-slate-200 mb-2" for="vehicle_photo">Foto Kendaraan (opsional)</label>
                        <input type="file" name="vehicle_photo" id="vehicle_photo"
                            accept="image/*"
                            class="w-full rounded-2xl border border-slate-700/70 bg-slate-900/70 px-4 py-3 text-sm text-slate-100 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 mb-4">
                        <p class="text-center text-slate-500 text-sm mb-8">Unggah foto kendaraan Anda untuk mempercantik profil garasi.</p>

                        @error('vehicle_photo')
                            <p class="mt-2 text-sm text-rose-500 text-center bg-rose-500/10 py-2 rounded-lg">{{ $message }}</p>
                        @enderror

                        @error('battery_service_date')
                            <p class="mt-2 text-sm text-rose-500 text-center bg-rose-500/10 py-2 rounded-lg">{{ $message }}</p>
                        @enderror
                        @error('battery_percentage')
                            <p class="mt-2 text-sm text-rose-500 text-center bg-rose-500/10 py-2 rounded-lg">{{ $message }}</p>
                        @enderror
                        @error('estimated_full_range_km')
                            <p class="mt-2 text-sm text-rose-500 text-center bg-rose-500/10 py-2 rounded-lg">{{ $message }}</p>
                        @enderror
                        @error('license_plate')
                            <p class="mt-2 text-sm text-rose-500 text-center bg-rose-500/10 py-2 rounded-lg">{{ $message }}</p>
                        @enderror

                        {{-- Summary --}}
                        <div class="summary-box" id="summary-box">
                            <p class="text-xs font-bold text-emerald-400 uppercase tracking-widest mb-4 border-b border-emerald-500/20 pb-2">Ringkasan Pendaftaran</p>
                            <div class="grid grid-cols-2 gap-4 text-center mb-6 pb-6 border-b border-emerald-500/20">
                                <div>
                                    <span class="text-slate-400 text-xs block mb-1 uppercase">Merek</span>
                                    <strong id="sum-merk" class="text-white text-lg"></strong>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-xs block mb-1 uppercase">Model</span>
                                    <strong id="sum-model" class="text-white text-lg"></strong>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-center mb-4">
                                <div>
                                    <span class="text-slate-400 text-xs block mb-1 uppercase">Konektor</span>
                                    <strong id="sum-connector" class="text-emerald-400 text-lg"></strong>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-xs block mb-1 uppercase">Plat</span>
                                    <strong id="sum-plate" class="text-emerald-400 text-lg"></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-12 pt-8 border-t border-slate-700/50 flex flex-col-reverse sm:flex-row justify-end gap-4">
                    <a href="{{ route('rider.vehicles.index') }}"
                        class="px-6 py-3.5 rounded-xl border border-slate-600 text-sm font-bold text-slate-300 hover:bg-slate-700 hover:text-white transition text-center">
                        Batal
                    </a>
                    <button type="submit" id="submit-btn" disabled
                        class="px-8 py-3.5 rounded-xl text-sm font-bold text-white transition-all duration-300 w-full sm:w-auto">
                        Simpan ke Garasi
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
    'Wuling':   ['Air EV Lite', 'Air EV Long Range', 'BinguoEV', 'Cloud EV'],
    'Toyota':   ['bZ4X', 'RAV4 PHEV', 'Prius', 'Urban Cruiser EV'],
    'Kia':      ['EV6', 'EV9'],
    'MG':       ['ZS EV', 'MG 4 EV'],
    'BMW':      ['i4', 'iX', 'i7'],
    'Neta':     ['Neta V-II', 'Neta X'],
    'Chery':    ['J6', 'Omoda E5', 'Tiggo 9 CSH'],
    'Volvo':    ['C40 Recharge', 'XC40 Recharge'],
    'GWM':      ['Tank 300 HEV', 'Tank 500 HEV', 'Haval H6 HEV', 'Ora Good Cat'],
    'XPeng':    ['P7', 'G6', 'G9', 'X9'],
    'Geely':    ['EX5', 'Galaxy L7', 'Galaxy E8'],
    'GAC Aion': ['Aion S', 'Aion Y', 'Aion LX Plus', 'Aion V'],
    'DENZA':    ['Denza D9'],
    'Jaecoo':   ['J5', 'J5 EV', 'J7', 'J8'],
};

let selectedBrand = null;
let selectedModel = null;

function renderModels(brand, activeModel = null) {
    const chipsContainer = document.getElementById('model-chips');
    chipsContainer.innerHTML = '';

    if (evModels[brand]) {
        evModels[brand].forEach(model => {
            const chip = document.createElement('div');
            chip.className = 'model-chip';
            chip.textContent = model;
            if (model === activeModel) {
                chip.classList.add('selected');
                selectedModel = model;
                document.getElementById('model-hidden').value = model;
            }
            chip.onclick = () => selectModel(model, chip);
            chipsContainer.appendChild(chip);
        });
    }
}

function selectBrand(brand, el) {
    selectedBrand = brand;
    selectedModel = null;

    document.querySelectorAll('.brand-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');

    document.getElementById('merk-hidden').value = brand;
    document.getElementById('model-hidden').value = '';

    setStep(2);

    document.getElementById('selected-brand-label').textContent = brand;
    renderModels(brand, null);
    
    document.getElementById('models-section').style.display = 'block';
    document.getElementById('plate-section').style.display = 'none';
    document.getElementById('summary-box').style.display = 'none';
    checkForm();
    
    setTimeout(() => {
        document.getElementById('models-section').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 100);
}

function resetBrand() {
    selectedBrand = null;
    selectedModel = null;
    document.querySelectorAll('.brand-card').forEach(c => c.classList.remove('selected'));
    document.getElementById('merk-hidden').value = '';
    document.getElementById('model-hidden').value = '';
    document.querySelector('input[name="connector_type"]').checked = false;
    document.getElementById('models-section').style.display = 'none';
    document.getElementById('connector-section').style.display = 'none';
    document.getElementById('plate-section').style.display = 'none';
    document.getElementById('summary-box').style.display = 'none';
    setStep(1);
    checkForm();
}

function selectModel(model, el) {
    selectedModel = model;
    document.querySelectorAll('.model-chip').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');

    document.getElementById('model-hidden').value = model;
    document.getElementById('connector-section').style.display = 'block';
    document.getElementById('plate-section').style.display = 'none';

    setStep(3);
    checkForm();

    setTimeout(() => {
        document.getElementById('connector-section').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 100);
}

function setStep(step) {
    const steps = ['step1-indicator', 'step2-indicator', 'step3-indicator', 'step4-indicator'];
    const lines = ['line1', 'line2', 'line3'];

    steps.forEach((id, i) => {
        const el = document.getElementById(id);
        el.className = 'step';
        if (i + 1 < step) el.classList.add('done');
        else if (i + 1 === step) el.classList.add('active');
        else el.classList.add('pending');
    });

    lines.forEach((id, i) => {
        const el = document.getElementById(id);
        el.className = 'step-line';
        if (i + 1 < step) el.classList.add('done');
    });
}

function checkForm() {
    const plate = document.getElementById('license_plate').value.trim();
    const merk = document.getElementById('merk-hidden').value;
    const model = document.getElementById('model-hidden').value;
    const connector = document.querySelector('input[name="connector_type"]:checked')?.value || '';
    const btn = document.getElementById('submit-btn');

    // Show plate section if model is selected
    if (merk && model) {
        if (!document.getElementById('plate-section').style.display || document.getElementById('plate-section').style.display === 'none') {
            document.getElementById('plate-section').style.display = 'block';
            if (connector) {
                setStep(4);
            } else {
                setStep(3); // Step 3 is connector, but we're allowing to skip
            }
            setTimeout(() => {
                document.getElementById('plate-section').scrollIntoView({ behavior: 'smooth', block: 'center' });
                document.getElementById('license_plate').focus();
            }, 100);
        }
    }

    // Form valid jika: merk + model + plate. Connector optional!
    if (merk && model && plate.length >= 4) {
        btn.disabled = false;
        
        // Get connector display name
        const connectorNames = {
            'CCS': 'CCS (Combined Charging System)',
            'CHAdeMO': 'CHAdeMO',
            'Type2': 'Type 2 / Mennekes',
            'GB/T': 'GB/T',
            'Tesla': 'Tesla Connector',
        };
        
        document.getElementById('sum-merk').textContent = merk;
        document.getElementById('sum-model').textContent = model;
        if (connector) {
            document.getElementById('sum-connector').textContent = connectorNames[connector] || connector;
        } else {
            document.getElementById('sum-connector').textContent = 'Belum dipilih';
        }
        document.getElementById('sum-plate').textContent = plate;
        document.getElementById('summary-box').style.display = 'block';
    } else {
        btn.disabled = true;
        document.getElementById('summary-box').style.display = 'none';
    }
}

window.addEventListener('DOMContentLoaded', () => {
    const oldBrand = document.getElementById('merk-hidden').value;
    const oldModel = document.getElementById('model-hidden').value;
    const oldConnector = document.querySelector('input[name="connector_type"]:checked')?.value || '';
    const oldPlate = document.getElementById('license_plate').value.trim();

    if (oldBrand) {
        const cardId = 'brand-' + slugify(oldBrand);
        const brandCard = document.getElementById(cardId);
        if (brandCard) {
            brandCard.classList.add('selected');
        }

        document.getElementById('selected-brand-label').textContent = oldBrand;
        renderModels(oldBrand, oldModel);
        document.getElementById('models-section').style.display = 'block';

        if (oldModel) {
            document.getElementById('connector-section').style.display = 'block';
            setStep(3);
        } else {
            setStep(2);
        }

        if (oldConnector) {
            document.getElementById('plate-section').style.display = 'block';
            setStep(4);
        }

        if (oldBrand && oldModel && oldConnector && oldPlate) {
            document.getElementById('summary-box').style.display = 'block';
        }
        checkForm();
    }
});

function slugify(text) {
    return text.toString().toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '');
}
</script>
@endsection