<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     */
    public function run(): void
    {
        // 1. Akun Default Admin Utama (Tidak Bisa Daftar Lewat Web)
        User::create([
            'name' => 'Super Admin EVHUB',
            'email' => 'admin@evhub.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // 2. Akun Simulasi Vendor untuk Testing
        User::create([
            'name' => 'Mitra Vendor Pusat',
            'email' => 'vendor@evhub.com',
            'password' => Hash::make('vendor123'),
            'role' => 'vendor'
        ]);

        // 3. Akun Simulasi Pengendara (Rider) untuk Testing
        User::create([
            'name' => 'Pengendara EV',
            'email' => 'rider@evhub.com',
            'password' => Hash::make('rider123'),
            'role' => 'rider'
        ]);
    }
}