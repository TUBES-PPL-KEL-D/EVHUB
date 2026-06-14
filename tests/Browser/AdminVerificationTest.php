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

    /**
     * PBI #9 - Case ID: TC.Adm.001
     * Menguji dashboard antrean vendor
     */
    public function test_admin_mengecek_daftar_vendor_baru()
    {
        // 1. Buat akun Admin agar tidak ditendang middleware
        $admin = User::factory()->create(['role' => 'admin']);
        
        // 2. Buat akun Vendor dummy
        $vendorUser = User::factory()->create();
        Vendor::create([
            'user_id' => $vendorUser->id,
            'company_name' => 'PT Antre Verifikasi',
            'status' => 'Pending',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    // 3. Harus masuk ke tab verifikasi dulu
                    ->click('#tabBtn-verifikasi')
                    ->waitForText('PT Antre Verifikasi', 10)
                    ->assertSee('PT Antre Verifikasi');
        });
    }

    /**
     * PBI #10 - Case ID: TC.Adm.002
     * Menguji aksi Approve legalitas
     */
    public function test_admin_menyetujui_izin_operasional_vendor()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendorUser = User::factory()->create();
        Vendor::create([
            'user_id' => $vendorUser->id, 
            'company_name' => 'PT Lolos Verifikasi', 
            'status' => 'Pending'
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('#tabBtn-verifikasi')
                    ->waitForText('PT Lolos Verifikasi', 10)
                    // Buka modal tinjauan
                    ->script("document.querySelectorAll('button[onclick*=\"openReviewModal\"]')[0].click();");
            
            $browser->waitFor('#reviewModal')
                    ->pause(1000)
                    // Klik tombol setujui kemitraan di dalam modal
                    ->click('#formApprove button[type="submit"]')
                    // Menunggu notifikasi muncul dan memvalidasi teks di dalamnya
                    ->waitForText('telah diaktifkan', 10)
                    ->assertSee('PT Lolos Verifikasi'); 
        });
    }

    /**
     * PBI #11 - Case ID: TC.Adm.003
     * Menguji halaman riwayat vendor aktif
     */
    public function test_admin_memantau_daftar_stasiun_spklu()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendorUser = User::factory()->create();
        Vendor::create([
            'user_id' => $vendorUser->id, 
            'company_name' => 'PT Vendor Aktif', 
            'status' => 'Approved'
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    // Masuk ke tab manajemen (pengganti /admin/stations)
                    ->click('#tabBtn-manajemen')
                    ->waitForText('Daftar Vendor Aktif', 10)
                    ->assertSee('PT Vendor Aktif');
        });
    }

    /**
     * PBI #12 - Case ID: TC.Adm.004
     * Menguji fitur Suspend akun vendor
     */
    public function test_admin_membekukan_vendor_yang_melanggar()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendorUser = User::factory()->create();
        Vendor::create([
            'user_id' => $vendorUser->id, 
            'company_name' => 'PT Bakal Suspend', 
            'status' => 'Approved'
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('#tabBtn-manajemen')
                    ->waitForText('PT Bakal Suspend', 10)
                    ->click('form[action*="suspend"] button[type="submit"]')
                    // Handle pop-up browser
                    ->acceptDialog()
                    ->waitForText('dibekukan sementara', 10);
        });
    }

    /**
     * PBI #12 - Case ID: TC.Adm.005
     * Menguji fitur Hapus Permanen (Destroy)
     */
    public function test_admin_menghapus_data_vendor_sepenuhnya()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $vendorUser = User::factory()->create();
        Vendor::create([
            'user_id' => $vendorUser->id, 
            'company_name' => 'PT Bakal Dihapus', 
            'status' => 'Suspended'
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click('#tabBtn-manajemen')
                    ->waitForText('PT Bakal Dihapus', 10)
                    // Cari tombol hapus pada form destroy
                    ->click('form[action*="destroy"] button[type="submit"]')
                    ->acceptDialog()
                    ->waitForText('dihapus permanen', 10);
        });
    }
}