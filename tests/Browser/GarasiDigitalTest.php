<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GarasiDigitalTest extends DuskTestCase
{
    // Menggunakan trait ini agar Dusk mereset database 'evhub_dusk_db' 
    use DatabaseMigrations;

    public function test_tambah_kendaraan_ev()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/rider/vehicles')
                    ->assertSee('Garasi Digital')
                    ->clickLink('+ Tambah Kendaraan')
                    ->waitForLocation('/rider/vehicles/create') // Tunggu halaman terbuka
                    
                    // 1. Pastikan card BYD sudah nampak di layar
                    ->waitFor('#brand-byd', 5)
                    ->click('#brand-byd')
                    
                    // 2. TUNGGU sampai kotak model-chips muncul di layar
                    ->waitFor('#models-section', 5)
                    ->pause(1000) // Ekstra jeda
                    
                    // 3. TUNGGU spesifik sampai elemen chip pertama muncul
                    ->waitFor('.model-chip:first-child', 5)
                    ->click('.model-chip:first-child')
                    
                    // 4. Pastikan input plat nomor sudah muncul
                    ->waitFor('#plate-section', 5)
                    ->pause(500)
                    ->type('license_plate', 'D 1234 TEST')
                    
                    // Tunggu tombol submit aktif
                    ->waitFor('#submit-btn:not([disabled])', 5)
                    ->press('Simpan Kendaraan')
                    
                    // TUNGGU sampai browser berpindah lokasi (Ini akan mencegah error "Actual path does not equal expected path")
                    ->waitForLocation('/rider/vehicles', 10) 
                    ->assertSee('BYD')
                    ->assertSee('D 1234 TEST');
        });
    }
}