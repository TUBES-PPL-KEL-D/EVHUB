<?php

namespace Tests\Browser;

use App\Models\ChargingQueue;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesEvhTestData;
use Tests\DuskTestCase;

class EVHUBPBITest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreatesEvhTestData;

    /*
    |--------------------------------------------------------------------------
    | PBI 43
    | Sebagai pengendara, saya ingin melihat estimasi durasi pengisian baterai
    | berdasarkan daya mesin.
    |--------------------------------------------------------------------------
    */

    public function test_pbi43_rider_can_see_estimated_charging_duration_based_on_machine_power(): void
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

    public function test_pbi43_estimated_duration_changes_when_energy_target_is_changed(): void
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

    /*
    |--------------------------------------------------------------------------
    | PBI 44
    | Sebagai pengendara, saya ingin masuk antrean digital jika mesin stasiun
    | sedang berstatus digunakan.
    |--------------------------------------------------------------------------
    */

    public function test_pbi44_rider_can_join_digital_queue_when_machine_is_unavailable(): void
    {
        $vendorUser = $this->createApprovedVendorUser();
        $rider = $this->createRider();

        $spklu = $this->createSpklu($vendorUser->evhub_vendor_id);

        $machine = $this->createChargerMachine($vendorUser->evhub_vendor_id, $spklu->id, [
            'name' => 'Mesin Sedang Digunakan',
            'status' => 'unavailable',
        ]);

        $this->browse(function (Browser $browser) use ($rider, $spklu) {
            $browser->loginAs($rider)
                ->visit(route('rider.spklu.show', $spklu->id))
                ->assertSee('Masuk Antrean Digital')
                ->click('form[action="' . route('rider.queues.store') . '"] button[type="submit"]')
                ->waitForText('Berhasil masuk antrean digital', 5)
                ->assertSee('Nomor antrean Anda');
        });

        $this->assertDatabaseHas('charging_queues', [
            'user_id' => $rider->id,
            'charger_machine_id' => $machine->id,
            'status' => 'waiting',
        ]);
    }

    public function test_pbi44_rider_cannot_create_duplicate_queue_on_same_machine(): void
    {
        $vendorUser = $this->createApprovedVendorUser();
        $rider = $this->createRider();

        $spklu = $this->createSpklu($vendorUser->evhub_vendor_id);

        $machine = $this->createChargerMachine($vendorUser->evhub_vendor_id, $spklu->id, [
            'status' => 'unavailable',
        ]);

        ChargingQueue::create([
            'user_id' => $rider->id,
            'charger_machine_id' => $machine->id,
            'status' => 'waiting',
            'queued_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($rider, $spklu) {
            $browser->loginAs($rider)
                ->visit(route('rider.spklu.show', $spklu->id))
                ->assertSee('Masuk Antrean Digital')
                ->click('form[action="' . route('rider.queues.store') . '"] button[type="submit"]')
                ->waitForText('Anda sudah berada dalam antrean', 5)
                ->assertSee('Nomor antrean Anda');
        });

        $this->assertSame(
            1,
            ChargingQueue::where('user_id', $rider->id)
                ->where('charger_machine_id', $machine->id)
                ->where('status', 'waiting')
                ->count()
        );
    }

    public function test_pbi44_maintenance_machine_does_not_show_queue_button(): void
    {
        $vendorUser = $this->createApprovedVendorUser();
        $rider = $this->createRider();

        $spklu = $this->createSpklu($vendorUser->evhub_vendor_id);

        $this->createChargerMachine($vendorUser->evhub_vendor_id, $spklu->id, [
            'status' => 'maintenance',
        ]);

        $this->browse(function (Browser $browser) use ($rider, $spklu) {
            $browser->loginAs($rider)
                ->visit(route('rider.spklu.show', $spklu->id))
                ->assertSee('Sedang Maintenance')
                ->assertDontSee('Masuk Antrean Digital');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PBI 45
    | Sebagai Vendor, saya ingin menetapkan tarif harga per kWh untuk
    | masing-masing mesin charger.
    |--------------------------------------------------------------------------
    */

    public function test_pbi45_vendor_can_update_price_per_kwh_for_charger_machine(): void
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

    public function test_pbi45_vendor_cannot_update_tariff_with_negative_value(): void
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

    /*
    |--------------------------------------------------------------------------
    | PBI 46
    | Sebagai Vendor, saya ingin melihat riwayat pemakaian mesin untuk
    | memantau logistik stasiun.
    |--------------------------------------------------------------------------
    */

    public function test_pbi46_vendor_can_view_machine_usage_history(): void
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

    public function test_pbi46_usage_summary_counts_only_success_transactions_for_energy_and_revenue(): void
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

    public function test_pbi46_vendor_only_sees_usage_history_from_own_charger_machines(): void
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