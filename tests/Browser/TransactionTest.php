<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Spklu;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TransactionTest extends DuskTestCase
{

    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']); 
    }

    public function test_pbi31_topup_ewallet()
    {   

        $user = User::factory()->create(['balance' => 10000, 'role' => 'rider']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->waitForLink('+ Isi Saldo') 
                    ->clickLink('+ Isi Saldo')
                    ->assertPathIs('/rider/wallet')
                    ->type('amount', '50000')
                    ->press('Konfirmasi Top-Up')
                    ->waitForText('Simulasi Top-Up berhasil! Saldo EV-Pay Anda telah bertambah.') // Tunggu teks muncul
                    ->assertSee('Rp60.000');
        });
    }

    public function test_pbi32_pemotongan_saldo_otomatis_setelah_transaksi()
    {   

        $user = User::factory()->create();

        Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Hyundai',
            'model' => 'Ioniq 5',
            'license_plate' => 'B 1223 UZ',
            'connector_type' => 'CCS',
        ]);

        User::find($user->id)->update(['balance' => 100000]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/peta')
                    ->waitFor('.leaflet-marker-icon', 15)
                    ->click('.leaflet-marker-icon') 
                    ->waitFor('a[href="/rider/spklu/1"]', 15) 
                    ->click('a[href="/rider/spklu/1"]')
                    ->waitForText('Mulai Mengisi', 15)
                    ->clickLink('Mulai Mengisi')
                    ->assertSee('Konfigurasi Pengisian')
                    ->type('energy_target', '15')
                    ->select('vehicle_id', '5')
                    ->press('Konfirmasi & Mulai Mengisi')
                    ->pause(2000)
                    ->assertSee('Riwayat Pengisian Daya')
                    ->press('Selesai');
        });
    }

    public function test_pbi33_detail_riwayat_transaksi()
    {   

        $user = User::factory()->create();

        Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Hyundai',
            'model' => 'Ioniq 5',
            'license_plate' => 'B 1223 UZ',
            'connector_type' => 'CCS',
        ]);

        User::find($user->id)->update(['balance' => 100000]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/peta')
                    ->waitFor('.leaflet-marker-icon', 15)
                    ->click('.leaflet-marker-icon') 
                    ->waitFor('a[href="/rider/spklu/1"]', 15) 
                    ->click('a[href="/rider/spklu/1"]')
                    ->waitForText('Mulai Mengisi', 15)
                    ->clickLink('Mulai Mengisi')
                    ->assertSee('Konfigurasi Pengisian')
                    ->type('energy_target', '15')
                    ->select('vehicle_id', '5')
                    ->press('Konfirmasi & Mulai Mengisi')
                    ->pause(2000)
                    ->assertSee('Riwayat Pengisian Daya')
                    ->press('Selesai')
                    ->pause(2000)
                    ->assertSee('Rincian Transaksi EVHUB');
        });
    }

    public function test_pbi34_unduh_pdf_bukti_pembayaran()
    {   

        $user = User::factory()->create();

        Vehicle::create([
            'user_id' => $user->id,
            'merk' => 'Hyundai',
            'model' => 'Ioniq 5',
            'license_plate' => 'B 1223 UZ',
            'connector_type' => 'CCS',
        ]);

        User::find($user->id)->update(['balance' => 100000]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/rider/peta')
                    ->waitFor('.leaflet-marker-icon', 15)
                    ->click('.leaflet-marker-icon') 
                    ->waitFor('a[href="/rider/spklu/1"]', 15) 
                    ->click('a[href="/rider/spklu/1"]')
                    ->pause(2000)
                    ->waitForText('Mulai Mengisi', 15)
                    ->clickLink('Mulai Mengisi')
                    ->assertSee('Konfigurasi Pengisian')
                    ->type('energy_target', '15')
                    ->select('vehicle_id', '5')
                    ->press('Konfirmasi & Mulai Mengisi')
                    ->pause(2000)
                    ->assertSee('Riwayat Pengisian Daya')
                    ->press('Selesai')
                    ->pause(2000)
                    ->assertSee('Rincian Transaksi EVHUB')
                    ->press('Unduh Bukti Struk');
        });
    }
}
