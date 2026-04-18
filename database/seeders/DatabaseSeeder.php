<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
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