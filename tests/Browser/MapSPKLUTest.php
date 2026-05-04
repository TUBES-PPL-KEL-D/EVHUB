<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseTruncation; 
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Spklu;
use App\Models\ChargerMachine;

class MapSPKLUTest extends DuskTestCase
{
    use DatabaseTruncation;

    private function createSpkluWithMachine($name, $lat, $lng, $status = 'available')
    {
        $user = User::factory()->create(['role' => 'vendor', 'status' => 'aktif']);
        $vendor = Vendor::forceCreate(['user_id' => $user->id, 'company_name' => 'PT EVHUB Solusi', 'status' => 'Approved']);
        $spklu = Spklu::forceCreate([
            'vendor_id' => $vendor->id,
            'name' => $name,
            'address' => 'Jl. Testing No. 1, Bandung',
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

    public function test_complete_spklu_mapping_feature()
    {
        // Start as unavailable
        $machine = $this->createSpkluWithMachine('SPKLU EV-HUB Bandung', -6.914744, 107.609810, 'unavailable');
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'aktif']);

        $this->browse(function (Browser $browser) use ($machine, $rider) {
            $browser->loginAs($rider)
                    ->visit('/rider/peta')
                    ->waitFor('#map', 10)
                    ->waitFor('.leaflet-marker-icon', 10)
                    ->click('.leaflet-marker-icon')
                    ->waitForText('SPKLU EV-HUB Bandung', 5);

            // Update status ke available di database
            $machine->update(['status' => 'available']);

            // Tunggu sampai frontend mendeteksi perubahan (Polling)
            // Kita pakai waitForText agar robot sabar menunggu sampai tulisan 'Tersedia' muncul
            $browser->waitForText('Tersedia', 10);
            
            // Verifikasi akhir
            $browser->assertSee('Tersedia');
        });
    }

    public function test_map_handles_empty_spklu_data()
    {
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'aktif']);
        $this->browse(function (Browser $browser) use ($rider) {
            $browser->loginAs($rider)->visit('/rider/peta')->waitFor('#map', 10)->pause(1000)->assertMissing('.leaflet-marker-icon'); 
        });
    }

    public function test_map_renders_multiple_spklus()
    {
        $this->createSpkluWithMachine('SPKLU Satu', -6.914744, 107.609810, 'available');
        $this->createSpkluWithMachine('SPKLU Dua', -6.921443, 107.610659, 'unavailable');

        $rider = User::factory()->create(['role' => 'rider', 'status' => 'aktif']);
        $this->browse(function (Browser $browser) use ($rider) {
            $browser->loginAs($rider)->visit('/rider/peta')->waitFor('#map', 10)->waitFor('.leaflet-marker-icon', 10); 
            $this->assertCount(2, $browser->elements('.leaflet-marker-icon'));
        });
    }
}
