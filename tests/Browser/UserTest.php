<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * =========================================================================
     * PBI #1: REGISTRASI PENGGUNA
     * =========================================================================
     */

    #[Test]
    public function test_tc_user_001_registrasi_pengguna_dengan_input_yang_valid()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'Budi Santoso')
                    ->type('phone', '081234567890')
                    ->type('email', 'budi@evhub.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->press('Registrasi')
                    ->assertPathIs('/rider/vehicles')
                    ->assertSee('Akun berhasil dibuat dan Anda telah login.');
        });
    }

    #[Test]
    public function test_tc_user_002_registrasi_pengguna_dengan_salah_satu_input_kosong()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', '') // Nama dikosongkan
                    ->type('email', 'test@evhub.com')
                    ->press('Registrasi')
                    ->assertPathIs('/register')
                    ->assertPresent('input[name="name"]:invalid');
        });
    }

    #[Test]
    public function test_tc_user_003_registrasi_pengguna_dengan_data_input_tidak_valid()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'Tester')
                    ->type('email', 'bukan-email-valid')
                    ->press('Registrasi')
                    ->assertPresent('input[type="email"]:invalid');
        });
    }

    /**
     * =========================================================================
     * PBI #2: LOGIN PENGGUNA
     * =========================================================================
     */

    #[Test]
    public function test_tc_user_004_login_pengguna_dengan_input_yang_valid()
    {
        $user = User::factory()->create([
            'email' => 'login@evhub.com',
            'password' => bcrypt('password123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password123')
                    ->press('Login')
                    ->assertPathIs('/rider/vehicles')
                    ->assertSee(substr($user->name, 0, 1));
        });
    }

    #[Test]
    public function test_tc_user_005_login_pengguna_dengan_salah_satu_input_kosong()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', '')
                    ->press('Login')
                    ->assertPresent('input[type="email"]:invalid'); // Muncul peringatan input kosong
        });
    }

    #[Test]
    public function test_tc_user_006_login_pengguna_dengan_data_input_tidak_valid()
    {
        $user = User::factory()->create(['password' => bcrypt('benar123')]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'salah123')
                    ->press('Login')
                    ->assertPathIs('/login');
        });
    }

    /**
     * =========================================================================
     * PBI #3: UPDATE PROFIL
     * =========================================================================
     */

    #[Test]
    public function test_tc_user_007_update_profil_dengan_input_yang_valid()
    {
        $user = User::factory()->create(['name' => 'Nama Lama']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->type('name', 'Nama Baru Terupdate')
                    ->type('email', 'baru@evhub.com')
                    ->type('phone', '08999999999') 
                    ->press('Simpan Perubahan')
                    ->assertSee('Profil berhasil diperbarui.');
        });
    }

    #[Test]
    public function test_tc_user_008_update_profil_dengan_input_yang_tidak_valid()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->type('name', '')
                    ->press('Simpan Perubahan')
                    ->assertPresent('input[name="name"]:invalid');
        });
    }

    /**
     * =========================================================================
     * PBI #4: DEAKTIVASI AKUN
     * =========================================================================
     */

    #[Test]
    public function test_tc_user_009_soft_delete_atau_nonaktifkan_akun_pengguna()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->press('Nonaktifkan')
                    ->acceptDialog()
                    ->assertPathIs('/login')
                    ->assertGuest();
        });
    }
}