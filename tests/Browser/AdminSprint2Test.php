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
        
        // Mengambil data admin dari database untuk proses login
        $this->admin = User::where('role', 'admin')->first();

        if (!$this->admin) {
            $this->artisan('db:seed');
            $this->admin = User::where('role', 'admin')->first();
        }
    }

    // =====================================================================
    // BAGIAN 1: POSITIVE PATH (HAPPY PATH)
    // Skenario pengujian alur ideal dimana semua input berhasil
    // =====================================================================

    /**
     * [TC.Adm.006] Happy Path 1: Admin Can Resolve Ticket
     * Skenario: Admin berhasil memberikan feedback dan menyelesaikan tiket aduan
     */
    public function test_admin_can_resolve_ticket()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    ->click('#tabBtn-manajemen')
                    ->waitForText('Laporan Kendala Aktif', 10)
                    ->script("document.querySelector('button[onclick*=\"openTicketModal\"]').click();");
            
            $browser->waitFor('#ticketModal')
                    ->pause(1000) // Tunggu animasi transisi modal selesai
                    ->type('feedback', 'Kendala mesin sudah kami reset dari pusat.')
                    ->click('#formResolveTicket button[type="submit"]')
                    ->waitForText('berhasil', 10);
        });
    }

    /**
     * [TC.Adm.007] Happy Path 2: Admin Can Approve And Suspend Vendor
     * Skenario: Admin berhasil menyetujui vendor baru, lalu membekukan (suspend) vendor tersebut
     */
    public function test_admin_can_approve_and_suspend_vendor()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    
                    // Fase Approve
                    ->click('#tabBtn-verifikasi')
                    ->waitForText('CV Maju Pengisian Cepat', 10)
                    ->script("document.querySelectorAll('button[onclick*=\"openReviewModal\"]')[0].click();");
            
            $browser->waitFor('#reviewModal')
                    ->pause(1000)
                    ->click('#formApprove button[type="submit"]')
                    ->waitForText('diaktifkan', 10)
                    
                    // Fase Suspend
                    ->click('#tabBtn-manajemen')
                    ->waitForText('CV Maju Pengisian Cepat', 10)
                    ->click('form[action*="suspend"] button[type="submit"]')
                    ->acceptDialog()
                    ->waitForText('STATUS PENANGGUHAN', 10);
        });
    }

    /**
     * [TC.Adm.008] Happy Path 3: Admin Can Export SPKLU Data
     * Skenario: Admin berhasil mengunduh laporan Excel rekap data stasiun
     */
    public function test_admin_can_export_spklu_data()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    ->click('#tabBtn-manajemen')
                    ->waitForText('DOWNLOAD BERKAS EXCEL', 10)
                    ->pause(1000); 
            
            $browser->script("
                let elements = document.querySelectorAll('a, button');
                for (let el of elements) {
                    if (el.innerText.toUpperCase().includes('EXCEL')) {
                        el.click();
                        break;
                    }
                }
            ");

            $browser->pause(2000)
                    ->assertPathIs('/admin/dashboard'); 
        });
    }

    /**
     * [TC.Adm.009] Happy Path 4: Admin Can Switch Tabs And View Chart
     * Skenario: Admin berhasil melihat grafik pertumbuhan dan berpindah antar tab menu
     */
    public function test_admin_can_switch_tabs_and_view_chart()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    ->assertPresent('#spkluGrowthChart')
                    ->click('#tabBtn-verifikasi')
                    ->waitFor('#panel-verifikasi')
                    ->assertVisible('#panel-verifikasi')
                    ->click('#tabBtn-manajemen')
                    ->waitFor('#panel-manajemen')
                    ->assertVisible('#panel-manajemen');
        });
    }

    /**
     * [TC.Adm.010] Happy Path 5: Admin Can Process Withdrawal
     * Skenario: Admin berhasil memproses dan menyetujui penarikan dana vendor
     */
    public function test_admin_can_process_withdrawal()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/withdrawals')
                    ->waitForText('Rp1.500.000', 10)
                    ->click('form[action*="approve"] button[type="submit"]')
                    ->waitForText('berhasil', 10);
        });
    }

    // =====================================================================
    // BAGIAN 2: NEGATIVE PATH (SAD PATH)
    // Skenario pengujian alur gagal dimana input tidak valid atau terjadi error
    // =====================================================================

    /**
     * [TC.Adm.011] Negative Path 1: Admin Cannot Resolve Ticket With Empty Feedback
     * Skenario: Sistem menolak proses penyelesaian tiket jika admin mengosongkan form
     */
    public function test_admin_cannot_resolve_ticket_with_empty_feedback()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    ->click('#tabBtn-manajemen')
                    ->waitForText('Laporan Kendala Aktif', 10)
                    ->script("document.querySelector('button[onclick*=\"openTicketModal\"]').click();");

            $browser->waitFor('#ticketModal')
                    ->pause(1000)
                    // Bypass required HTML untuk menguji pertahanan backend
                    ->script("document.querySelector('textarea[name=\"feedback\"]').removeAttribute('required');");

            $browser->clear('feedback')
                    ->click('#formResolveTicket button[type="submit"]')
                    ->pause(1000)
                    ->assertDontSee('berhasil diselesaikan')
                    ->assertPathIs('/admin/dashboard');
        });
    }

    /**
     * [TC.Adm.012] Negative Path 2: Admin Cannot Send Empty Warning
     * Skenario: Sistem menolak proses pengiriman surat peringatan jika pesannya kosong
     */
    public function test_admin_cannot_send_empty_warning()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    ->click('#tabBtn-manajemen')
                    ->waitForText('Daftar Vendor Aktif', 10)
                    ->script("document.querySelector('form[action*=\"warning\"] input[name=\"message\"]').removeAttribute('required');");

            $browser->clear('message')
                    ->click('form[action*="warning"] button[type="submit"]')
                    ->pause(1000)
                    ->assertDontSee('berhasil dikirim')
                    ->assertPathIs('/admin/dashboard');
        });
    }

    /**
     * [TC.Adm.013] Negative Path 3: Admin Cancels Vendor Suspension
     * Skenario: Sistem membatalkan aksi penangguhan saat admin klik "Cancel" di konfirmasi browser
     */
    public function test_admin_cancels_vendor_suspension()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/admin/dashboard')
                    ->click('#tabBtn-manajemen')
                    ->waitForText('Daftar Vendor Aktif', 10)
                    ->click('form[action*="suspend"] button[type="submit"]')
                    // Memilih opsi "Cancel" pada dialog konfirmasi
                    ->dismissDialog() 
                    ->pause(1000)
                    ->assertDontSee('telah dibekukan sementara')
                    ->assertPathIs('/admin/dashboard');
        });
    }

    /**
     * [TC.Adm.014] Negative Path 4: Non-Admin Cannot Access Admin Dashboard
     * Skenario: Sistem keamanan menolak pengguna biasa (Rider) yang mencoba masuk ke link Admin
     */
    public function test_non_admin_cannot_access_admin_dashboard()
    {
        $rider = User::where('role', 'rider')->first();

        if (!$rider) {
            $this->artisan('db:seed');
            $rider = User::where('role', 'rider')->first();
        }

        $this->browse(function (Browser $browser) use ($rider) {
            $browser->loginAs($rider)
                    ->visit('/admin/dashboard')
                    ->pause(1000)
                    ->assertPathIsNot('/admin/dashboard');
        });
    }

    /**
     * [TC.Adm.015] Negative Path 5: Unauthenticated User Redirected To Login
     * Skenario: Sistem keamanan menendang pengunjung tanpa akun kembali ke halaman login
     */
    public function test_unauthenticated_user_redirected_to_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout() 
                    ->visit('/admin/dashboard')
                    ->pause(1000)
                    ->assertPathIs('/login'); 
        });
    }
}