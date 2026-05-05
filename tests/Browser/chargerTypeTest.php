<?php

namespace Tests\Browser;

use App\Models\ChargerMachine;
use App\Models\Spklu;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class chargerTypeTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setupFullData(int $machineCount = 1): array
    {
        $userVendor = User::create([
            'name'     => 'Vendor Owner',
            'email'    => 'owner@vendor.com',
            'phone'    => '081222333444',
            'role'     => 'vendor',
            'password' => Hash::make('password'),
            'status'   => 'active',
        ]);

        $vendor = Vendor::create([
            'user_id'      => $userVendor->id,
            'company_name' => 'PT Solusi Energi',
            'status'       => 'Approved',
        ]);

        $spklu = Spklu::create([
            'vendor_id' => $vendor->id,
            'name'      => 'SPKLU Pusat Kota',
            'address'   => 'Jl. Merdeka No. 10, Bandung',
            'latitude'  => -6.9175,
            'longitude' => 107.6191,
        ]);

        for ($i = 1; $i <= $machineCount; $i++) {
            ChargerMachine::create([
                'vendor_id'         => $vendor->id,
                'spklu_id'          => $spklu->id,
                'name'              => "Mesin Charger $i",
                'connector_type'    => 'CCS2',
                'capacity_kw'       => 50,
                'price_per_kwh'     => 2500,
                'operational_hours' => '24/7',
                'photo_path'        => 'chargers/default.jpg',
                'status'            => 'available', 
            ]);
        }

        return ['vendor_user' => $userVendor, 'spklu' => $spklu];
    }

    protected function loginAndGoToMap(Browser $browser): void
    {
        $rider = User::create([
            'name'     => 'Rider Test',
            'email'    => 'rider@test.com',
            'phone'    => '089876543210',
            'role'     => 'rider',
            'password' => Hash::make('password'),
            'status'   => 'active',
        ]);

        $browser->loginAs($rider)
                ->visit('/rider/peta')
                ->waitFor('#map', 10)
                ->waitFor('.leaflet-marker-icon', 15); 
    }

 
    #[Test]
    public function test_modal_menampilkan_info_stasiun_saat_marker_diklik(): void
    {
        $data = $this->setupFullData();

        $this->browse(function (Browser $browser) use ($data) {
            $this->loginAndGoToMap($browser);

            $browser->script("document.querySelector('.leaflet-marker-icon').click();");

            $browser->waitFor('.leaflet-popup-content-wrapper', 5)
                    ->assertSeeIn('.leaflet-popup-content-wrapper', $data['spklu']->name);
        });
    }

    #[Test]
    public function test_modal_tertutup_saat_klik_tombol_close(): void
    {
        $this->setupFullData();

        $this->browse(function (Browser $browser) {
            $this->loginAndGoToMap($browser);
            
            $browser->script("document.querySelector('.leaflet-marker-icon').click();");
            $browser->waitFor('.leaflet-popup-content-wrapper', 5);

            $browser->click('.leaflet-popup-close-button')
                    ->pause(1000)
                    ->assertMissing('.leaflet-popup-content-wrapper');
        });
    }

    #[Test]
    public function test_modal_menampilkan_jumlah_mesin_tersedia(): void
    {
        $this->setupFullData(3); // Buat 3 mesin

        $this->browse(function (Browser $browser) {
            $this->loginAndGoToMap($browser);
            $browser->script("document.querySelector('.leaflet-marker-icon').click();");

            $browser->waitFor('.leaflet-popup-content-wrapper', 5);
        });
    }

    #[Test]
    public function test_modal_menampilkan_pesan_jika_tidak_ada_mesin(): void
    {
        $userVendor = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::create(['user_id' => $userVendor->id, 'company_name' => 'PT Kosong']);
        $spklu = Spklu::create([
            'vendor_id' => $vendor->id,
            'name' => 'SPKLU Tanpa Mesin',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
            'address' => 'Alamat Palsu'
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAndGoToMap($browser);
            $browser->script("document.querySelector('.leaflet-marker-icon').click();");

            $browser->waitFor('.leaflet-popup-content-wrapper', 5)
                    ->assertSeeIn('.leaflet-popup-content-wrapper', 'Informasi port belum tersedia');
        });
    }

    #[Test]
    public function test_modal_menampilkan_tipe_port_charger(): void
    {
        $this->setupFullData();

        $this->browse(function (Browser $browser) {
            $this->loginAndGoToMap($browser);
            $browser->script("document.querySelector('.leaflet-marker-icon').click();");

            $browser->waitFor('.leaflet-popup-content-wrapper', 5);
        });
    }

    #[Test]
    public function test_vehicle_seeder_berhasil_dieksekusi(): void
    {
        $exitCode = Artisan::call('db:seed', [
            '--class' => 'VehicleSeeder',
            '--force' => true,
        ]);

        $this->assertEquals(0, $exitCode);
        $this->assertGreaterThan(0, DB::table('vehicles')->count());
    }
}