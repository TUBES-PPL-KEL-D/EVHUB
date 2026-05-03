<?php

namespace Tests\Browser;

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
                    ->press('Simpan Kendaraan')
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
                    ->press('Simpan Perubahan')
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
                    ->press('Simpan Perubahan')
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
    }
}