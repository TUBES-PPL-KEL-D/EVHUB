<?php

namespace App\Http\Controllers;

use App\Models\Spklu;
use App\Models\SpkluGalleryPhoto;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SpkluGalleryController extends Controller
{
    private function checkVendorStatus()
    {
        return Vendor::find(1);
    }

    private function ensureOwnSpklu(Spklu $spklu): void
    {
        $vendor = $this->checkVendorStatus();

        if (! $vendor) {
            abort(403);
        }

        if ($spklu->vendor_id !== $vendor->id) {
            abort(403);
        }
    }

    public function index(Spklu $spklu)
    {
        $this->ensureOwnSpklu($spklu);

        if (! Schema::hasTable('spklu_gallery_photos')) {
            return view('vendor.spklu-gallery.index', [
                'spklu' => $spklu,
                'photos' => collect(),
            ])->with('error', 'Tabel galeri foto belum tersedia. Jalankan migrasi database terlebih dahulu.');
        }

        $photos = $spklu->galleryPhotos()->get();

        return view('vendor.spklu-gallery.index', compact('spklu', 'photos'));
    }

    public function store(Request $request, Spklu $spklu)
    {
        $this->ensureOwnSpklu($spklu);

        if (! Schema::hasTable('spklu_gallery_photos')) {
            return redirect()
                ->route('vendor.spklu.gallery.index', $spklu)
                ->with('error', 'Tabel galeri foto belum tersedia. Jalankan migrasi database terlebih dahulu.');
        }

        $validated = $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'captions' => ['nullable', 'array'],
            'captions.*' => ['nullable', 'string', 'max:255'],
        ]);

        $currentMaxOrder = (int) SpkluGalleryPhoto::where('spklu_id', $spklu->id)->max('sort_order');

        foreach ($request->file('photos', []) as $index => $photo) {
            $path = $photo->store('spklu/gallery', 'public');

            SpkluGalleryPhoto::create([
                'spklu_id' => $spklu->id,
                'image_path' => $path,
                'caption' => $validated['captions'][$index] ?? null,
                'sort_order' => $currentMaxOrder + $index + 1,
            ]);
        }

        return redirect()
            ->route('vendor.spklu.gallery.index', $spklu)
            ->with('success', 'Foto galeri SPKLU berhasil diunggah.');
    }

    public function destroy(Spklu $spklu, SpkluGalleryPhoto $photo)
    {
        $this->ensureOwnSpklu($spklu);

        if ($photo->spklu_id !== $spklu->id) {
            abort(404);
        }

        if ($photo->image_path && Storage::disk('public')->exists($photo->image_path)) {
            Storage::disk('public')->delete($photo->image_path);
        }

        $photo->delete();

        return redirect()
            ->route('vendor.spklu.gallery.index', $spklu)
            ->with('success', 'Foto galeri berhasil dihapus.');
    }
}