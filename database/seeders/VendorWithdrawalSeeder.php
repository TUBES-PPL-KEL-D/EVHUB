<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\VendorWithdrawal;

class VendorWithdrawalSeeder extends Seeder
{
    public function run(): void
    {
        // Cari vendor yang sudah Approved (dari seeder bawaanmu)
        $vendor = Vendor::where('status', 'Approved')->first();

        if ($vendor) {
            VendorWithdrawal::create([
                'vendor_id' => $vendor->id,
                'amount' => 1500000, // Rp 1.500.000
                'status' => 'pending',
                'reference_code' => 'WD-TEST-' . rand(1000, 9999),
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name' => $vendor->company_name,
                'notes' => 'Pencairan dana bulan ini'
            ]);
        }
    }
}