<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class AdminSprint2Test extends DuskTestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Langsung ambil admin dari database yang sudah di-seed
        $this->admin = User::where('role', 'admin')->first();
    }

    /**
     * Test: Admin bisa ganti-ganti Tab & lihat grafik
     */
    public function test_admin_can_switch_tabs_and_view_chart()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    // Pastikan grafik muncul
                    ->assertPresent('#spkluGrowthChart')
                    
                    // Klik tab Antrean Verifikasi
                    ->click('#tabBtn-verifikasi')
                    ->waitFor('#panel-verifikasi')
                    ->assertVisible('#panel-verifikasi')
                    
                    // Klik tab Manajemen
                    ->click('#tabBtn-manajemen')
                    ->waitFor('#panel-manajemen')
                    ->assertVisible('#panel-manajemen');
        });
    }

    /**
     * Test: Admin membalas laporan/tiket (PBI 34/35)
     */
    public function test_admin_can_resolve_ticket()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    // Harus klik tab manajemen dulu agar tiket terlihat
                    ->click('#tabBtn-manajemen')
                    ->waitForText('Laporan Kendala Aktif', 10)
                    
                    // Klik tombol TINJAU
                    ->script("document.querySelector('button[onclick*=\"openTicketModal\"]').click();");
            
            $browser->waitFor('#ticketModal')
                    ->pause(1000) // PENTING: Tunggu animasi transisi modal selesai (300ms+)
                    ->type('feedback', 'Kendala mesin sudah kami reset dari pusat.')
                    // Eksekusi klik langsung ke elemen tombol submit di dalam form tiket
                    ->click('#formResolveTicket button[type="submit"]')
                    // Cek pop-up success session
                    ->waitForText('berhasil', 10);
        });
    }

    /**
     * Test: Admin merespons pencairan dana (PBI 38)
     */
    public function test_admin_can_process_withdrawal()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/withdrawals')
                    // Cek data dari seeder
                    ->waitForText('Rp1.500.000', 10)
                    // PENTING: Jangan pakai press('Setujui') karena bentrok dengan class "uppercase".
                    // Langsung klik tombol submit di dalam form approve.
                    ->click('form[action*="approve"] button[type="submit"]')
                    ->waitForText('berhasil', 10);
        });
    }
}