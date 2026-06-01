<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChargerMachine;

class ChargerMachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $machines = [
            // === SPKLU ID: 1 ===
            [
                'vendor_id' => 1,
                'spklu_id' => 1,
                'name' => 'Ultra Fast Charger Delta-01',
                'connector_type' => 'CCS',
                'capacity_kw' => 150.00,
                'price_per_kwh' => 2475.00,
                'operational_hours' => '24 Jam',
                'photo_path' => 'chargers/delta01.jpg',
                'status' => 'available',
            ],
            [
                'vendor_id' => 1,
                'spklu_id' => 1,
                'name' => 'Fast Charger Delta-02',
                'connector_type' => 'CHAdeMO',
                'capacity_kw' => 50.00,
                'price_per_kwh' => 2200.00,
                'operational_hours' => '24 Jam',
                'photo_path' => 'chargers/delta02.jpg',
                'status' => 'available',
            ],
            [
                'vendor_id' => 1,
                'spklu_id' => 1,
                'name' => 'Medium Charger Delta-03',
                'connector_type' => 'Type2',
                'capacity_kw' => 22.00,
                'price_per_kwh' => 1650.00,
                'operational_hours' => '24 Jam',
                'photo_path' => 'chargers/delta03.jpg',
                'status' => 'available',
            ],
            [
                'vendor_id' => 1,
                'spklu_id' => 1,
                'name' => 'Super Charger Delta-04 (Maint)',
                'connector_type' => 'CCS',
                'capacity_kw' => 200.00,
                'price_per_kwh' => 2700.00,
                'operational_hours' => '24 Jam',
                'photo_path' => 'chargers/delta04.jpg',
                'status' => 'maintenance',
            ],

            // === SPKLU ID: 2 ===
            [
                'vendor_id' => 1,
                'spklu_id' => 2,
                'name' => 'Sigma Fast Charge A',
                'connector_type' => 'CCS',
                'capacity_kw' => 60.00,
                'price_per_kwh' => 2250.00,
                'operational_hours' => '06:00 - 22:00',
                'photo_path' => 'chargers/sigma_a.jpg',
                'status' => 'available',
            ],
            [
                'vendor_id' => 1,
                'spklu_id' => 2,
                'name' => 'Sigma Eco Charge B',
                'connector_type' => 'Type2',
                'capacity_kw' => 11.00,
                'price_per_kwh' => 1500.00,
                'operational_hours' => '06:00 - 22:00',
                'photo_path' => 'chargers/sigma_b.jpg',
                'status' => 'available',
            ],
            [
                'vendor_id' => 1,
                'spklu_id' => 2,
                'name' => 'Sigma Heavy Charger C',
                'connector_type' => 'CHAdeMO',
                'capacity_kw' => 100.00,
                'price_per_kwh' => 2400.00,
                'operational_hours' => '06:00 - 22:00',
                'photo_path' => 'chargers/sigma_c.jpg',
                'status' => 'unavailable',
            ],
            [
                'vendor_id' => 1,
                'spklu_id' => 2,
                'name' => 'Sigma Fast Charge D',
                'connector_type' => 'CCS',
                'capacity_kw' => 60.00,
                'price_per_kwh' => 2250.00,
                'operational_hours' => '06:00 - 22:00',
                'photo_path' => 'chargers/sigma_d.jpg',
                'status' => 'available',
            ],

            // === SPKLU ID: 3 ===
            [
                'vendor_id' => 1,
                'spklu_id' => 3,
                'name' => 'E-Volt HyperStation 01',
                'connector_type' => 'CCS',
                'capacity_kw' => 350.00,
                'price_per_kwh' => 2950.00,
                'operational_hours' => '24 Jam',
                'photo_path' => 'chargers/evolt01.jpg',
                'status' => 'available',
            ],
            [
                'vendor_id' => 1,
                'spklu_id' => 3,
                'name' => 'E-Volt RapidStation 02',
                'connector_type' => 'CHAdeMO',
                'capacity_kw' => 120.00,
                'price_per_kwh' => 2450.00,
                'operational_hours' => '24 Jam',
                'photo_path' => 'chargers/evolt02.jpg',
                'status' => 'available',
            ],
            [
                'vendor_id' => 1,
                'spklu_id' => 3,
                'name' => 'E-Volt CityStation 03',
                'connector_type' => 'Type2',
                'capacity_kw' => 22.00,
                'price_per_kwh' => 1650.00,
                'operational_hours' => '24 Jam',
                'photo_path' => 'chargers/evolt03.jpg',
                'status' => 'available',
            ],
            [
                'vendor_id' => 1,
                'spklu_id' => 3,
                'name' => 'E-Volt CityStation 04',
                'connector_type' => 'Type2',
                'capacity_kw' => 7.00,
                'price_per_kwh' => 1450.00,
                'operational_hours' => '24 Jam',
                'photo_path' => 'chargers/evolt04.jpg',
                'status' => 'maintenance',
            ],
        ];

        foreach ($machines as $machine) {
            ChargerMachine::create($machine);
        }
    }
}
