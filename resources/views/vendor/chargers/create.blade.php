@extends('layouts.app')

@section('content')
<div class="container" style="padding: 20px; max-width: 700px; margin: auto; font-family: sans-serif;">
    <h2 style="margin-bottom: 20px;">Tambah Mesin Charger Baru</h2>

    @if ($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #f5c6cb;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('chargers.store') }}" method="POST" enctype="multipart/form-data" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @csrf

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Pilih Lokasi SPKLU <span style="color:red;">*</span></label>
            <select name="spklu_id" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px;">
                <option value="">-- Silakan Pilih SPKLU --</option>
                @foreach($spklus as $spklu)
                    <option value="{{ $spklu->id }}" {{ old('spklu_id') == $spklu->id ? 'selected' : '' }}>{{ $spklu->name }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Nama Mesin <span style="color:red;">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;" placeholder="Contoh: Fast Charger V2">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Tipe Konektor <span style="color:red;">*</span></label>
            <input type="text" name="connector_type" value="{{ old('connector_type') }}" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;" placeholder="Contoh: CCS2, Type 2, CHAdeMO">
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="font-weight: bold; display: block; margin-bottom: 8px;">Kapasitas (kW) <span style="color:red;">*</span></label>
                <input type="number" name="capacity_kw" value="{{ old('capacity_kw') }}" min="1" step="0.01" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: bold; display: block; margin-bottom: 8px;">Harga (Rp/kWh) <span style="color:red;">*</span></label>
                <input type="number" name="price_per_kwh" value="{{ old('price_per_kwh') }}" min="0" step="0.01" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;">
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Jam Operasional <span style="color:red;">*</span></label>
            <input type="text" name="operational_hours" value="{{ old('operational_hours') }}" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;" placeholder="Contoh: 24 Jam atau 08:00 - 22:00">
        </div>

        <div style="margin-bottom: 25px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Foto Fisik Mesin <span style="color:red;">*</span></label>
            <input type="file" name="photo" accept="image/jpeg, image/png, image/jpg" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa; box-sizing: border-box;">
            <small style="color: #6c757d; display: block; margin-top: 5px;">Format yang didukung: JPG, JPEG, PNG. Maksimal ukuran file: 2MB.</small>
        </div>

        <div style="text-align: right;">
            <a href="{{ route('chargers.index') }}" style="text-decoration: none; color: #6c757d; margin-right: 20px; font-weight: bold;">Batal</a>
            <button type="submit" style="background-color: #28a745; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 15px;">Simpan Infrastruktur</button>
        </div>
    </form>
</div>
@endsection