<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Vendor;
use App\Models\User;

class AdminVerificationTest extends DuskTestCase
{
    // Ini akan mereset database khusus untuk testing secara otomatis
    use DatabaseMigrations;

    public function test_alur_verifikasi_dan_manajemen_vendor()
    {
        $this->browse(function (Browser $browser) {
            // 1. Persiapan Data: Buat User dan Vendor Approved
            $user = User::factory()->create();
            
            // Kita buat vendor yang statusnya sudah 'Pending' untuk di-approve
            $vendor = Vendor::create([
                'user_id' => $user->id,
                'company_name' => 'PT Antre Verifikasi',
                'npwp' => '123456789012345',
                'address' => 'Jl. Bojongsoang No. 1',
                'status' => 'Pending',
            ]);

            // ==========================================
            // PBI 9 & 10: DASHBOARD & APPROVE
            // ==========================================
            $browser->visit('/admin/dashboard')
                    ->assertSee('PT Antre Verifikasi')
                    // Gunakan click ketimbang press untuk menghindari masalah z-index/overlay
                    ->click('form[action*="approve"] button') 
                    ->waitForText('Semua Selesai!', 10);

            // ==========================================
            // PBI 11 & 12: STASIUN, SUSPEND, DAN DESTROY
            // ==========================================
            $browser->visit('/admin/stations')
                    ->assertSee('PT Antre Verifikasi');

            // Matikan konfirmasi JS agar robot tidak berhenti
            $browser->script("window.confirm = function(){ return true; };");

            // Klik tombol SUSPEND_ACC
            $browser->click('form[action*="suspend"] button')
                    ->pause(2000)
                    // Gunakan assertSee daripada waitForText jika teks sudah ada di judul bagian
                    ->assertSee('Daftar Suspend'); 

            // Klik tombol HAPUS pada vendor yang sudah disuspend
            $browser->click('form[action*="destroy"] button')
                    ->pause(2000)
                    ->assertSee('Belum ada vendor aktif')
                    ->assertSee('Tidak ada akun yang dibekukan');
        });
    }
}