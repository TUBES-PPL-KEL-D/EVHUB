<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Spklu;
use App\Models\ChargerMachine;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Storage;

class ChargerMachineTest extends DuskTestCase
{
    use DatabaseTruncation;

    /**
     * Trik untuk menghasilkan file JPG valid 1x1 pixel tanpa butuh PHP GD.
     * Sangat berguna untuk environment yang ekstensinya mati/terbatas.
     */
    private function getValidJpgPath()
    {
        $path = storage_path('framework/testing/valid_dummy.jpg');
        if (!file_exists($path)) {
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            // Base64 murni dari gambar JPG 1x1 pixel
            $base64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';
            file_put_contents($path, base64_decode($base64));
        }
        return $path;
    }

    /**
     * Pembangun Data Dummy: Memastikan Vendor ID 1 selalu ada
     * karena Controller kita sedang menggunakan bypass pre-production.
     */
    private function createVendorId1()
    {
        $user = User::factory()->create([
            'role' => 'vendor',
            'status' => 'aktif'
        ]);

        // Paksa pembuatan Vendor dengan ID 1
        $vendor = Vendor::updateOrCreate(
            ['id' => 1],
            [
                'user_id' => $user->id,
                'company_name' => 'PT EVHUB Solusi Terpadu',
                'status' => 'Approved'
            ]
        );

        return ['user' => $user, 'vendor' => $vendor];
    }

    /**
     * PBI #15: TC.Vnd.015 - Read
     */
    public function test_TC_Vnd_015_vendor_can_view_charger_list()
    {
        $data = $this->createVendorId1();
        
        $spklu = Spklu::create([
            'vendor_id' => $data['vendor']->id,
            'name' => 'SPKLU Rest Area KM 97',
            'address' => 'Tol Cipularang',
            'latitude' => -6.9733,
            'longitude' => 107.6305,
        ]);
        
        $charger = ChargerMachine::create([
            'vendor_id' => $data['vendor']->id,
            'spklu_id' => $spklu->id,
            'name' => 'Mesin Ultra Fast 01',
            'connector_type' => 'CCS2',
            'capacity_kw' => 150,
            'price_per_kwh' => 2500,
            'operational_hours' => '24 Jam',
            'status' => 'available',
            'photo_path' => 'dummy/photo.jpg'
        ]);

        $this->browse(function (Browser $browser) use ($data, $charger, $spklu) {
            $browser->loginAs($data['user'])
                    ->visit('/vendor/chargers')
                    ->waitForText('Daftar Mesin Charger')
                    ->assertSee($charger->name)
                    ->assertSee($spklu->name);
        });
    }

    /**
     * PBI #16: TC.Vnd.016.1 - Create Positive
     */
    public function test_TC_Vnd_016_1_vendor_can_create_valid_charger()
    {
        $data = $this->createVendorId1();
        Storage::fake('public');
        $photoPath = $this->getValidJpgPath();

        $this->browse(function (Browser $browser) use ($data, $photoPath) {
            $browser->loginAs($data['user'])
                    ->visit('/vendor/chargers/create')
                    ->waitForText('Tambah Infrastruktur')
                    // 1. Isi Data SPKLU
                    ->type('spklu_name', 'SPKLU Buah Batu')
                    ->type('address', 'Jl. Terusan Buah Batu, Bandung')
                    // Injeksi JS untuk Bypass interaksi Map Leaflet
                    ->script([
                        "document.getElementById('latitude').value = '-6.9730';",
                        "document.getElementById('longitude').value = '107.6303';"
                    ]);

            $browser// 2. Isi Data Mesin
                    ->type('name', 'Mesin AC Standard Baru')
                    ->type('connector_type', 'Type 2')
                    ->type('capacity_kw', '22')
                    ->type('price_per_kwh', '2000')
                    ->type('operational_hours', '08:00 - 17:00')
                    ->attach('photo', $photoPath)
                    ->click('button[type="submit"]') 
                    // 3. Verifikasi
                    ->waitForLocation('/vendor/chargers')
                    ->assertSee('Infrastruktur SPKLU dan Mesin berhasil diletakkan pada peta!')
                    ->assertSee('Mesin AC Standard Baru')
                    ->assertSee('SPKLU Buah Batu');
        });
    }

    /**
     * PBI #16: TC.Vnd.016.2 - Create Negative (Validasi Form)
     */
    public function test_TC_Vnd_016_2_vendor_cannot_create_empty_charger()
    {
        $data = $this->createVendorId1();

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['user'])
                    ->visit('/vendor/chargers/create')
                    ->waitForText('Tambah Infrastruktur')
                    ->click('button[type="submit"]')
                    ->assertPathIs('/vendor/chargers/create'); 
        });
    }

    /**
     * PBI #17: TC.Vnd.017 - Update
     */
    public function test_TC_Vnd_017_vendor_can_update_charger_status()
    {
        $data = $this->createVendorId1();
        
        $spklu = Spklu::create([
            'vendor_id' => $data['vendor']->id,
            'name' => 'SPKLU Test Edit',
            'address' => 'Bandung',
            'latitude' => -6.9733,
            'longitude' => 107.6305,
        ]);
        
        $charger = ChargerMachine::create([
            'vendor_id' => $data['vendor']->id,
            'spklu_id' => $spklu->id,
            'name' => 'Mesin Lama',
            'connector_type' => 'CCS2',
            'capacity_kw' => 50,
            'price_per_kwh' => 2500,
            'operational_hours' => '24 Jam',
            'status' => 'available',
            'photo_path' => 'dummy/photo.jpg'
        ]);

        $this->browse(function (Browser $browser) use ($data, $charger) {
            $browser->loginAs($data['user'])
                    ->visit('/vendor/chargers/' . $charger->id . '/edit')
                    ->waitForText('Edit Mesin Charger')
                    ->select('status', 'maintenance')
                    ->pause(500) 
                    ->click('button[type="submit"]')
                    ->waitForLocation('/vendor/chargers')
                    ->assertSee('MAINTENANCE');
        });
    }

    /**
     * PBI #18: TC.Vnd.018 - Delete
     */
    public function test_TC_Vnd_018_vendor_can_delete_charger()
    {
        $data = $this->createVendorId1();
        
        $spklu = Spklu::create([
            'vendor_id' => $data['vendor']->id,
            'name' => 'SPKLU Akan Dihapus',
            'address' => 'Bandung',
            'latitude' => -6.9733,
            'longitude' => 107.6305,
        ]);
        
        $charger = ChargerMachine::create([
            'vendor_id' => $data['vendor']->id,
            'spklu_id' => $spklu->id,
            'name' => 'Mesin Rusak Total',
            'connector_type' => 'CHAdeMO',
            'capacity_kw' => 25,
            'price_per_kwh' => 2000,
            'operational_hours' => '24 Jam',
            'status' => 'maintenance',
            'photo_path' => 'dummy/photo.jpg'
        ]);

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['user'])
                    ->visit('/vendor/chargers')
                    ->waitForText('Mesin Rusak Total')
                    ->press('Hapus')
                    ->acceptDialog() 
                    ->waitForLocation('/vendor/chargers')
                    ->assertDontSee('Mesin Rusak Total');
        });
    }
}