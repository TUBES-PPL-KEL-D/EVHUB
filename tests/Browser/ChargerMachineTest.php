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
use Illuminate\Http\UploadedFile;

class ChargerMachineTest extends DuskTestCase
{
    use DatabaseTruncation;

    private function createDummyDataChain()
    {
        $user = User::factory()->create([
            'role' => 'vendor',
            'status' => 'aktif'
        ]);

        $vendor = new Vendor();
        $vendor->user_id = $user->id;
        $vendor->company_name = 'PT EVHUB Solusi';
        $vendor->status = 'Approved';
        $vendor->save();

        $spklu = new Spklu();
        $spklu->vendor_id = $vendor->id;
        $spklu->name = 'SPKLU Pusat ' . rand(1, 100);
        $spklu->address = 'Jl. Telekomunikasi No. 1, Bandung';
        $spklu->latitude = -6.9733;
        $spklu->longitude = 107.6305;
        $spklu->save();

        return ['user' => $user, 'vendor' => $vendor, 'spklu' => $spklu];
    }

    /**
     * PBI #15: TC.Vnd.015 - Read
     */
    public function test_TC_Vnd_015_vendor_can_view_charger_list()
    {
        $data = $this->createDummyDataChain();
        $user = $data['user'];
        $spklu = $data['spklu'];
        
        $charger = ChargerMachine::create([
            'vendor_id' => $user->id,
            'spklu_id' => $spklu->id,
            'name' => 'Mesin Ultra Fast 01',
            'connector_type' => 'CCS2',
            'capacity_kw' => 150,
            'price_per_kwh' => 2500,
            'operational_hours' => '24 Jam',
            'status' => 'available',
            'photo_path' => 'dummy/photo.jpg' // Menambahkan photo_path yang wajib
        ]);

        $this->browse(function (Browser $browser) use ($user, $charger, $spklu) {
            $browser->loginAs($user)
                    ->visit('/vendor/chargers')
                    ->assertSee('Daftar Mesin Charger')
                    ->assertSee($charger->name)
                    ->assertSee($spklu->name);
        });
    }

    /**
     * PBI #16: TC.Vnd.016.1 - Create Positive
     */
    public function test_TC_Vnd_016_1_vendor_can_create_valid_charger()
    {
        $data = $this->createDummyDataChain();
        $user = $data['user'];
        $spklu = $data['spklu'];

        Storage::fake('public');
        $file = UploadedFile::fake()->create('charger_test.jpg', 100, 'image/jpeg');

        $this->browse(function (Browser $browser) use ($user, $spklu, $file) {
            $browser->loginAs($user)
                    ->visit('/vendor/chargers/create')
                    ->type('name', 'Mesin AC Standard')
                    ->select('spklu_id', (string) $spklu->id)
                    ->type('connector_type', 'Type 2')
                    ->type('capacity_kw', '22')
                    ->type('price_per_kwh', '2000')
                    ->type('operational_hours', '08:00 - 17:00')
                    // Baris ->select('status') DIHAPUS karena otomatis 'available' di DB
                    ->attach('photo', $file->getPathname())
                    ->press('Simpan') // Asumsi tombol submit bernama "Simpan"
                    ->assertPathIs('/vendor/chargers')
                    ->assertSee('Mesin AC Standard');
        });
    }

    /**
     * PBI #16: TC.Vnd.016.2 - Create Negative (Validasi Form)
     */
    public function test_TC_Vnd_016_2_vendor_cannot_create_empty_charger()
    {
        $user = User::factory()->create(['role' => 'vendor']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/vendor/chargers/create')
                    ->press('Simpan')
                    ->assertPathIs('/vendor/chargers/create'); 
        });
    }

    /**
     * PBI #17: TC.Vnd.017 - Update
     */
    public function test_TC_Vnd_017_vendor_can_update_charger_status()
    {
        $data = $this->createDummyDataChain();
        $user = $data['user'];
        $spklu = $data['spklu'];
        
        $charger = ChargerMachine::create([
            'vendor_id' => $user->id,
            'spklu_id' => $spklu->id,
            'name' => 'Mesin Lama',
            'connector_type' => 'CCS2',
            'capacity_kw' => 50,
            'price_per_kwh' => 2500,
            'operational_hours' => '24 Jam',
            'status' => 'available',
            'photo_path' => 'dummy/photo.jpg' // Menambahkan photo_path yang wajib
        ]);

        $this->browse(function (Browser $browser) use ($user, $charger) {
            $browser->loginAs($user)
                    ->visit('/vendor/chargers/' . $charger->id . '/edit')
                    // Asumsi: <select name="status"> ada di edit.blade.php
                    ->select('status', 'maintenance')
                    ->press('Update')
                    ->assertPathIs('/vendor/chargers')
                    ->assertSee('MAINTENANCE');
        });
    }

    /**
     * PBI #18: TC.Vnd.018 - Delete
     */
    public function test_TC_Vnd_018_vendor_can_delete_charger()
    {
        $data = $this->createDummyDataChain();
        $user = $data['user'];
        $spklu = $data['spklu'];
        
        $charger = ChargerMachine::create([
            'vendor_id' => $user->id,
            'spklu_id' => $spklu->id,
            'name' => 'Mesin Rusak Total',
            'connector_type' => 'CHAdeMO',
            'capacity_kw' => 25,
            'price_per_kwh' => 2000,
            'operational_hours' => '24 Jam',
            'status' => 'maintenance',
            'photo_path' => 'dummy/photo.jpg' // Menambahkan photo_path yang wajib
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/vendor/chargers')
                    ->assertSee('Mesin Rusak Total')
                    ->press('Hapus')
                    ->acceptDialog() 
                    ->assertPathIs('/vendor/chargers')
                    ->assertDontSee('Mesin Rusak Total');
        });
    }
}