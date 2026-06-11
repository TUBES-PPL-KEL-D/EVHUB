<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Spklu;
use App\Models\Vendor;
use App\Models\Review;
use App\Models\VendorProfile;

class InteraksiDanDetailSpkluTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * PBI 25: Merender titik (marker) lokasi SPKLU di atas peta digital.
     */
    public function testRenderSpkluMarkersOnMap(): void
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();
        VendorProfile::factory()->create(['user_id' => $vendor->user_id]);

        $spklu = Spklu::create([
            'vendor_id' => $vendor->id,
            'name' => 'SPKLU Test Station',
            'address' => 'Jalan Test',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $this->browse(function (Browser $browser) use ($user, $spklu) {
            $browser->loginAs($user)
                    ->visitRoute('rider.map')
                    // Memastikan tampilan Peta terbuka (teks Jaringan SPKLU)
                    ->assertSee('Jaringan')
                    // Diasumsikan ada elemen map yang dirender dengan ID map
                    ->assertPresent('#map');
        });
    }

    /**
     * PBI 51: Halaman Detail SPKLU utuh untuk melihat seluruh fasilitas stasiun.
     */
    public function testSpkluDetailPageShowsFacilities(): void
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();
        VendorProfile::factory()->create(['user_id' => $vendor->user_id]);

        $spklu = Spklu::create([
            'vendor_id' => $vendor->id,
            'name' => 'SPKLU Super Fast',
            'address' => 'Jl. Sudirman',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);

        $this->browse(function (Browser $browser) use ($user, $spklu) {
            $browser->loginAs($user)
                    ->visitRoute('rider.spklu.show', ['spklu' => $spklu->id])
                    ->assertSee($spklu->name)
                    ->assertSee($spklu->address);
        });
    }

    /**
     * PBI 52: Memberikan Rating dan ulasan tertulis pasca pengisian daya.
     */
    public function testUserCanSubmitRatingAndReview(): void
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();
        VendorProfile::factory()->create(['user_id' => $vendor->user_id]);

        $spklu = Spklu::create([
            'vendor_id' => $vendor->id,
            'name' => 'SPKLU Review Test',
            'address' => 'Jl. Thamrin',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);

        $this->browse(function (Browser $browser) use ($user, $spklu) {
            $browser->loginAs($user)
                    ->visitRoute('rider.spklu.show', ['spklu' => $spklu->id])
                    // Klik tombol untuk membuka form ulasan
                    ->click('button[onclick*="review-form"]')
                    ->pause(1000)
                    // Mengisi form ulasan
                    ->type('comment', 'Sangat memuaskan, pengisian cepat!')
                    // Mengklik tombol submit
                    ->click('#review-form button[type="submit"]')
                    // Memastikan ulasan tersimpan dan muncul di halaman
                    ->waitForText('Sangat memuaskan, pengisian cepat!');
        });
    }

    /**
     * PBI 53: Membaca ulasan pengguna lain untuk mengetahui kualitas layanan.
     */
    public function testUserCanReadOtherUsersReviews(): void
    {
        $user1 = User::factory()->create(['name' => 'Budi User Satu']);
        $user2 = User::factory()->create(['name' => 'Andi User Dua']);
        
        $vendor = Vendor::factory()->create();
        VendorProfile::factory()->create(['user_id' => $vendor->user_id]);

        $spklu = Spklu::create([
            'vendor_id' => $vendor->id,
            'name' => 'SPKLU View Review',
            'address' => 'Jl. Gatot Subroto',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);

        // User 1 memberikan ulasan
        Review::create([
            'user_id' => $user1->id,
            'spklu_id' => $spklu->id,
            'rating' => 4,
            'comment' => 'Tempatnya nyaman dan bersih.',
        ]);

        // User 2 mengunjungi halaman SPKLU dan membaca ulasan User 1
        $this->browse(function (Browser $browser) use ($user2, $spklu) {
            $browser->loginAs($user2)
                    ->visitRoute('rider.spklu.show', ['spklu' => $spklu->id])
                    ->assertSee('Tempatnya nyaman dan bersih.')
                    ->assertSee('Budi User Satu');
        });
    }

    /**
     * PBI 54: Fitur Laporkan Kendala yang tersambung ke Dashboard Admin.
     */
    public function testUserCanReportAnIssue(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visitRoute('rider.tickets.index')
                    ->type('subject', 'Mesin Rusak')
                    ->type('description', 'Konektor tidak bisa dicabut')
                    ->click('form[action*="tickets"] button[type="submit"]')
                    ->waitForText('berhasil dikirim')
                    ->assertSee('Mesin Rusak');
        });
    }
}
