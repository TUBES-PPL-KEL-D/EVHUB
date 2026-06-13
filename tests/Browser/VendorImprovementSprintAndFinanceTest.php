<?php

namespace Tests\Browser;

use App\Models\ChargerMachine;
use App\Models\Spklu;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Vendor;
use App\Models\VendorProfile;
use App\Models\VendorWithdrawal;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class VendorImprovementSprintAndFinanceTest extends DuskTestCase
{
    use DatabaseMigrations;

    private function createVendorUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'vendor',
            'status' => 'aktif',
        ], $attributes));
    }

    private function getValidJpgPath(): string
    {
        $path = storage_path('framework/testing/valid_gallery.jpg');

        if (! file_exists($path)) {
            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            $base64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';
            file_put_contents($path, base64_decode($base64));
        }

        return $path;
    }

    private function seedApprovedVendor(array $vendorAttributes = [], array $profileAttributes = []): array
    {
        $user = $this->createVendorUser();

        $vendor = Vendor::factory()->create(array_merge([
            'user_id' => $user->id,
            'status' => 'Approved',
            'company_name' => 'PT EVHUB Vendor Utama',
        ], $vendorAttributes));

        $profile = VendorProfile::create(array_merge([
            'user_id' => $user->id,
            'company_name' => $vendor->company_name,
            'company_email' => 'vendor@evhub.test',
            'company_phone' => '081234567890',
            'company_address' => 'Jl. Test Vendor No. 1',
            'company_description' => 'Vendor test untuk Dusk.',
            'latitude' => -6.9123456,
            'longitude' => 107.6123456,
            'opens_at' => '08:00',
            'closes_at' => '17:00',
            'npwp' => '123456789012345',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'PT EVHUB Vendor Utama',
        ], $profileAttributes));

        return compact('user', 'vendor', 'profile');
    }

    private function seedRevenueBundle(): array
    {
        $bundle = $this->seedApprovedVendor([
            'company_name' => 'PT Pendapatan Dusk',
        ], [
            'company_name' => 'PT Pendapatan Dusk',
        ]);

        $rider = User::factory()->create([
            'name' => 'Rider Dusk',
            'role' => 'rider',
        ]);

        $vehicle = Vehicle::create([
            'user_id' => $rider->id,
            'merk' => 'Hyundai',
            'model' => 'Ioniq 5',
            'license_plate' => 'B 1234 EV',
        ]);

        $spklu = Spklu::create([
            'vendor_id' => $bundle['vendor']->id,
            'name' => 'SPKLU Dusk Revenue',
            'address' => 'Jl. Pendapatan 1',
            'latitude' => -6.91,
            'longitude' => 107.61,
        ]);

        $charger = ChargerMachine::create([
            'vendor_id' => $bundle['vendor']->id,
            'spklu_id' => $spklu->id,
            'name' => 'Mesin Revenue A',
            'connector_type' => 'CCS2',
            'capacity_kw' => 150,
            'price_per_kwh' => 2500,
            'operational_hours' => '08:00 - 17:00',
            'photo_path' => 'chargers/dusk.jpg',
            'status' => 'available',
        ]);

        Transaction::create([
            'user_id' => $rider->id,
            'charger_machine_id' => $charger->id,
            'vehicle_id' => $vehicle->id,
            'energy_consumed' => 3,
            'total_price' => 7500,
            'started_at' => now()->subHours(2),
            'finished_at' => now()->subHour(),
            'status' => 'success',
        ]);

        Transaction::create([
            'user_id' => $rider->id,
            'charger_machine_id' => $charger->id,
            'vehicle_id' => $vehicle->id,
            'energy_consumed' => 3,
            'total_price' => 7500,
            'started_at' => now()->subHours(4),
            'finished_at' => now()->subHours(3),
            'status' => 'success',
        ]);

        Transaction::create([
            'user_id' => $rider->id,
            'charger_machine_id' => $charger->id,
            'vehicle_id' => $vehicle->id,
            'energy_consumed' => 0,
            'total_price' => 0,
            'started_at' => now()->subHours(6),
            'finished_at' => now()->subHours(5),
            'status' => 'failed',
        ]);

        Transaction::create([
            'user_id' => $rider->id,
            'charger_machine_id' => $charger->id,
            'vehicle_id' => $vehicle->id,
            'energy_consumed' => 0,
            'total_price' => 0,
            'started_at' => now()->subHours(8),
            'finished_at' => now()->subHours(7),
            'status' => 'pending',
        ]);

        return array_merge($bundle, compact('rider', 'vehicle', 'spklu', 'charger'));
    }

    // PBI 5
    public function test_pbi_5_vendor_can_save_geographic_profile(): void
    {
        $user = $this->createVendorUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/vendor/profile/create')
                ->waitForText('Form Profil Vendor', 10)
                ->type('company_name', 'PT Geografis EVHUB')
                ->type('company_email', 'geo@evhub.test')
                ->type('company_phone', '081234567890')
                ->type('company_address', 'Jl. Geografis No. 10, Bandung')
                ->type('company_description', 'Profil vendor untuk uji koordinat.')
                ->type('npwp', '123456789012345')
                ->type('bank_name', 'BCA')
                ->type('bank_account_number', '9876543210')
                ->type('bank_account_name', 'PT Geografis EVHUB');
            
            $browser->script([
                    "document.getElementById('latitude').value = '-6.914744';",
                    "document.getElementById('longitude').value = '107.609810';",
                ]);
                
            $browser->type('opens_at', '08:00')
                ->type('closes_at', '17:00')
                ->press('Simpan Profil')
                ->waitForText('Ringkasan profil perusahaan', 10)
                ->assertSee('PT Geografis EVHUB')
                ->assertSee('Jam Operasional')
                ->assertSee('08:00')
                ->assertSee('17:00')
                ->assertSee('Latitude:')
                ->assertSee('-6.914744')
                ->assertSee('Longitude:')
                ->assertSee('107.609810');
        });

        $this->assertDatabaseHas('vendor_profiles', [
            'user_id' => $user->id,
            'company_name' => 'PT Geografis EVHUB',
            'latitude' => -6.914744,
            'longitude' => 107.60981,
            'opens_at' => '08:00:00',
            'closes_at' => '17:00:00',
        ]);
    }

    // PBI 6
    public function test_pbi_6_vendor_cannot_save_invalid_coordinates(): void
    {
        $user = $this->createVendorUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/vendor/profile/create')
                ->waitForText('Form Profil Vendor', 10)
                ->type('company_name', 'PT Koordinat Salah')
                ->type('company_address', 'Jl. Salah Koordinat No. 1')
                ->type('npwp', '123456789012345')
                ->type('bank_name', 'Mandiri')
                ->type('bank_account_number', '1234567890')
                ->type('bank_account_name', 'PT Koordinat Salah');
            
            $browser->script([
                    "document.getElementById('latitude').value = '91';",
                    "document.getElementById('longitude').value = '181';",
                ]);
                
            $browser->press('Simpan Profil')
                ->waitForText('Ada kesalahan pada form:', 10)
                ->assertPathIs('/vendor/profile/create')
                ->assertSee('The latitude field must be between -90 and 90.')
                ->assertSee('The longitude field must be between -180 and 180.');
        });
    }

    // PBI 39
    public function test_pbi_39_vendor_can_update_operational_hours_from_profile_form(): void
    {
        $bundle = $this->seedApprovedVendor();

        $this->browse(function (Browser $browser) use ($bundle) {
            $browser->loginAs($bundle['user'])
                ->visit('/vendor/profile/create')
                ->waitForText('Perbaiki Profil Vendor', 10)
                ->type('opens_at', '07:30')
                ->type('closes_at', '21:30')
                ->press('Simpan Perbaikan Profil')
                ->waitForText('Ringkasan profil perusahaan', 10)
                ->assertSee('07:30')
                ->assertSee('21:30');
        });

        $this->assertDatabaseHas('vendor_profiles', [
            'user_id' => $bundle['user']->id,
            'opens_at' => '07:30:00',
            'closes_at' => '21:30:00',
        ]);
    }

    // PBI 40
    public function test_pbi_40_vendor_dashboard_shows_revenue_summary(): void
    {
        $bundle = $this->seedRevenueBundle();

        $this->browse(function (Browser $browser) use ($bundle) {
            $browser->loginAs($bundle['user'])
                ->visit('/vendor/dashboard')
                ->waitForText('Rekap Pendapatan', 10)
                ->waitForText('Rp15.000', 10)
                ->assertSee('Mesin Revenue A')
                ->assertSee('SPKLU')
                ->assertSee('2 transaksi')
                ->assertSee('PENDING');
        });
    }

    // PBI 41
    public function test_pbi_41_vendor_can_upload_and_delete_gallery_photo(): void
    {
        Storage::fake('public');

        $bundle = $this->seedApprovedVendor();

        $spklu = Spklu::create([
            'vendor_id' => $bundle['vendor']->id,
            'name' => 'SPKLU Galeri Dusk',
            'address' => 'Jl. Galeri No. 5',
            'latitude' => -6.91,
            'longitude' => 107.61,
        ]);

        ChargerMachine::create([
            'vendor_id' => $bundle['vendor']->id,
            'spklu_id' => $spklu->id,
            'name' => 'Mesin Galeri A',
            'connector_type' => 'CCS2',
            'capacity_kw' => 100,
            'price_per_kwh' => 2500,
            'operational_hours' => '08:00 - 17:00',
            'photo_path' => 'chargers/gallery.jpg',
            'status' => 'available',
        ]);

        $photoPath = $this->getValidJpgPath();

        $this->browse(function (Browser $browser) use ($bundle, $spklu, $photoPath) {
            $browser->loginAs($bundle['user'])
                ->visit('/vendor/spklu/' . $spklu->id . '/gallery')
                ->waitForText('Galeri Foto SPKLU', 10)
                ->attach('photo', $photoPath)
                ->press('.lg\:col-span-1 button[type="submit"]')
                ->waitForText('Foto berhasil ditambahkan', 10)
                ->mouseover('img[alt="Foto Stasiun"]')
                ->press('Hapus')
                ->acceptDialog()
                ->waitForText('Foto berhasil dihapus', 10)
                ->assertSee('BELUM ADA FOTO YANG DIUNGGAH.');
        });

        $this->assertDatabaseMissing('spklu_gallery_photos', [
            'spklu_id' => $spklu->id,
        ]);
    }

    // PBI 42
    public function test_pbi_42_vendor_can_submit_withdrawal_and_see_status(): void
    {
        $bundle = $this->seedRevenueBundle();

        $this->browse(function (Browser $browser) use ($bundle) {
            $browser->loginAs($bundle['user'])
                ->visit('/vendor/withdrawals')
                ->waitForText('Penarikan Dana Pendapatan', 10)
                ->assertSee('Rp15.000')
                ->type('amount', '10000')
                ->type('bank_name', 'BCA')
                ->type('bank_account_name', 'PT Pendapatan Dusk')
                ->type('bank_account_number', '1234567890')
                ->type('notes', 'Pengujian withdrawal Dusk')
                ->press('Kirim Pengajuan')
                ->waitForText('Pengajuan withdrawal berhasil dikirim.', 10)
                ->waitForText('Rp10.000', 10);
        });

        $this->assertDatabaseHas('vendor_withdrawals', [
            'vendor_id' => $bundle['vendor']->id,
            'amount' => 10000,
            'bank_name' => 'BCA',
            'status' => 'pending',
        ]);
    }

    public function test_pbi_42_vendor_cannot_withdraw_more_than_available_balance(): void
    {
        $bundle = $this->seedRevenueBundle();

        $this->browse(function (Browser $browser) use ($bundle) {
            $browser->loginAs($bundle['user'])
                ->visit('/vendor/withdrawals')
                ->waitForText('Penarikan Dana Pendapatan', 10);
                
            $browser->script("document.getElementsByName('amount')[0].removeAttribute('max');");
            
            $browser->type('amount', '30000')
                ->type('bank_name', 'BCA')
                ->type('bank_account_name', 'PT Pendapatan Dusk')
                ->type('bank_account_number', '1234567890')
                ->press('Kirim Pengajuan')
                ->waitForText('Nominal melebihi saldo yang tersedia untuk ditarik.', 10);
        });
    }
}
