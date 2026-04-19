<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Spklu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat akun User pertama (sebagai vendor)
        $user1 = User::create([
            'name' => 'Budi Pengusaha',
            'email' => 'budi.vendor@example.com',
            'password' => Hash::make('password123'),
            'role' => 'vendor',
            'phone' => '081234567890',
        ]);

        // Membuat data pendaftaran Vendor untuk User pertama
        Vendor::create([
            'user_id' => $user1->id,
            'company_name' => 'PT Energi Nusantara Raya',
            'legality_document_path' => 'dummy/legalitas_pt_energi.pdf',
            'status' => 'Pending',
        ]);
        // Ambil data vendor pertama (PT Energi Nusantara Raya) yang baru saja dibuat di atasnya
        $vendor1 = Vendor::first();

        // Memasukkan data dummy SPKLU
        $spklus = [
            [
                'vendor_id' => $vendor1->id, 
                'name' => 'SPKLU PLN Gedung Sate',
                'address' => 'Jl. Diponegoro No.22, Citarum, Bandung',
                'latitude' => -6.902481,
                'longitude' => 107.618810,
            ],
            [
                'vendor_id' => $vendor1->id,
                'name' => 'SPKLU PLN Braga CityWalk',
                'address' => 'Jl. Braga No.99-101, Braga, Bandung',
                'latitude' => -6.917464,
                'longitude' => 107.609348,
            ],
            [
                'vendor_id' => $vendor1->id,
                'name' => 'SPKLU Trans Studio Mall',
                'address' => 'Jl. Gatot Subroto No.289, Cibangkong, Bandung',
                'latitude' => -6.925093,
                'longitude' => 107.636494,
            ]
        ];

        foreach ($spklus as $spklu) {
            Spklu::create($spklu);
        }


        // 2. Membuat akun User kedua (sebagai vendor)
        $user2 = User::create([
            'name' => 'Siti Strum',
            'email' => 'siti.strum@example.com',
            'password' => Hash::make('password123'),
            'role' => 'vendor',
            'phone' => '089876543210',
        ]);

        // Membuat data pendaftaran Vendor untuk User kedua (contoh tanpa file dokumen)
        Vendor::create([
            'user_id' => $user2->id,
            'company_name' => 'CV Maju Pengisian Cepat',
            'legality_document_path' => null,
            'status' => 'Pending',
        ]);
    }
}