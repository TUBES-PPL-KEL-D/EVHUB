<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class VendorSetHoursTest extends DuskTestCase
{
    use DatabaseMigrations;

    private function createVendorUser(): User
    {
        return User::factory()->create([
            'role'   => 'vendor',
            'status' => 'aktif',
        ]);
    }

    public function test_vendor_can_set_operational_hours(): void
    {
        $this->browse(function (Browser $browser) {
            $user = $this->createVendorUser();
            VendorProfile::factory()->create(['user_id' => $user->id]);

            $browser->loginAs($user)
                    ->visit('/vendor/profile/create')
                    ->assertSee('Form Profil Vendor')
                    ->type('company_name', 'PT Test Vendor')
                    ->type('company_address', 'Jl. Contoh 1')
                    ->type('company_phone', '081234567890')
                    ->type('opens_at', '08:00')
                    ->type('closes_at', '17:00')
                    ->press('Simpan Profil')
                    ->waitForText('Profil Vendor', 5)
                    ->assertSee('08:00')
                    ->assertSee('17:00');
        });
    }
}
