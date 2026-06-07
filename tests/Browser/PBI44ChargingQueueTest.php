<?php

namespace Tests\Browser;

use App\Models\ChargingQueue;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesEvhTestData;
use Tests\DuskTestCase;

class PBI44ChargingQueueTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreatesEvhTestData;

    public function test_rider_can_join_digital_queue_when_machine_is_unavailable(): void
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

    public function test_rider_cannot_create_duplicate_queue_on_same_machine(): void
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

    public function test_maintenance_machine_does_not_show_queue_button(): void
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
}