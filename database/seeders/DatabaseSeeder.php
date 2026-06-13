<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Spklu;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. MENYIAPKAN DOKUMEN DUMMY ---
        Storage::disk('public')->makeDirectory('vendor_documents');
        $dummyPdfPath = 'vendor_documents/dummy_legalitas.pdf';
        Storage::disk('public')->put($dummyPdfPath, 'Ini adalah isi dari dokumen legalitas dummy. Dalam file aslinya, ini akan berupa format PDF yang valid dari vendor.');

        // --- 2. SEEDING VENDOR & SPKLU ---
        $user1 = User::create([
            'name' => 'Budi Pengusaha',
            'email' => 'budi.vendor@example.com',
            'password' => Hash::make('password123'),
            'role' => 'vendor',
            'phone' => '081234567890',
        ]);

        $vendor1 = Vendor::create([
            'user_id' => $user1->id,
            'company_name' => 'PT Energi Nusantara Raya',
            'legality_document_path' => $dummyPdfPath,
            // REVISI PENTING: Diubah jadi Approved agar bisa ditest Withdrawal-nya
            'status' => 'Approved', 
        ]);

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

        $user2 = User::create([
            'name' => 'Siti Strum',
            'email' => 'siti.strum@example.com',
            'password' => Hash::make('password123'),
            'role' => 'vendor',
            'phone' => '089876543210',
        ]);

        Vendor::create([
            'user_id' => $user2->id,
            'company_name' => 'CV Maju Pengisian Cepat',
            'legality_document_path' => null,
            'status' => 'Pending',
        ]);

        // --- 3. SEEDING TIKET LAPORAN ---
        $userPelapor = User::create([
            'name' => 'Agus Pengendara EV',
            'email' => 'agus.ev@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user', 
            'phone' => '085555555555',
        ]);

        Ticket::create([
            'user_id' => $userPelapor->id,
            'subject' => 'Mesin Charger di Braga CityWalk Mati',
            'description' => 'Saya mencoba mengisi daya tapi layarnya blank.',
            'status' => 'pending'
        ]);

        Ticket::create([
            'user_id' => $userPelapor->id,
            'subject' => 'Lokasi SPKLU Gedung Sate Kurang Akurat',
            'description' => 'Marker di peta melenceng sekitar 50 meter.',
            'status' => 'pending'
        ]);

        // --- 4. CALL OTHER SEEDERS (REVISI: Dijadikan satu agar tidak duplikat) ---
        $this->call([
            UserSeeder::class, // Pastikan UserSeeder ini memiliki akun dengan role 'admin'
            VehicleSeeder::class,
            ChargerMachineSeeder::class,
            VendorWithdrawalSeeder::class, 
        ]);
    }
}