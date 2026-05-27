<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Spklu;
use App\Models\Ticket; // Tambahan model Ticket untuk PBI 9
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Tambahan untuk memanipulasi file

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- 1. MENYIAPKAN DOKUMEN DUMMY ---
        // Memastikan folder penyimpanan tersedia
        Storage::disk('public')->makeDirectory('vendor_documents');

        // Membuat file PDF dummy secara otomatis
        $dummyPdfPath = 'vendor_documents/dummy_legalitas.pdf';
        Storage::disk('public')->put($dummyPdfPath, 'Ini adalah isi dari dokumen legalitas dummy. Dalam file aslinya, ini akan berupa format PDF yang valid dari vendor.');


        // --- 2. SEEDING VENDOR & SPKLU ---
        // 1. Membuat akun User pertama (sebagai vendor)
        $user1 = User::create([
            'name' => 'Budi Pengusaha',
            'email' => 'budi.vendor@example.com',
            'password' => Hash::make('password123'),
            'role' => 'vendor',
            'phone' => '081234567890',
        ]);

        // Membuat data pendaftaran Vendor untuk User pertama
        $vendor1 = Vendor::create([
            'user_id' => $user1->id,
            'company_name' => 'PT Energi Nusantara Raya',
            'legality_document_path' => $dummyPdfPath, // Path diganti ke file dummy yang baru saja di-generate
            'status' => 'Pending',
        ]);

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

        // Membuat data pendaftaran Vendor untuk User kedua (tanpa file dokumen)
        Vendor::create([
            'user_id' => $user2->id,
            'company_name' => 'CV Maju Pengisian Cepat',
            'legality_document_path' => null,
            'status' => 'Pending',
        ]);


        // --- 3. SEEDING TIKET LAPORAN (UNTUK PBI 9) ---
        // Buat user dummy sebagai pengendara pelapor
        $userPelapor = User::create([
            'name' => 'Agus Pengendara EV',
            'email' => 'agus.ev@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user', 
            'phone' => '085555555555',
        ]);

        // Buat data tiket laporan
        Ticket::create([
            'user_id' => $userPelapor->id,
            'subject' => 'Mesin Charger di Braga CityWalk Mati',
            'description' => 'Saya mencoba mengisi daya tapi layarnya blank hitam.',
            'status' => 'pending'
        ]);

        Ticket::create([
            'user_id' => $userPelapor->id,
            'subject' => 'Lokasi SPKLU Gedung Sate Kurang Akurat',
            'description' => 'Marker di peta agak melenceng sekitar 50 meter dari lokasi aslinya.',
            'status' => 'pending'
        ]);


        // --- 4. CALL OTHER SEEDERS ---
        // manggil vehicle seeder
        $this->call([
            VehicleSeeder::class,
        ]);

        $this->call([
            // Pastikan VendorSeeder & SpkluSeeder dipanggil lebih dulu sebelum ChargerMachineSeeder
            ChargerMachineSeeder::class,
        ]);

    }
}