<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProfile;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class VendorRegistrationTest extends DuskTestCase
{
    use DatabaseMigrations;

    // ─── HELPER ──────────────────────────────────────────────────────────────

    private function createVendorUser(): User
    {
        return User::factory()->create([
            'role'   => 'vendor',
            'status' => 'aktif',
        ]);
    }

    private function file(string $filename): string
    {
        return base_path("tests/Browser/files/{$filename}");
    }

    // =========================================================================
    // PBI #6 — Upload Dokumen Legalitas Vendor
    // =========================================================================

    // TC.Vendor.004 — Positive
    public function test_TC_Vendor_004_upload_dokumen_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $user    = $this->createVendorUser();
            VendorProfile::factory()->create(['user_id' => $user->id]);

            $browser->loginAs($user)
                    ->visit('/vendor/documents/create')
                    ->assertSee('Upload Dokumen Legalitas Vendor')
                    ->attach('legality_document', $this->file('valid_document.pdf'))
                    ->press('Upload Dokumen')
                    ->pause(3000)
                    ->assertPathIsNot('/vendor/documents/create');
        });
    }

    // TC.Vendor.005 — Negative
    public function test_TC_Vendor_005_upload_format_tidak_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $user    = $this->createVendorUser();
            VendorProfile::factory()->create(['user_id' => $user->id]);

            $browser->loginAs($user)
                    ->visit('/vendor/documents/create')
                    ->assertSee('Upload Dokumen Legalitas Vendor')
                    ->attach('legality_document', $this->file('invalid_file.exe'))
                    ->press('Upload Dokumen')
                    ->waitForText('kesalahan', 5)
                    ->assertSee('kesalahan');
        });
    }

    // TC.Vendor.006 — Negative
    public function test_TC_Vendor_006_upload_file_terlalu_besar(): void
    {
        $this->browse(function (Browser $browser) {
            $user    = $this->createVendorUser();
            VendorProfile::factory()->create(['user_id' => $user->id]);

            $browser->loginAs($user)
                    ->visit('/vendor/documents/create')
                    ->assertSee('Upload Dokumen Legalitas Vendor')
                    ->attach('legality_document', $this->file('large_document_6mb.pdf'))
                    ->press('Upload Dokumen')
                    ->waitForText('kesalahan', 5)
                    ->assertSee('kesalahan');
        });
    }

    // =========================================================================
    // PBI #7 — Status Pendaftaran Vendor
    // =========================================================================

    // TC.Vendor.007 — Positive
    public function test_TC_Vendor_007_status_pending(): void
    {
        $this->browse(function (Browser $browser) {
            $user   = $this->createVendorUser();
            Vendor::factory()->create([
                'user_id' => $user->id,
                'status'  => 'Pending',
            ]);

            $browser->loginAs($user)
                    ->visit('/vendor/status')
                    ->assertSee('Pending')
                    ->assertDontSee('Perbaiki & Upload Ulang');
        });
    }

    // TC.Vendor.008 — Positive
    public function test_TC_Vendor_008_status_approved(): void
    {
        $this->browse(function (Browser $browser) {
            $user   = $this->createVendorUser();
            Vendor::factory()->create([
                'user_id' => $user->id,
                'status'  => 'Approved',
            ]);

            $browser->loginAs($user)
                    ->visit('/vendor/status')
                    ->assertSee('Approved')
                    ->assertDontSee('Perbaiki & Upload Ulang');
        });
    }

    // TC.Vendor.009 — Negative
    public function test_TC_Vendor_009_status_rejected(): void
    {
        $this->browse(function (Browser $browser) {
            $user   = $this->createVendorUser();
            Vendor::factory()->create([
                'user_id' => $user->id,
                'status'  => 'Rejected',
            ]);

            $browser->loginAs($user)
                    ->visit('/vendor/status')
                    ->assertSee('Rejected')
                    ->assertSee('Perbaiki & Upload Ulang');
        });
    }

    // =========================================================================
    // PBI #8 — Upload Ulang Dokumen Ditolak
    // =========================================================================

    // TC.Vendor.010 — Positive
    public function test_TC_Vendor_010_upload_ulang_dokumen_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $user   = $this->createVendorUser();
            $vendor = Vendor::factory()->create([
                'user_id' => $user->id,
                'status'  => 'Rejected',
            ]);

            $browser->loginAs($user)
                    ->visit('/vendor/status')
                    ->assertSee('Rejected')
                    ->clickLink('Perbaiki & Upload Ulang')
                    ->waitForText('Perbaiki & Unggah Ulang Dokumen')
                    ->attach('legality_document', $this->file('valid_document.pdf'))
                    ->press('Simpan Perbaikan')
                    ->waitForText('Pending', 5)
                    ->assertSee('Pending');
        });
    }

    // TC.Vendor.011 — Negative
    public function test_TC_Vendor_011_upload_ulang_format_tidak_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $user   = $this->createVendorUser();
            $vendor = Vendor::factory()->create([
                'user_id' => $user->id,
                'status'  => 'Rejected',
            ]);

            $browser->loginAs($user)
                    ->visit("/vendor/documents/{$vendor->id}/edit")
                    ->assertSee('Perbaiki & Unggah Ulang Dokumen')
                    ->attach('legality_document', $this->file('invalid_file.exe'))
                    ->press('Simpan Perbaikan')
                    ->waitForText('kesalahan', 5)
                    ->assertSee('kesalahan')
                    ->assertPathContains('/edit');
        });
    }

    // TC.Vendor.012 — Negative
    public function test_TC_Vendor_012_approved_tidak_ada_tombol_upload_ulang(): void
    {
        $this->browse(function (Browser $browser) {
            $user   = $this->createVendorUser();
            Vendor::factory()->create([
                'user_id' => $user->id,
                'status'  => 'Approved',
            ]);

            $browser->loginAs($user)
                    ->visit('/vendor/status')
                    ->assertSee('Approved')
                    ->assertDontSee('Perbaiki & Upload Ulang');
        });
    }
}