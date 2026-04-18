@extends('layouts.app')

@section('content')
<div class="container" style="padding: 20px; font-family: sans-serif;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Daftar Mesin Charger</h2>
        <a href="{{ route('chargers.create') }}" style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold;">+ Tambah Mesin Baru</a>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <table style="width: 100%; border-collapse: collapse; background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; text-align: left;">
                <th style="padding: 12px;">Foto</th>
                <th style="padding: 12px;">Nama Mesin</th>
                <th style="padding: 12px;">Lokasi SPKLU</th>
                <th style="padding: 12px;">Spesifikasi</th>
                <th style="padding: 12px;">Status</th>
                <th style="padding: 12px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($chargers as $charger)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;">
                        <img src="{{ asset('storage/' . $charger->photo_path) }}" alt="Foto" style="width: 80px; height: auto; border-radius: 4px; border: 1px solid #ccc;">
                    </td>
                    <td style="padding: 12px;">
                        <strong>{{ $charger->name }}</strong><br>
                        <small style="color: #6c757d;">Jam: {{ $charger->operational_hours }}</small>
                    </td>
                    <td style="padding: 12px;">{{ $charger->spklu->name ?? 'Tidak Terhubung' }}</td>
                    <td style="padding: 12px;">
                        Tipe: {{ $charger->connector_type }}<br>
                        Kapasitas: {{ $charger->capacity_kw }} kW<br>
                        Harga: Rp {{ number_format($charger->price_per_kwh, 0, ',', '.') }}/kWh
                    </td>
                    <td style="padding: 12px;">
                        <span style="padding: 5px 10px; border-radius: 12px; font-size: 12px; background-color: {{ $charger->status == 'available' ? '#d4edda' : ($charger->status == 'maintenance' ? '#fff3cd' : '#f8d7da') }}; color: {{ $charger->status == 'available' ? '#155724' : ($charger->status == 'maintenance' ? '#856404' : '#721c24') }}; font-weight: bold;">
                            {{ strtoupper($charger->status) }}
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        <a href="{{ route('vendor.chargers.edit', $charger->id) }}" style="color: #ffc107; text-decoration: none; margin-right: 15px; font-weight: bold;">Edit</a>
                        <form action="{{ route('vendor.chargers.destroy', $charger->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: #dc3545; border: none; background: none; cursor: pointer; padding: 0; font-weight: bold; font-size: 16px;" onclick="return confirm('Apakah Anda yakin ingin menghapus mesin ini secara permanen?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 30px; text-align: center; color: #6c757d;">Belum ada data mesin charger yang didaftarkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection