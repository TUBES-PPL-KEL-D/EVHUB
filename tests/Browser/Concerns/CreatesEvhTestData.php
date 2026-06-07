<?php

namespace Tests\Browser\Concerns;

use App\Models\ChargerMachine;
use App\Models\Spklu;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

trait CreatesEvhTestData
{
    protected function createRider(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Rider Test',
            'email' => 'rider.test.' . uniqid() . '@evhub.test',
            'phone' => '081234567890',
            'role' => 'rider',
            'status' => 'active',
            'balance' => 500000,
            'password' => Hash::make('password123'),
        ], $overrides));
    }

    protected function createApprovedVendorUser(array $overrides = []): User
    {
        $user = User::create(array_merge([
            'name' => 'Vendor Test',
            'email' => 'vendor.test.' . uniqid() . '@evhub.test',
            'phone' => '081234567891',
            'role' => 'vendor',
            'status' => 'active',
            'balance' => 0,
            'password' => Hash::make('password123'),
        ], $overrides));

        $vendorId = $this->insertTableData('vendors', [
            'user_id' => $user->id,
            'company_name' => 'Vendor EVHUB Test',
            'legality_document_path' => 'vendor/legalities/test.pdf',
            'status' => 'Approved',
        ]);

        /*
         * Catatan:
         * Controller vendor EVHUB memfilter charger_machines.vendor_id
         * berdasarkan id dari tabel vendors.
         */
        $user->evhub_vendor_id = $vendorId;

        return $user;
    }

    protected function createSpklu(int $vendorId, array $overrides = []): Spklu
    {
        return Spklu::create(array_merge([
            'vendor_id' => $vendorId,
            'name' => 'SPKLU Dusk Test',
            'address' => 'Jl. Testing EVHUB No. 1',
            'latitude' => -6.973,
            'longitude' => 107.630,
        ], $overrides));
    }

    protected function createChargerMachine(int $vendorId, int $spkluId, array $overrides = []): ChargerMachine
    {
        return ChargerMachine::create(array_merge([
            'vendor_id' => $vendorId,
            'spklu_id' => $spkluId,
            'name' => 'Mesin Charger Dusk',
            'connector_type' => 'CCS2',
            'capacity_kw' => 20,
            'price_per_kwh' => 3500,
            'operational_hours' => '08:00 - 22:00',
            'photo_path' => 'chargers/test.jpg',
            'status' => 'available',
        ], $overrides));
    }

    protected function createVehicle(int $userId, array $overrides = []): Vehicle
    {
        return Vehicle::create(array_merge([
            'user_id' => $userId,
            'merk' => 'Hyundai',
            'model' => 'Ioniq 5',
            'license_plate' => 'D ' . rand(1000, 9999) . ' EV',
            'connector_type' => 'CCS',
            'battery_percentage' => 50,
            'estimated_full_range_km' => 400,
        ], $overrides));
    }

    protected function createTransaction(
        int $userId,
        int $chargerMachineId,
        ?int $vehicleId,
        string $status = 'success',
        float $energy = 20,
        float $totalPrice = 70000
    ): int {
        return $this->insertTableData('transactions', [
            'user_id' => $userId,
            'charger_machine_id' => $chargerMachineId,
            'vehicle_id' => $vehicleId,
            'energy_consumed' => $energy,
            'total_price' => $totalPrice,
            'started_at' => Carbon::now()->subHour(),
            'finished_at' => $status === 'success' ? Carbon::now() : null,
            'status' => $status,
        ]);
    }

    protected function insertTableData(string $table, array $data): int
    {
        $columns = Schema::getColumnListing($table);

        if (in_array('created_at', $columns) && ! array_key_exists('created_at', $data)) {
            $data['created_at'] = now();
        }

        if (in_array('updated_at', $columns) && ! array_key_exists('updated_at', $data)) {
            $data['updated_at'] = now();
        }

        $filteredData = array_intersect_key($data, array_flip($columns));

        return DB::table($table)->insertGetId($filteredData);
    }
}