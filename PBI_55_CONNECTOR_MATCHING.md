# PBI 55: Connector Matching System - Sprint 2

## Deskripsi Fitur
Implementasi sistem pencocokan konektor kendaraan dengan stasiun pengisian daya SPKLU untuk membantu pengendara mengetahui stasiun mana yang kompatibel dengan kendaraan mereka.

## Status: ✅ COMPLETED

---

## Perubahan yang Dilakukan

### 1. Database Migration
**File**: `database/migrations/2026_05_23_000000_add_connector_type_to_vehicles_table.php`
- Menambahkan kolom `connector_type` ke tabel `vehicles`
- Tipe data: `string` (nullable)
- Digunakan untuk menyimpan tipe konektor kendaraan

**Command untuk menjalankan:**
```bash
php artisan migrate
```

### 2. Service Layer - ConnectorMatchingService
**File**: `app/Services/ConnectorMatchingService.php`

Class ini menyediakan logika bisnis untuk pencocokan konektor:

#### Methods:
- `getAvailableConnectors()`: Mendapatkan list tipe konektor yang tersedia
  - CCS (Combined Charging System)
  - CHAdeMO
  - Type 2 / Mennekes
  - GB/T
  - Tesla Connector

- `isCompatible(Vehicle $vehicle, ChargerMachine $chargerMachine)`: 
  - Mengecek apakah konektor kendaraan cocok dengan charger machine
  - Return: `boolean`

- `getCompatibleChargers(Vehicle $vehicle)`:
  - Mendapatkan semua charger machines yang kompatibel dengan kendaraan
  - Return: `Collection`

- `getCompatibleStations(Vehicle $vehicle)`:
  - Mendapatkan semua SPKLU yang memiliki charger kompatibel
  - Return: `Collection`

- `getMatchingStatus(Vehicle $vehicle, Spklu $station)`:
  - Mendapatkan detail pencocokan antara kendaraan dan stasiun
  - Return: Array dengan keys:
    - `is_compatible`: boolean
    - `count`: jumlah charger yang cocok
    - `chargers`: Collection charger machines

### 3. Model Updates
**File**: `app/Models/Vehicle.php`

Update fillable dan tambah methods:

```php
protected $fillable = [
    'user_id',
    'merk',
    'model',
    'license_plate',
    'connector_type',  // NEW
];

// NEW Methods untuk convenience
public function isCompatibleWith(ChargerMachine $chargerMachine): bool
public function getCompatibleChargers()
public function getCompatibleStations()
public function getMatchingStatus(Spklu $station): array
public function getConnectorDisplayName(): string
```

### 4. Controller Updates
**File**: `app/Http/Controllers/VehicleController.php`

Update validation di methods `store()` dan `update()`:
```php
'connector_type' => 'required|string|in:CCS,CHAdeMO,Type2,GB/T,Tesla',
```

### 5. UI - Create Vehicle Form
**File**: `resources/views/rider/vehicles/create.blade.php`

Perubahan:
- Update stepper dari 3 steps menjadi 4 steps
- Step 3: Pilih Tipe Konektor (NEW)
- Step 4: Masukkan Plat Nomor

Form menampilkan:
- 5 opsi radio button untuk tipe konektor
- CCS ditandai sebagai "Populer"
- Validasi: connector_type wajib diisi
- Summary box menampilkan connector yang dipilih

### 6. UI - Edit Vehicle Form
**File**: `resources/views/rider/vehicles/edit.blade.php`

Perubahan sama seperti create form:
- Update stepper dari 3 steps menjadi 4 steps
- Menampilkan connector_type saat ini
- Allow user untuk mengubah connector_type

### 7. Blade Components (Reusable)
**File**: `resources/views/components/connector-status.blade.php`
- Component untuk menampilkan status kompatibilitas antara vehicle & charger
- Props: `vehicle`, `chargerMachine`
- Output: Badge "Cocok" (hijau) atau "Tidak Cocok" (merah)

**File**: `resources/views/components/station-compatibility.blade.php`
- Component untuk menampilkan status kompatibilitas di stasiun
- Props: `vehicle`, `station`
- Output: Card dengan detail jumlah charger yang cocok

---

## Cara Penggunaan

### Di Controller/Service:
```php
// Check if vehicle is compatible with a charger
$isCompatible = $vehicle->isCompatibleWith($charger);

// Get all compatible chargers for a vehicle
$chargers = $vehicle->getCompatibleChargers();

// Get matching status for a station
$status = $vehicle->getMatchingStatus($station);
if ($status['is_compatible']) {
    // Show available chargers
    foreach ($status['chargers'] as $charger) {
        // ...
    }
}

// Get compatible stations
$stations = $vehicle->getCompatibleStations();
```

### Di Blade Template (Menampilkan Status):
```blade
<!-- Menampilkan status kompatibilitas single charger -->
<x-connector-status :vehicle="$vehicle" :chargerMachine="$charger" />

<!-- Menampilkan status kompatibilitas di stasiun -->
<x-station-compatibility :vehicle="$vehicle" :station="$station" />
```

---

## Data yang Disimpan

### Tabel vehicles
| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| connector_type | string | yes | CCS, CHAdeMO, Type2, GB/T, Tesla |

---

## Testing Checklist

- [ ] Database migration berjalan tanpa error
- [ ] User bisa membuat kendaraan dengan memilih connector_type
- [ ] User bisa mengedit connector_type kendaraan
- [ ] Form validation menolak jika connector_type tidak dipilih
- [ ] Validation rules hanya menerima nilai: CCS, CHAdeMO, Type2, GB/T, Tesla
- [ ] Summary box menampilkan connector yang dipilih dengan benar
- [ ] Connector matching logic bekerja dengan benar
- [ ] Components menampilkan status "Cocok" dan "Tidak Cocok" dengan benar

---

## API Ready untuk Pengembangan

### Untuk menampilkan stations yang cocok dengan vehicle:
```php
$compatibleStations = $vehicle->getCompatibleStations();

foreach ($compatibleStations as $station) {
    $status = $vehicle->getMatchingStatus($station);
    // status['count'] = jumlah charger yang cocok
    // status['is_compatible'] = true/false
}
```

### Untuk filter stations berdasarkan compatibility:
```php
// Bisa diintegrasikan dengan API endpoint untuk map/list view
// GET /api/compatible-stations/{vehicleId}
```

---

## Next Steps untuk Integrasi Lebih Lanjut

1. **API Endpoint**: Buat endpoint untuk mendapatkan compatible stations
   - `GET /api/vehicles/{id}/compatible-stations`
   - Return stations dengan charging points yang tersedia

2. **Map Integration**: Tampilkan stations yang cocok di map
   - Filter SPKLU berdasarkan compatible dengan vehicle user

3. **Search/Filter UI**: Di list stasiun, tambah filter berdasarkan kompatibilitas

4. **Booking System**: Saat user memilih charger untuk booking, pastikan sudah dicek compatibility

5. **Notification**: Notifikasi user jika connector_type belum diisi saat browsing stations

---

## Notes untuk Team

- Service class `ConnectorMatchingService` bisa diperluas untuk feature lainnya (e.g., charging speed compatibility)
- Migration file sudah mengikuti naming convention Laravel (timestamp-based)
- Form UI menggunakan pattern yang konsisten dengan create/edit vehicle form yang sudah ada
- Component-component bisa digunakan di multiple views (stations list, booking flow, etc)

---

**Created**: May 23, 2026  
**Sprint**: 2  
**Status**: Ready for Testing
