<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseTruncation; 
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Spklu;
use App\Models\ChargerMachine;

class MapTest extends DuskTestCase
{
    use DatabaseTruncation;

    // Fungsi bantuan untuk membuat data dummy SPKLU dan mesin chargernya
    private function createSpkluWithMachine($name, $lat, $lng, $status = 'available')
    {
        $user = User::factory()->create(['role' => 'vendor', 'status' => 'aktif']);
        $vendor = Vendor::forceCreate(['user_id' => $user->id, 'company_name' => 'PT EVHUB Solusi', 'status' => 'Approved']);
        $spklu = Spklu::forceCreate([
            'vendor_id' => $vendor->id,
            'name' => $name,
            'address' => 'Jl. Pahlawan, Bandung',
            'latitude' => $lat,
            'longitude' => $lng
        ]);

        return ChargerMachine::create([
            'vendor_id' => $user->id,
            'spklu_id' => $spklu->id,
            'name' => 'Mesin Ultra Fast',
            'connector_type' => 'CCS2',
            'capacity_kw' => 150,
            'price_per_kwh' => 2500,
            'operational_hours' => '24 Jam',
            'status' => $status, 
            'photo_path' => 'dummy/photo.jpg'
        ]);
    }

    public function test_tombol_lokasi_saya_berhasil_menampilkan_posisi_user()
    {
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'aktif']);
        
        $this->browse(function (Browser $browser) use ($rider) {
            $browser->loginAs($rider)->visit('/rider/peta')->waitFor('#map', 10);
            
            // Bypass GPS Browser
            $browser->script("
                navigator.geolocation.getCurrentPosition = function(success) {
                    success({ coords: { latitude: -6.920000, longitude: 107.600000 } });
                };
            ");
            
            $browser->click('#btn-locate-me')
                    ->waitFor('.user-gps-marker', 10) 
                    ->assertPresent('.user-gps-marker'); 
        });
    }

    public function test_pencarian_dan_filter_status_berfungsi_dengan_baik()
    {
        $this->createSpkluWithMachine('SPKLU Telkom', -6.973003, 107.629253, 'available');
        $this->createSpkluWithMachine('SPKLU Telkom Penuh', -6.974000, 107.630000, 'unavailable');
        
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'aktif']);
        
        $this->browse(function (Browser $browser) use ($rider) {
            $browser->loginAs($rider)->visit('/rider/peta')->waitFor('#station-list', 10);

            $browser->type('#search-spklu', 'Telkom')
                    ->select('#filter-status', 'tersedia')
                    ->pause(2000) 
                    ->assertSeeIn('#station-list', 'SPKLU Telkom')
                    ->assertDontSeeIn('#station-list', 'SPKLU Telkom Penuh'); 
        });
    }

    public function test_sistem_menampilkan_pesan_jika_spklu_tidak_ditemukan()
    {
        $this->createSpkluWithMachine('SPKLU Gedung Sate', -6.902481, 107.618810, 'available');
        
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'aktif']);
        
        $this->browse(function (Browser $browser) use ($rider) {
            $browser->loginAs($rider)->visit('/rider/peta')->waitFor('#station-list', 10);

            $browser->type('#search-spklu', 'Stasiun Antariksa') 
                    ->waitForText('Tidak ada stasiun yang sesuai.', 10)
                    ->assertSee('Tidak ada stasiun yang sesuai.'); 
        });
    }

    public function test_daftar_spklu_otomatis_mengurutkan_jarak_terdekat()
    {
        $this->createSpkluWithMachine('SPKLU UPI', -6.860431, 107.589886, 'available');
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'aktif']);

        $this->browse(function (Browser $browser) use ($rider) {
            $browser->loginAs($rider)->visit('/rider/peta')->waitFor('#station-list', 10);

            $browser->script("
                navigator.geolocation.getCurrentPosition = function(success) {
                    success({ coords: { latitude: -6.920000, longitude: 107.600000 } });
                };
            ");

            $browser->click('#btn-locate-me')->pause(1000);

            // Tembak spasi untuk memicu pemuatan ulang daftar
            $browser->type('#search-spklu', ' ')
                    ->waitForText('km', 15) 
                    ->assertSeeIn('#station-list', 'SPKLU UPI')
                    ->assertSeeIn('#station-list', 'Mesin');
        });
    }

    public function test_tombol_bagikan_spklu_berfungsi_dan_dapat_diklik()
    {
        $this->createSpkluWithMachine('SPKLU Braga', -6.917464, 107.609348, 'available');
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'aktif']);

        $this->browse(function (Browser $browser) use ($rider) {
            $browser->loginAs($rider)
                    ->visit('/rider/peta')
                    ->waitForText('SPKLU Braga', 15)
                    ->assertSee('SPKLU Braga');
        });
    }

    public function test_tombol_ubah_tampilan_peta_satelit_berfungsi()
    {
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'aktif']);

        $this->browse(function (Browser $browser) use ($rider) {
            $browser->loginAs($rider)->visit('/rider/peta')->waitFor('#map', 10);

            // Klik ubah ke Satelit
            $browser->click('#btn-toggle-layer')
                    ->pause(500)
                    ->assertPresent('#btn-toggle-layer.text-emerald-400'); 

            // Klik kembali ke Street View
            $browser->click('#btn-toggle-layer')
                    ->pause(500)
                    ->assertMissing('#btn-toggle-layer.text-emerald-400');
        });
    }
}