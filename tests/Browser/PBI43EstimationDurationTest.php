<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesEvhTestData;
use Tests\DuskTestCase;

class PBI43EstimationDurationTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreatesEvhTestData;

    public function test_rider_can_see_estimated_charging_duration_based_on_machine_power(): void
    {
        $vendorUser = $this->createApprovedVendorUser();
        $rider = $this->createRider();

        $spklu = $this->createSpklu($vendorUser->evhub_vendor_id);

        $machine = $this->createChargerMachine($vendorUser->evhub_vendor_id, $spklu->id, [
            'capacity_kw' => 20,
            'price_per_kwh' => 3500,
            'status' => 'available',
        ]);

        $this->createVehicle($rider->id, [
            'connector_type' => $machine->connector_type,
        ]);

        $this->browse(function (Browser $browser) use ($rider, $machine) {
            $browser->loginAs($rider)
                ->visit(route('rider.transactions.prepare', $machine->id))
                ->assertSee('Konfigurasi Pengisian')
                ->assertSee('Target Pengisian')
                ->type('energy_target', '30')
                ->waitForText('1 jam 30 menit', 5)
                ->assertSee('1 jam 30 menit');
        });
    }

    public function test_estimated_duration_changes_when_energy_target_is_changed(): void
    {
        $vendorUser = $this->createApprovedVendorUser();
        $rider = $this->createRider();

        $spklu = $this->createSpklu($vendorUser->evhub_vendor_id);

        $machine = $this->createChargerMachine($vendorUser->evhub_vendor_id, $spklu->id, [
            'capacity_kw' => 20,
            'price_per_kwh' => 3500,
            'status' => 'available',
        ]);

        $this->createVehicle($rider->id, [
            'connector_type' => $machine->connector_type,
        ]);

        $this->browse(function (Browser $browser) use ($rider, $machine) {
            $browser->loginAs($rider)
                ->visit(route('rider.transactions.prepare', $machine->id))
                ->assertSee('Konfigurasi Pengisian')
                ->assertSee('Target Pengisian')
                ->type('energy_target', '10')
                ->waitForText('30 menit', 5)
                ->assertSee('30 menit')
                ->clear('energy_target')
                ->type('energy_target', '20')
                ->waitForText('1 jam', 5)
                ->assertSee('1 jam');
        });
    }
}