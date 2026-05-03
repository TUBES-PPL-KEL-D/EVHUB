<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\User; 

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Asep Rider',
                'email' => 'asep.rider@example.com',
                'password' => bcrypt('password123'),
                'role' => 'rider',
                'phone' => '081999888777',
            ]);
        }

        $vehicles = [
            [
                'user_id' => $user->id,
                'merk' => 'Hyundai',
                'model' => 'Ioniq 5',
                'license_plate' => 'D 1234 EV'
            ],
            [
                'user_id' => $user->id,
                'merk' => 'Wuling',
                'model' => 'Binguo EV',
                'license_plate' => 'B 5678 EV'
            ],
            [
                'user_id' => $user->id,
                'merk' => 'BYD',
                'model' => 'Seal',
                'license_plate' => 'B 9999 BYD'
            ],
            [
                'user_id' => $user->id,
                'merk' => 'Toyota',
                'model' => 'bZ4X',
                'license_plate' => 'D 7777 TOY'
            ]
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}