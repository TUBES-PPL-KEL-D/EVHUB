<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesEvhTestData;
use Tests\DuskTestCase;

class PBI46UsageHistoryTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreatesEvhTestData;

    public function test_vendor_can_view_machine_usage_history(): void
    {
        $vendorUser = $this->createApprovedVendorUser();
        $rider = $this->createRider();

        $spklu = $this->createSpklu($vendorUser->evhub_vendor_id, [
            'name' => 'SPKLU Riwayat Test',
        ]);

        $charger = $this->createChargerMachine($vendorUser->evhub_vendor_id, $spklu->id, [
            'name' => 'Charger Riwayat Test',
            'price_per_kwh' => 3500,
        ]);

        $vehicle = $this->createVehicle($rider->id, [
            'merk' => 'Wuling',
            'model' => 'Air EV',
            'license_plate' => 'D 1234 UAT',
        ]);

        $this->createTransaction(
            userId: $rider->id,
            chargerMachineId: $charger->id,
            vehicleId: $vehicle->id,
            status: 'success',
            energy: 20,
            totalPrice: 70000
        );

        $this->browse(function (Browser $browser) use ($vendorUser) {
            $browser->loginAs($vendorUser)
                ->visit(route('vendor.chargers.usageHistory'))
                ->assertSee('Riwayat Pemakaian Mesin')
                ->assertSee('Charger Riwayat Test')
                ->assertSee('SPKLU Riwayat Test')
                ->assertSee('Wuling Air EV')
                ->assertSee('D 1234 UAT')
                ->assertSee('20,00 kWh')
                ->assertSee('70.000')
                ->assertSee('SUCCESS');
        });
    }

    public function test_usage_summary_counts_only_success_transactions_for_energy_and_revenue(): void
    {
        $vendorUser = $this->createApprovedVendorUser();
        $rider = $this->createRider();

        $spklu = $this->createSpklu($vendorUser->evhub_vendor_id);
        $charger = $this->createChargerMachine($vendorUser->evhub_vendor_id, $spklu->id);
        $vehicle = $this->createVehicle($rider->id);

        $this->createTransaction(
            userId: $rider->id,
            chargerMachineId: $charger->id,
            vehicleId: $vehicle->id,
            status: 'success',
            energy: 20,
            totalPrice: 70000
        );

        $this->createTransaction(
            userId: $rider->id,
            chargerMachineId: $charger->id,
            vehicleId: $vehicle->id,
            status: 'pending',
            energy: 10,
            totalPrice: 35000
        );

        $this->createTransaction(
            userId: $rider->id,
            chargerMachineId: $charger->id,
            vehicleId: $vehicle->id,
            status: 'failed',
            energy: 5,
            totalPrice: 17500
        );

        $this->browse(function (Browser $browser) use ($vendorUser) {
            $browser->loginAs($vendorUser)
                ->visit(route('vendor.chargers.usageHistory'))
                ->assertSee('Riwayat Pemakaian Mesin')
                ->assertSee('20,00 kWh')
                ->assertSee('70.000')
                ->assertDontSee('35,00 kWh')
                ->assertDontSee('122.500');
        });
    }

    public function test_vendor_only_sees_usage_history_from_own_charger_machines(): void
    {
        $vendorA = $this->createApprovedVendorUser();
        $rider = $this->createRider();

        $spkluA = $this->createSpklu($vendorA->evhub_vendor_id);
        $chargerA = $this->createChargerMachine($vendorA->evhub_vendor_id, $spkluA->id, [
            'name' => 'Charger Milik Vendor A',
        ]);

        $vehicle = $this->createVehicle($rider->id);

        $this->createTransaction(
            userId: $rider->id,
            chargerMachineId: $chargerA->id,
            vehicleId: $vehicle->id,
            status: 'success',
            energy: 15,
            totalPrice: 52500
        );

        $vendorB = $this->createApprovedVendorUser([
            'email' => 'vendor.b.' . uniqid() . '@evhub.test',
        ]);

        $spkluB = $this->createSpklu($vendorB->evhub_vendor_id, [
            'name' => 'SPKLU Vendor B',
        ]);

        $chargerB = $this->createChargerMachine($vendorB->evhub_vendor_id, $spkluB->id, [
            'name' => 'Charger Milik Vendor B',
        ]);

        $this->createTransaction(
            userId: $rider->id,
            chargerMachineId: $chargerB->id,
            vehicleId: $vehicle->id,
            status: 'success',
            energy: 30,
            totalPrice: 105000
        );

        $this->browse(function (Browser $browser) use ($vendorA) {
            $browser->loginAs($vendorA)
                ->visit(route('vendor.chargers.usageHistory'))
                ->assertSee('Charger Milik Vendor A')
                ->assertDontSee('Charger Milik Vendor B');
        });
    }
}