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
        $user = User::factory()->create();
        Vendor::create([
            'user_id' => $user->id,
            'company_name' => 'PT Antre Verifikasi',
            'npwp' => '123456789012345',
            'address' => 'Jl. Bojongsoang No. 1',
            'status' => 'Pending',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/dashboard')
                    ->assertSee('Antrean Dokumen')
                    ->assertSee('PT Antre Verifikasi');
        });
    }

    /**
     * PBI #10 - Case ID: TC.Adm.002
     * Menguji aksi Approve legalitas
     */
    public function test_admin_menyetujui_izin_operasional_vendor()
    {
        $user = User::factory()->create();
        Vendor::create([
            'user_id' => $user->id, 
            'company_name' => 'PT Lolos Verifikasi', 
            'npwp' => '1', 
            'address' => 'A', 
            'status' => 'Pending'
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/dashboard')
                    ->assertSee('PT Lolos Verifikasi')
                    ->press('TERIMA')
                    ->pause(3000) // Memberi waktu proses database
                    ->refresh()   // Paksa refresh untuk membersihkan DOM lama
                    ->waitForText('Semua Selesai!', 10)
                    ->assertDontSee('PT Lolos Verifikasi');
        });
    }

    /**
     * PBI #11 - Case ID: TC.Adm.003
     * Menguji halaman riwayat vendor aktif
     */
    public function test_admin_memantau_daftar_stasiun_spklu()
    {
        $user = User::factory()->create();
        Vendor::create([
            'user_id' => $user->id, 
            'company_name' => 'PT Vendor Aktif', 
            'npwp' => '2', 
            'address' => 'B', 
            'status' => 'Approved'
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/stations')
                    ->assertSee('Vendor Aktif')
                    ->assertSee('PT Vendor Aktif');
        });
    }

    /**
     * PBI #12 - Case ID: TC.Adm.004
     * Menguji fitur Suspend akun vendor
     */
    public function test_admin_membekukan_vendor_yang_melanggar()
    {
        $user = User::factory()->create();
        Vendor::create([
            'user_id' => $user->id, 
            'company_name' => 'PT Bakal Suspend', 
            'npwp' => '3', 
            'address' => 'C', 
            'status' => 'Approved'
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/stations')
                    ->script("window.confirm = function(){ return true; };"); // Bypass dialog
            
            $browser->press('SUSPEND_ACC')
                    ->pause(2000)
                    ->waitForText('AKUN DITANGGUHKAN', 10);
        });
    }

    /**
     * PBI #12 - Case ID: TC.Adm.005
     * Menguji fitur Hapus Permanen (Destroy)
     */
    public function test_admin_menghapus_data_vendor_sepenuhnya()
    {
        $user = User::factory()->create();
        Vendor::create([
            'user_id' => $user->id, 
            'company_name' => 'PT Bakal Dihapus', 
            'npwp' => '4', 
            'address' => 'D', 
            'status' => 'Suspended'
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/stations')
                    ->script("window.confirm = function(){ return true; };"); // Bypass dialog
            
            $browser->press('HAPUS')
                    ->pause(2000)
                    ->waitForText('BELUM ADA VENDOR AKTIF', 10);
        });
    }
}