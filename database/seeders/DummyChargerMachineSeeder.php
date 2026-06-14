<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChargerMachine;
use App\Models\Spklu;
use App\Models\Vendor;

class DummyChargerMachineSeeder extends Seeder
{
    public function run()
    {
        $vendor = Vendor::first();
        if (!$vendor) return;


        $spklus = Spklu::whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8])->get();

        foreach ($spklus as $spklu) {
            if ($spklu->chargerMachines()->count() == 0) {
                ChargerMachine::create([
                    'vendor_id' => $vendor->id,
                    'spklu_id' => $spklu->id,
                    'name' => 'Mesin Utama ' . $spklu->name,
                    'connector_type' => 'CCS2',
                    'capacity_kw' => 50,
                    'price_per_kwh' => 2500,
                    'operational_hours' => '24 Jam',
                    'status' => 'available',
                    'photo_path' => '',
                ]);
                
                ChargerMachine::create([
                    'vendor_id' => $vendor->id,
                    'spklu_id' => $spklu->id,
                    'name' => 'Mesin Kedua ' . $spklu->name,
                    'connector_type' => 'CHAdeMO',
                    'capacity_kw' => 50,
                    'price_per_kwh' => 2500,
                    'operational_hours' => '24 Jam',
                    'status' => 'available',
                    'photo_path' => '',
                ]);
            }
        }
    }
}
