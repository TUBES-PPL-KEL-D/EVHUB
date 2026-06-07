<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesEvhTestData;
use Tests\DuskTestCase;

class PBI45VendorTariffTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreatesEvhTestData;

    public function test_vendor_can_update_price_per_kwh_for_charger_machine(): void
    {
        $vendorUser = $this->createApprovedVendorUser();

        $spklu = $this->createSpklu($vendorUser->evhub_vendor_id);

        $charger = $this->createChargerMachine($vendorUser->evhub_vendor_id, $spklu->id, [
            'price_per_kwh' => 2500,
            'status' => 'available',
        ]);

        $this->browse(function (Browser $browser) use ($vendorUser, $charger) {
            $formSelector = 'form[action="' . route('vendor.chargers.updateTariff', $charger->id) . '"]';

            $browser->loginAs($vendorUser)
                ->visit(route('vendor.chargers.index'))
                ->assertSee('Tarif Saat Ini')
                ->assertSee('Rp2.500/kWh')
                ->within($formSelector, function (Browser $form) {
                    $form->clear('price_per_kwh')
                        ->type('price_per_kwh', '3500')
                        ->click('button[type="submit"]');
                })
                ->waitForText('Tarif harga per kWh berhasil diperbarui', 5)
                ->assertSee('Tarif harga per kWh berhasil diperbarui')
                ->assertSee('Rp3.500/kWh');
        });

        $this->assertDatabaseHas('charger_machines', [
            'id' => $charger->id,
            'price_per_kwh' => 3500,
        ]);
    }

    public function test_vendor_cannot_update_tariff_with_negative_value(): void
    {
        $vendorUser = $this->createApprovedVendorUser();

        $spklu = $this->createSpklu($vendorUser->evhub_vendor_id);

        $charger = $this->createChargerMachine($vendorUser->evhub_vendor_id, $spklu->id, [
            'price_per_kwh' => 2500,
            'status' => 'available',
        ]);

        $this->browse(function (Browser $browser) use ($vendorUser, $charger) {
            $formSelector = 'form[action="' . route('vendor.chargers.updateTariff', $charger->id) . '"]';

            $browser->loginAs($vendorUser)
                ->visit(route('vendor.chargers.index'))
                ->assertSee('Tarif Saat Ini')
                ->assertSee('Rp2.500/kWh')
                ->within($formSelector, function (Browser $form) {
                    $form->clear('price_per_kwh')
                        ->type('price_per_kwh', '-1000')
                        ->click('button[type="submit"]');
                })
                ->pause(1000);
        });

        $this->assertDatabaseHas('charger_machines', [
            'id' => $charger->id,
            'price_per_kwh' => 2500,
        ]);
    }
}