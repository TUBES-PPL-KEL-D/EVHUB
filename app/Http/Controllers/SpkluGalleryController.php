<?php

namespace App\Http\Controllers;

use App\Models\Spklu;
use App\Models\SpkluGalleryPhoto;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SpkluGalleryController extends Controller
{
    private function getVendor()
    {
        return Vendor::where('user_id', Auth::id())->first();
    }

    public function index(Spklu $spklu)
    {
        $vendor = $this->getVendor();
        
        if (!$vendor) {
            return redirect()->route('vendor.status')->with('error', 'Akses ditolak.');
        }

        if ($spklu->vendor_id !== $vendor->id) {
            abort(403, 'Akses ditolak. Anda tidak berhak memodifikasi galeri SPKLU ini.');
        }

        $photos = $spklu->galleryPhotos()->latest()->get();
        return view('vendor.spklu-gallery.index', compact('spklu', 'photos'));
    }

    public function store(Request $request, Spklu $spklu)
    {
        $vendor = $this->getVendor();
        if ($spklu->vendor_id !== $vendor->id) abort(403);

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'photo.required' => 'File foto wajib dipilih.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran maksimal file adalah 5 MB.',
        ]);

        $path = $request->file('photo')->store('spklu_galleries', 'public');

        // PERBAIKAN: Menggunakan kolom image_path
        SpkluGalleryPhoto::create([
            'spklu_id' => $spklu->id,
            'image_path' => $path,
        ]);

        return back()->with('success', 'Foto berhasil ditambahkan ke galeri SPKLU.');
    }

    public function destroy(Spklu $spklu, SpkluGalleryPhoto $photo)
    {
        $vendor = $this->getVendor();
        if ($spklu->vendor_id !== $vendor->id) abort(403);
        if ($photo->spklu_id !== $spklu->id) abort(404);

        // PERBAIKAN: Menggunakan kolom image_path
        if (Storage::disk('public')->exists($photo->image_path)) {
            Storage::disk('public')->delete($photo->image_path);
        }
        
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus dari galeri.');
    }
}