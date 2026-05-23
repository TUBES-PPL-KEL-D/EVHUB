# Quick Start Guide - PBI 55: Connector Matching

## 🚀 Fitur yang Sudah Diimplementasikan

Sistem pencocokan konektor kendaraan dengan stasiun pengisian daya SPKLU sudah siap digunakan.

---

## 📋 Step-by-Step untuk Testing

### 1️⃣ Jalankan Migration
```bash
php artisan migrate
```
Ini akan menambahkan kolom `connector_type` ke tabel vehicles.

### 2️⃣ Buat Kendaraan Baru
- Buka form tambah kendaraan: `http://localhost/riders/vehicles/create`
- Form sekarang memiliki 4 steps:
  1. Pilih Merek
  2. Pilih Model
  3. **[NEW]** Pilih Tipe Konektor
  4. Masukkan Plat Nomor

- Pilih salah satu konektor:
  - CCS (Combined Charging System) - Populer
  - CHAdeMO
  - Type 2 / Mennekes
  - GB/T
  - Tesla Connector

### 3️⃣ Edit Kendaraan
- Buka form edit kendaraan
- Form sekarang juga menampilkan pilihan Tipe Konektor
- User bisa mengubah konektor yang dipilih

---

## 🔧 Cara Menggunakan di Code

### A. Di Controller/Service
```php
use App\Services\ConnectorMatchingService;

// Get available connectors
$connectors = ConnectorMatchingService::getAvailableConnectors();
// Returns: ['CCS' => 'CCS (Combined Charging System)', ...]

// Check if compatible
$vehicle = Vehicle::find(1);
$charger = ChargerMachine::find(1);
if ($vehicle->isCompatibleWith($charger)) {
    // Vehicle can use this charger
}

// Get compatible chargers
$chargers = $vehicle->getCompatibleChargers();

// Get compatible stations
$stations = $vehicle->getCompatibleStations();

// Get detailed matching status
$status = $vehicle->getMatchingStatus($station);
echo $status['count']; // Jumlah charger yang cocok
echo $status['is_compatible']; // true/false
```

### B. Di Blade Template

#### Menampilkan Status Single Charger
```blade
<!-- app/resources/views/chargers/show.blade.php -->
<x-connector-status :vehicle="$userVehicle" :chargerMachine="$charger" />

<!-- Output:
    ✅ Cocok   (jika compatible)
    atau
    ❌ Tidak Cocok (jika tidak compatible)
-->
```

#### Menampilkan Status di Stasiun
```blade
<!-- app/resources/views/stations/show.blade.php -->
@forelse($stations as $station)
    <x-station-compatibility :vehicle="$vehicle" :station="$station" />
    <!-- Output:
        ✅ Cocok!
        5 charger tersedia dengan konektor CCS
        atau
        ❌ Tidak Cocok
        Stasiun ini tidak memiliki konektor CCS
    -->
@empty
    <p>No stations found</p>
@endforelse
```

---

## 📊 Database Schema

### Vehicles Table
```sql
ALTER TABLE vehicles ADD COLUMN connector_type VARCHAR(255) NULLABLE;
```

### Contoh Data
```
id | user_id | merk    | model      | license_plate | connector_type | created_at
1  | 1       | Hyundai | Ioniq 5    | B 1234 AB     | CCS            | 2026-05-23
2  | 2       | BYD     | Dolphin    | DK 5678 CD    | CHAdeMO        | 2026-05-23
```

---

## ✅ Testing Scenarios

### Scenario 1: Membuat Kendaraan dengan Konektor
```
1. Buka /riders/vehicles/create
2. Pilih merek: Hyundai
3. Pilih model: Ioniq 5
4. Pilih konektor: CCS
5. Masukkan plat: B 1234 AB
6. Submit
✅ Kendaraan berhasil dibuat dengan connector_type = 'CCS'
```

### Scenario 2: Validasi Form
```
1. Buka /riders/vehicles/create
2. Pilih merek dan model
3. JANGAN pilih konektor
4. Masukkan plat nomor
5. Coba klik Submit
✅ Button akan tetap disabled (validation error)
```

### Scenario 3: Check Compatibility
```php
$vehicle = Vehicle::where('connector_type', 'CCS')->first();

// Get compatible chargers (only CCS chargers)
$chargers = $vehicle->getCompatibleChargers();

// Get compatible stations (with CCS chargers)
$stations = $vehicle->getCompatibleStations();

// Check specific station
$station = Spklu::find(1);
$status = $vehicle->getMatchingStatus($station);
if ($status['is_compatible']) {
    echo $status['count']; // e.g., "3 chargers available"
}
```

---

## 🎯 Apa Berikutnya?

### Siap untuk Dikembangkan:
1. **API Endpoints** - Untuk mobile/web app
   ```
   GET /api/vehicles/{id}/compatible-stations
   GET /api/stations/{id}/compatible-vehicles
   ```

2. **Map Integration** - Tampilkan stations cocok di map
   ```blade
   <x-map :stations="$vehicle->getCompatibleStations()" />
   ```

3. **Filter di List Stasiun** - Filter by compatibility
   ```blade
   @foreach($stations as $station)
       <x-station-compatibility :vehicle="$userVehicle" :station="$station" />
   @endforeach
   ```

4. **Booking Flow** - Auto-check kompatibilitas saat user mau booking

---

## 📝 File yang Dimodifikasi

| File | Perubahan |
|------|-----------|
| `database/migrations/2026_05_23_000000_add_connector_type_to_vehicles_table.php` | Baru - Migration |
| `app/Services/ConnectorMatchingService.php` | Baru - Service Layer |
| `app/Models/Vehicle.php` | Updated - Add fillable & methods |
| `app/Http/Controllers/VehicleController.php` | Updated - Validation & storage |
| `resources/views/rider/vehicles/create.blade.php` | Updated - Add connector step |
| `resources/views/rider/vehicles/edit.blade.php` | Updated - Add connector step |
| `resources/views/components/connector-status.blade.php` | Baru - Component |
| `resources/views/components/station-compatibility.blade.php` | Baru - Component |

---

## 🐛 Common Issues & Solutions

### Issue: Migration error
```
SQLSTATE[HY000]: General error
```
**Solution:**
```bash
php artisan migrate:rollback
php artisan migrate
```

### Issue: Form tidak menampilkan connector options
**Solution:**
- Clear browser cache
- Restart development server
- Check console untuk JavaScript errors

### Issue: Validation error "connector_type tidak dikenali"
**Solution:**
- Pastikan nilai yang dikirim adalah: CCS, CHAdeMO, Type2, GB/T, atau Tesla
- Bukan typo atau lowercase yang salah

---

## 💡 Tips & Tricks

1. **Get Connector Display Name**
   ```php
   $vehicle->getConnectorDisplayName();
   // Returns: "CCS (Combined Charging System)"
   ```

2. **Batch Check Compatibility**
   ```php
   $stations = Spklu::all();
   $vehicle = Vehicle::find(1);
   
   foreach ($stations as $station) {
       $status = $vehicle->getMatchingStatus($station);
       if ($status['is_compatible']) {
           // Show as option
       }
   }
   ```

3. **Filter by Connector Type**
   ```php
   // Get all CCS chargers
   $cssChargers = ChargerMachine::where('connector_type', 'CCS')->get();
   
   // Or using vehicle
   $compatible = $vehicle->getCompatibleChargers();
   ```

---

**Questions?** Refer ke PBI_55_CONNECTOR_MATCHING.md untuk dokumentasi lengkap.
