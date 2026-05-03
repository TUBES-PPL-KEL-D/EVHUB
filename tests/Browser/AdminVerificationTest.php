<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Vendor;
use App\Models\User;

class AdminVerificationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_alur_verifikasi_dan_manajemen_vendor()
    {
        $this->browse(function (Browser $browser) {
            // 1. SETUP DATA
            $user = User::factory()->create();
            $vendor = Vendor::create([
                'user_id' => $user->id,
                'company_name' => 'PT Antre Verifikasi',
                'npwp' => '123456789012345',
                'address' => 'Jl. Bojongsoang No. 1',
                'status' => 'Pending',
            ]);

            // 2. APPROVE VENDOR (DASHBOARD)
            $browser->visit('/admin/dashboard')
                    ->assertSee('PT Antre Verifikasi')
                    ->press('TERIMA') 
                    ->waitForText('Semua Selesai!', 10);

            // 3. MENUJU HALAMAN STASIUN
            $browser->visit('/admin/stations')
                    ->waitForText('PT Antre Verifikasi', 10);

            // 4. PROSES SUSPEND (Bypass Confirm)
            $browser->script("window.confirm = function(){ return true; };");
            $browser->press('SUSPEND_ACC')
                    ->pause(3000)
                    // Berdasarkan screenshot, teksnya KAPITAL karena class uppercase
                    ->waitForText('AKUN DITANGGUHKAN', 10); 

            // 5. PROSES HAPUS (Bypass Confirm)
            $browser->script("window.confirm = function(){ return true; };");
            $browser->press('HAPUS')
                    ->pause(3000);
            
            // 6. VERIFIKASI AKHIR (WAJIB KAPITAL SESUAI SCREENSHOT)
            $browser->waitForText('BELUM ADA VENDOR AKTIF', 10)
                    ->assertSee('TIDAK ADA AKUN YANG DIBEKUKAN');
        });
    }
}