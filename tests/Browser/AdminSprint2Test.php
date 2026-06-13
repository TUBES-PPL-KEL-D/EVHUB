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
     * [Improvement Sprint 1] PBI 9: Admin membalas laporan/tiket kendala
     */
    public function test_admin_can_resolve_ticket()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    // Harus klik tab manajemen dulu agar tiket terlihat
                    ->click('#tabBtn-manajemen')
                    ->waitForText('Laporan Kendala Aktif', 10)
                    
                    // Klik tombol TINJAU (membuka modal tiket)
                    ->script("document.querySelector('button[onclick*=\"openTicketModal\"]').click();");
            
            $browser->waitFor('#ticketModal')
                    ->pause(1000) // Tunggu animasi transisi modal selesai (300ms+)
                    ->type('feedback', 'Kendala mesin sudah kami reset dari pusat.')
                    // Eksekusi klik langsung ke elemen tombol submit di dalam form tiket
                    ->click('#formResolveTicket button[type="submit"]')
                    // Cek pop-up success session
                    ->waitForText('berhasil', 10);
        });
    }

    /**
     * PBI 35: Admin menyetujui vendor (Approve) lalu memberikan Suspend langsung
     */
    public function test_admin_can_approve_and_suspend_vendor()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    
                    // --- FASE 1: APPROVE VENDOR ---
                    ->click('#tabBtn-verifikasi')
                    ->waitForText('CV Maju Pengisian Cepat', 10)
                    
                    // Klik tombol TINJAU pada antrean vendor
                    ->script("document.querySelectorAll('button[onclick*=\"openReviewModal\"]')[0].click();");
            
            $browser->waitFor('#reviewModal')
                    ->pause(1000)
                    ->click('#formApprove button[type="submit"]')
                    ->waitForText('diaktifkan', 10)
                    
                    // --- FASE 2: SUSPEND VENDOR ---
                    ->click('#tabBtn-manajemen')
                    ->waitForText('CV Maju Pengisian Cepat', 10)
                    
                    // Klik tombol SUSPEND
                    ->click('form[action*="suspend"] button[type="submit"]')
                    
                    // PENTING: Menyuruh robot Dusk menekan "OK" di pop-up konfirmasi
                    ->acceptDialog()
                    
                    // Memastikan vendor masuk ke daftar akun dibekukan sesuai gambar ke-3
                    ->waitForText('STATUS PENANGGUHAN', 10);
        });
    }


    /**
     * PBI 36: Admin mengekspor rekap data SPKLU ke Excel
     */
    public function test_admin_can_export_spklu_data()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    
                    // 1. Pindah ke tab Manajemen Vendor & Stasiun terlebih dahulu
                    ->click('#tabBtn-manajemen')
                    
                    // 2. Tunggu sampai tombolnya benar-benar terlihat di layar
                    ->waitForText('DOWNLOAD BERKAS EXCEL', 10)
                    ->pause(1000); // Jeda transisi tab
            
            // 3. Eksekusi klik
            $browser->script("
                let elements = document.querySelectorAll('a, button');
                for (let el of elements) {
                    if (el.innerText.toUpperCase().includes('EXCEL')) {
                        el.click();
                        break;
                    }
                }
            ");

            // 4. Jeda 2 detik agar file terunduh, lalu pastikan tidak ada error
            $browser->pause(2000)
                    ->assertPathIs('/admin/dashboard'); 
        });
    }

    /**
     * PBI 37: Admin melihat grafik analitik pertumbuhan SPKLU
     */
    public function test_admin_can_switch_tabs_and_view_chart()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    // Pastikan grafik muncul
                    ->assertPresent('#spkluGrowthChart')
                    
                    // Validasi fungsi perpindahan tab
                    ->click('#tabBtn-verifikasi')
                    ->waitFor('#panel-verifikasi')
                    ->assertVisible('#panel-verifikasi')
                    
                    ->click('#tabBtn-manajemen')
                    ->waitFor('#panel-manajemen')
                    ->assertVisible('#panel-manajemen');
        });
    }

    /**
     * PBI 38: Admin merespons pengajuan Withdrawal vendor
     */
    public function test_admin_can_process_withdrawal()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/withdrawals')
                    // Cek data dari seeder
                    ->waitForText('Rp1.500.000', 10)
                    // Klik tombol submit di dalam form approve
                    ->click('form[action*="approve"] button[type="submit"]')
                    ->waitForText('berhasil', 10);
        });
    }
}