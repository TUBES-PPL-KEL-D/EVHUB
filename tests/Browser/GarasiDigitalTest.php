<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Vehicle;
use App\Models\Spklu;
use App\Models\ChargerMachine;
use App\Services\ConnectorMatchingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GarasiDigitalTest extends DuskTestCase
{
    use DatabaseMigrations;

    // TC.Garasi.001 - Positive
    public function test_pengendara_can_add_new_ev_to_garage()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->assertSee('Garasi Digital')
                    ->clickLink('+ Tambah Kendaraan')
                    ->waitForLocation('/rider/vehicles/create')
                    ->waitForText('BYD', 5)
                    ->click('.brand-card:nth-child(2)')
                    ->waitFor('#models-section', 5)
                    ->waitFor('.model-chip', 5)
                    ->pause(300)
                    ->click('.model-chip:first-child')
                    ->waitFor('#plate-section', 5)
                    ->pause(300)
                    ->type('#license_plate', 'B 1234 CD')
                    ->waitFor('#submit-btn:not([disabled])', 5)
                    ->click('#submit-btn')
                    ->waitForLocation('/rider/vehicles', 10)
                    ->assertSee('BYD')
                    ->assertSee('Dolphin');
        });
    }

    // TC.Garasi.002 - Negative
    public function test_pengendara_cannot_submit_without_selecting_brand_and_model()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles/create')
                    ->waitForText('BYD', 5)
                    ->assertPresent('#submit-btn[disabled]')
                    ->assertMissing('#models-section[style="display: block;"]')
                    ->assertMissing('#plate-section[style="display: block;"]');
        });
    }

    // TC.Garasi.003 - Positive
    public function test_pengendara_can_view_their_ev_list()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles');

            Vehicle::create([
                'user_id'       => $user->id,
                'merk'          => 'Hyundai',
                'model'         => 'Ioniq 5',
                'license_plate' => 'D 1234 EV',
            ]);

            $browser->visit('/rider/vehicles')
                    ->waitForText('Hyundai', 5)
                    ->assertSee('Hyundai')
                    ->assertSee('Ioniq 5');
        });
    }

    // TC.Garasi.004 - Negative
    public function test_pengendara_sees_empty_state_when_no_ev_registered()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->pause(500)
                    ->assertSee('Belum ada kendaraan EV di garasi Anda.');
        });
    }

    // TC.Garasi.005 - Positive
    public function test_pengendara_can_update_ev_specification()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles');

            $vehicle = Vehicle::create([
                'user_id'       => $user->id,
                'merk'          => 'Wuling',
                'model'         => 'Air EV Long Range',
                'license_plate' => 'B 9999 XX',
            ]);

            $browser->visit('/rider/vehicles/' . $vehicle->id . '/edit')
                    ->waitFor('#submit-btn', 5)
                    ->waitFor('.model-chip.selected', 5)
                    ->tap(function ($browser) {
                        $browser->script([
                            "document.getElementById('model-hidden').value = 'BinguoEV';",
                            "document.getElementById('sum-model').textContent = 'BinguoEV';",
                            "checkForm();",
                        ]);
                    })
                    ->waitFor('#submit-btn:not([disabled])', 5)
                    ->click('#submit-btn')
                    ->waitForLocation('/rider/vehicles', 10)
                    ->assertSee('BinguoEV')
                    ->assertDontSee('Air EV Long Range');
        });
    }

    // TC.Garasi.006 - Negative
    public function test_pengendara_cannot_update_ev_with_duplicate_license_plate()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles');

            Vehicle::create([
                'user_id'       => $user->id,
                'merk'          => 'Hyundai',
                'model'         => 'Ioniq 5',
                'license_plate' => 'D 1111 AA',
            ]);

            $vehicle = Vehicle::create([
                'user_id'       => $user->id,
                'merk'          => 'Wuling',
                'model'         => 'Air EV Long Range',
                'license_plate' => 'B 9999 XX',
            ]);

            $browser->visit('/rider/vehicles/' . $vehicle->id . '/edit')
                    ->waitFor('#license_plate', 5)
                    ->clear('#license_plate')
                    ->type('#license_plate', 'D 1111 AA')
                    ->tap(function ($browser) {
                        $browser->script(["checkForm();"]);
                    })
                    ->waitFor('#submit-btn:not([disabled])', 5)
                    ->click('#submit-btn')
                    ->pause(500)
                    ->assertPathContains('/edit')
                    ->assertSee('The license plate has already been taken.');
        });
    }

    // TC.Garasi.007 - Positive
    public function test_pengendara_can_delete_ev_from_garage()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles');

            Vehicle::create([
                'user_id'       => $user->id,
                'merk'          => 'Nissan',
                'model'         => 'Leaf',
                'license_plate' => 'L 3400 F',
            ]);

            $browser->visit('/rider/vehicles')
                    ->waitForText('Nissan', 5)
                    ->assertSee('Nissan')
                    ->press('Hapus')
                    ->waitForDialog()
                    ->acceptDialog()
                    ->waitForLocation('/rider/vehicles', 10)
                    ->assertDontSee('Nissan Leaf');
        });
    }

    // TC.Garasi.008 - Negative
    public function test_pengendara_can_cancel_delete_ev_from_garage()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles');

            Vehicle::create([
                'user_id'       => $user->id,
                'merk'          => 'Toyota',
                'model'         => 'bZ4X',
                'license_plate' => 'B 4321 TX',
            ]);

            $browser->visit('/rider/vehicles')
                    ->waitForText('Toyota', 5)
                    ->assertSee('Toyota')
                    ->press('Hapus')
                    ->waitForDialog()
                    ->dismissDialog()
                    ->pause(500)
                    ->assertSee('Toyota')
                    ->assertSee('bZ4X');
        });

    // PBI 55 
    }
    public function test_pengendara_dapat_mencocokkan_connector_ev_dengan_stasiun_spklu()
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();

        $spklu = Spklu::create([
            'vendor_id' => $vendor->id,
            'name'      => 'SPKLU Cikarang',
            'address'   => 'Jalan Raya Cikarang No. 12',
            'latitude'  => -6.318,
            'longitude' => 107.411,
        ]);

        ChargerMachine::create([
            'vendor_id'        => $vendor->id,
            'spklu_id'         => $spklu->id,
            'name'             => 'Charger CCS 150kW',
            'connector_type'   => 'CCS',
            'capacity_kw'      => 150,
            'price_per_kwh'    => 2500,
            'status'           => 'available',
            'operational_hours'=> '24/7',
            'photo_path'       => 'chargers/dummy.jpg',
        ]);

        ChargerMachine::create([
            'vendor_id'        => $vendor->id,
            'spklu_id'         => $spklu->id,
            'name'             => 'Charger Type2 22kW',
            'connector_type'   => 'Type2',
            'capacity_kw'      => 22,
            'price_per_kwh'    => 2000,
            'status'           => 'available',
            'operational_hours'=> '24/7',
            'photo_path'       => 'chargers/dummy.jpg',
        ]);

        $vehicle = Vehicle::create([
            'user_id'        => $user->id,
            'merk'           => 'Hyundai',
            'model'          => 'Ioniq 5',
            'license_plate'  => 'D 1234 EV',
            'connector_type' => 'CCS',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->assertSee('Garasi Digital')
                    ->assertSee('Hyundai')
                    ->assertSee('Ioniq 5');
        });

        $matchingStatus = ConnectorMatchingService::getMatchingStatus($vehicle, $spklu);

        $this->assertTrue($matchingStatus['is_compatible']);
        $this->assertSame(1, $matchingStatus['count']);
    }
    public function test_pengendara_mendapati_tidak_cocok_jika_tidak_ada_charger_yang_sesuai()
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();

        $spklu = Spklu::create([
            'vendor_id' => $vendor->id,
            'name'      => 'SPKLU Bekasi',
            'address'   => 'Jl. Bekasi Timur No. 8',
            'latitude'  => -6.222,
            'longitude' => 107.002,
        ]);

        ChargerMachine::create([
            'vendor_id'        => $vendor->id,
            'spklu_id'         => $spklu->id,
            'name'             => 'Charger Type2 22kW',
            'connector_type'   => 'Type2',
            'capacity_kw'      => 22,
            'price_per_kwh'    => 2000,
            'status'           => 'available',
            'operational_hours'=> '24/7',
            'photo_path'       => 'chargers/dummy.jpg',
        ]);

        $vehicle = Vehicle::create([
            'user_id'        => $user->id,
            'merk'           => 'Tesla',
            'model'          => 'Model 3',
            'license_plate'  => 'B 5678 TS',
            'connector_type' => 'CCS',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->assertSee('Garasi Digital')
                    ->assertSee('Tesla')
                    ->assertSee('Model 3');
        });

        $matchingStatus = ConnectorMatchingService::getMatchingStatus($vehicle, $spklu);

        $this->assertFalse($matchingStatus['is_compatible']);
        $this->assertSame(0, $matchingStatus['count']);
    }
    public function test_pengendara_dapat_mencocokkan_connector_type2_dengan_dua_charger_yang_tersedia()
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();

        $spklu = Spklu::create([
            'vendor_id' => $vendor->id,
            'name'      => 'SPKLU Senayan',
            'address'   => 'Jl. Asia Afrika No. 1',
            'latitude'  => -6.227,
            'longitude' => 106.807,
        ]);

        ChargerMachine::create([
            'vendor_id'        => $vendor->id,
            'spklu_id'         => $spklu->id,
            'name'             => 'Charger Type2 22kW',
            'connector_type'   => 'Type2',
            'capacity_kw'      => 22,
            'price_per_kwh'    => 2200,
            'status'           => 'available',
            'operational_hours'=> '24/7',
            'photo_path'       => 'chargers/dummy.jpg',
        ]);

        ChargerMachine::create([
            'vendor_id'        => $vendor->id,
            'spklu_id'         => $spklu->id,
            'name'             => 'Charger Type2 11kW',
            'connector_type'   => 'Type2',
            'capacity_kw'      => 11,
            'price_per_kwh'    => 1800,
            'status'           => 'available',
            'operational_hours'=> '24/7',
            'photo_path'       => 'chargers/dummy.jpg',
        ]);

        $vehicle = Vehicle::create([
            'user_id'        => $user->id,
            'merk'           => 'Mitsubishi',
            'model'          => 'Outlander PHEV',
            'license_plate'  => 'B 3456 EV',
            'connector_type' => 'Type2',
        ]);

        $matchingStatus = ConnectorMatchingService::getMatchingStatus($vehicle, $spklu);

        $this->assertTrue($matchingStatus['is_compatible']);
        $this->assertSame(2, $matchingStatus['count']);
    }
    public function test_pengendara_mendapati_tidak_cocok_jika_charger_sesuai_tidak_tersedia()
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();

        $spklu = Spklu::create([
            'vendor_id' => $vendor->id,
            'name'      => 'SPKLU Sudirman',
            'address'   => 'Jl. Jenderal Sudirman No. 10',
            'latitude'  => -6.222,
            'longitude' => 106.814,
        ]);

        ChargerMachine::create([
            'vendor_id'        => $vendor->id,
            'spklu_id'         => $spklu->id,
            'name'             => 'Charger Type2 22kW',
            'connector_type'   => 'Type2',
            'capacity_kw'      => 22,
            'price_per_kwh'    => 2100,
            'status'           => 'maintenance',
            'operational_hours'=> '24/7',
            'photo_path'       => 'chargers/dummy.jpg',
        ]);

        $vehicle = Vehicle::create([
            'user_id'        => $user->id,
            'merk'           => 'Renault',
            'model'          => 'Zoe',
            'license_plate'  => 'B 7890 ZO',
            'connector_type' => 'Type2',
        ]);

        $matchingStatus = ConnectorMatchingService::getMatchingStatus($vehicle, $spklu);

        $this->assertFalse($matchingStatus['is_compatible']);
        $this->assertSame(0, $matchingStatus['count']);
    }

    // PBI 56 - 
    
    public function test_pengendara_melihat_notifikasi_servis_baterai_ketika_jadwal_servis_mendekat()
    {
        $user = User::factory()->create();
        $serviceDate = Carbon::now()->addDays(5)->format('d M Y');

        Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Renault',
            'model' => 'Zoe',
            'license_plate' => 'B 5678 TS',
            'battery_service_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'battery_percentage' => 60,
            'estimated_full_range_km' => 250,
        ]);

        $this->browse(function (Browser $browser) use ($user, $serviceDate) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->assertSee('Pengingat Servis Baterai')
                    ->assertSee('Jadwal servis:')
                    ->assertSee($serviceDate);
        });
    }

    
    public function test_pengendara_melihat_banner_notifikasi_ketika_ada_kendaraan_yang_perlu_servis()
    {
        $user = User::factory()->create();

        Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Hyundai',
            'model' => 'Ioniq 5',
            'license_plate' => 'D 1234 EV',
            'battery_service_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'battery_percentage' => 45,
            'estimated_full_range_km' => 300,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->assertSee('Pengingat Servis Baterai')
                    ->assertSee('Ada 1 kendaraan yang perlu pemeriksaan servis baterai segera.')
                    ->assertSee('Periksa jadwal servis baterai Anda');
        });
    }

    
    public function test_pengendara_tidak_melihat_notifikasi_ketika_semua_jadwal_servis_masih_jauh()
    {
        $user = User::factory()->create();

        Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Toyota',
            'model' => 'bZ4X',
            'license_plate' => 'B 4321 TX',
            'battery_service_date' => Carbon::now()->addDays(60)->format('Y-m-d'),
            'battery_percentage' => 80,
            'estimated_full_range_km' => 300,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->assertDontSee('Pengingat Servis Baterai');
        });
    }

    public function test_pengendara_tidak_melihat_notifikasi_ketika_tidak_ada_jadwal_servis()
    {
        $user = User::factory()->create();

        Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Nissan',
            'model' => 'Leaf',
            'license_plate' => 'D 3456 EV',
            'battery_percentage' => 55,
            'estimated_full_range_km' => 280,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->assertDontSee('Pengingat Servis Baterai')
                    ->assertSee('Jadwal servis baterai belum diatur');
        });
    }

    // PBI 57 
    
    public function test_pengendara_melihat_kalkulasi_jarak_tempuh_dengan_persentase_baterai()
    {
        $user = User::factory()->create();

        Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'BYD',
            'model' => 'Dolphin',
            'license_plate' => 'B 1111 EV',
            'battery_percentage' => 50,
            'estimated_full_range_km' => 400,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->waitForText('BYD', 5)
                    ->pause(500)
                    ->assertSee('200 km')
                    ->assertSee('50% baterai')
                    ->assertSee('Jarak penuh: 400 km');
        });
    }

    
    public function test_pengendara_melihat_kalkulasi_jarak_tempuh_dengan_baterai_penuh()
    {
        $user = User::factory()->create();

        Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Tesla',
            'model' => 'Model Y',
            'license_plate' => 'D 2222 EV',
            'battery_percentage' => 100,
            'estimated_full_range_km' => 500,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->waitForText('Tesla', 5)
                    ->pause(500)
                    ->assertSee('500 km')
                    ->assertSee('100% baterai')
                    ->assertSee('Jarak penuh: 500 km');
        });
    }

    
    public function test_pengendara_melihat_pesan_jika_belum_input_jarak_penuh()
    {
        $user = User::factory()->create();

        Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Hyundai',
            'model' => 'Ioniq 5',
            'license_plate' => 'B 3333 EV',
            'battery_percentage' => 75,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->waitForText('Hyundai', 5)
                    ->assertSee('Persentase baterai terdaftar: 75%')
                    ->assertSee('Tambahkan perkiraan jarak penuh kendaraan di menu edit untuk menghitung sisa jarak.');
        });
    }

    
    public function test_pengendara_melihat_pesan_jika_belum_input_data_baterai()
    {
        $user = User::factory()->create();

        Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Wuling',
            'model' => 'Air EV',
            'license_plate' => 'B 4444 EV',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/vehicles')
                    ->waitForText('Wuling', 5)
                    ->assertSee('Tambahkan persentase baterai dan perkiraan jarak penuh kendaraan di menu edit untuk melihat kalkulasi jarak tempuh.');
        });
    }

    // PBI 58 
    
    public function test_pengendara_dapat_mengunggah_foto_kendaraan_saat_edit()
    {
        $user = User::factory()->create();

        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Hyundai',
            'model' => 'Ioniq 5',
            'license_plate' => 'D 5555 EV',
            'battery_percentage' => 75,
            'estimated_full_range_km' => 350,
        ]);

        $testImagePath = base_path('tests/resources/test-image.png');
        
        // Ciptakan test image jika belum ada
        if (!file_exists(dirname($testImagePath))) {
            mkdir(dirname($testImagePath), 0755, true);
        }
        if (!file_exists($testImagePath)) {
            // Buat image placeholder 1x1 pixel PNG
            $img = imagecreatetruecolor(1, 1);
            imagepng($img, $testImagePath);
            imagedestroy($img);
        }

        $this->browse(function (Browser $browser) use ($vehicle, $testImagePath) {
            $browser->loginAs(User::first())
                    ->visit('/rider/vehicles/' . $vehicle->id . '/edit')
                    ->waitForText('Hyundai', 5)
                    ->waitFor('#vehicle_photo', 5)
                    ->attach('#vehicle_photo', $testImagePath)
                    ->pause(500)
                    ->click('#submit-btn')
                    ->waitForLocation('/rider/vehicles', 10)
                    ->assertSee('Data kendaraan EV berhasil diperbarui.');
        });

        // Verifikasi foto tersimpan di database
        $updatedVehicle = Vehicle::find($vehicle->id);
        $this->assertNotNull($updatedVehicle->vehicle_photo_path);
    }

    
    public function test_pengendara_dapat_mengganti_foto_kendaraan_yang_sudah_ada()
    {
        $user = User::factory()->create();

        // Buat test image pertama
        $testImagePath = base_path('tests/resources/test-image.png');
        if (!file_exists(dirname($testImagePath))) {
            mkdir(dirname($testImagePath), 0755, true);
        }
        if (!file_exists($testImagePath)) {
            $img = imagecreatetruecolor(1, 1);
            imagepng($img, $testImagePath);
            imagedestroy($img);
        }

        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'BYD',
            'model' => 'Dolphin',
            'license_plate' => 'B 6666 EV',
            'battery_percentage' => 80,
            'estimated_full_range_km' => 320,
            'vehicle_photo_path' => 'vehicle_photos/old-photo.png',
        ]);

        $testImagePath2 = base_path('tests/resources/test-image-2.jpg');
        if (!file_exists($testImagePath2)) {
            $img = imagecreatetruecolor(10, 10);
            imagejpeg($img, $testImagePath2, 95);
            imagedestroy($img);
        }

        $this->browse(function (Browser $browser) use ($vehicle, $testImagePath2) {
            $browser->loginAs(User::first())
                    ->visit('/rider/vehicles/' . $vehicle->id . '/edit')
                    ->waitForText('BYD', 5)
                    ->waitFor('#vehicle_photo', 5)
                    ->attach('#vehicle_photo', $testImagePath2)
                    ->pause(500)
                    ->click('#submit-btn')
                    ->waitForLocation('/rider/vehicles', 10)
                    ->assertSee('Data kendaraan EV berhasil diperbarui.');
        });
    }

    
    public function test_pengendara_tidak_dapat_mengunggah_file_bukan_gambar()
    {
        $user = User::factory()->create();

        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Toyota',
            'model' => 'bZ4X',
            'license_plate' => 'B 7777 EV',
        ]);

        
        $testFilePath = base_path('tests/resources/test-file.txt');
        if (!file_exists(dirname($testFilePath))) {
            mkdir(dirname($testFilePath), 0755, true);
        }
        file_put_contents($testFilePath, 'This is a test file, not an image.');

        $this->browse(function (Browser $browser) use ($vehicle, $testFilePath) {
            $browser->loginAs(User::first())
                    ->visit('/rider/vehicles/' . $vehicle->id . '/edit')
                    ->waitForText('Toyota', 5)
                    ->waitFor('#vehicle_photo', 5)
                    ->attach('#vehicle_photo', $testFilePath)
                    ->pause(500)
                    ->click('#submit-btn')
                    ->pause(500)
                    ->assertPathContains('/edit')
                    ->assertSee('The vehicle photo field must be an image');
        });
    }

    
    public function test_pengendara_melihat_pesan_validasi_jika_format_file_tidak_didukung()
    {
        $user = User::factory()->create();

        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Nissan',
            'model' => 'Leaf',
            'license_plate' => 'L 8888 EV',
        ]);

        // Buat file dengan format yang tidak didukung (svg)
        $testFilePath = base_path('tests/resources/test-file.svg');
        if (!file_exists(dirname($testFilePath))) {
            mkdir(dirname($testFilePath), 0755, true);
        }
        file_put_contents($testFilePath, '<svg></svg>');

        $this->browse(function (Browser $browser) use ($vehicle, $testFilePath) {
            $browser->loginAs(User::first())
                    ->visit('/rider/vehicles/' . $vehicle->id . '/edit')
                    ->waitForText('Nissan', 5)
                    ->waitFor('#vehicle_photo', 5)
                    ->attach('#vehicle_photo', $testFilePath)
                    ->pause(500)
                    ->click('#submit-btn')
                    ->pause(500)
                    ->assertPathContains('/edit');
        });
    }
}