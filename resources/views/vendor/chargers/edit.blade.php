@extends('layouts.app')

@section('content')
<div class="container" style="padding: 20px; max-width: 700px; margin: auto; font-family: sans-serif;">
    <h2 style="margin-bottom: 20px;">Edit Mesin Charger</h2>

    @if ($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #f5c6cb;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('chargers.update', $charger->id) }}" method="POST" enctype="multipart/form-data" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Pilih Lokasi SPKLU <span style="color:red;">*</span></label>
            <select name="spklu_id" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px;">
                <option value="">-- Silakan Pilih SPKLU --</option>
                @foreach($spklus as $spklu)
                    <option value="{{ $spklu->id }}" {{ (old('spklu_id', $charger->spklu_id) == $spklu->id) ? 'selected' : '' }}>{{ $spklu->name }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Nama Mesin <span style="color:red;">*</span></label>
            <input type="text" name="name" value="{{ old('name', $charger->name) }}" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Tipe Konektor <span style="color:red;">*</span></label>
            <input type="text" name="connector_type" value="{{ old('connector_type', $charger->connector_type) }}" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="font-weight: bold; display: block; margin-bottom: 8px;">Kapasitas (kW) <span style="color:red;">*</span></label>
                <input type="number" name="capacity_kw" value="{{ old('capacity_kw', $charger->capacity_kw) }}" min="1" step="0.01" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: bold; display: block; margin-bottom: 8px;">Harga (Rp/kWh) <span style="color:red;">*</span></label>
                <input type="number" name="price_per_kwh" value="{{ old('price_per_kwh', $charger->price_per_kwh) }}" min="0" step="0.01" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;">
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Jam Operasional <span style="color:red;">*</span></label>
            <input type="text" name="operational_hours" value="{{ old('operational_hours', $charger->operational_hours) }}" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Status Mesin <span style="color:red;">*</span></label>
            <select name="status" required style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px;">
                <option value="available" {{ old('status', $charger->status) == 'available' ? 'selected' : '' }}>Available (Tersedia)</option>
                <option value="unavailable" {{ old('status', $charger->status) == 'unavailable' ? 'selected' : '' }}>Unavailable (Tidak Tersedia)</option>
                <option value="maintenance" {{ old('status', $charger->status) == 'maintenance' ? 'selected' : '' }}>Maintenance (Perawatan)</option>
            </select>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="font-weight: bold; display: block; margin-bottom: 8px;">Update Foto (Opsional)</label>
            <div style="margin-bottom: 10px;">
                <img src="{{ asset('storage/' . $charger->photo_path) }}" alt="Foto Saat Ini" style="max-height: 120px; border-radius: 5px; border: 1px solid #ccc; display: block;">
            </div>
            <input type="file" name="photo" accept="image/jpeg, image/png, image/jpg" style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa; box-sizing: border-box;">
            <small style="color: #6c757d; display: block; margin-top: 5px;">Biarkan kosong jika tidak ingin mengubah foto mesin.</small>
        </div>

        <div style="text-align: right;">
            <a href="{{ route('chargers.index') }}" style="text-decoration: none; color: #6c757d; margin-right: 20px; font-weight: bold;">Batal</a>
            <button type="submit" style="background-color: #007bff; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 15px;">Update Data</button>
        </div>
    </form>
</div>
@endsection