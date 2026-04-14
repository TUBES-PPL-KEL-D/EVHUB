<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function create(Request $request)
    {
        $vendorProfile = $request->user()->vendorProfile;

        if (! $vendorProfile) {
            return redirect()
                ->route('vendor.profile.create')
                ->with('error', 'Silakan lengkapi profil vendor terlebih dahulu.');
        }

        return view('vendor_documents.create', [
            'vendorProfile' => $vendorProfile,
            'vendor' => $request->user()->vendor,
        ]);
    }

    public function store(Request $request)
    {
        $vendorProfile = $request->user()->vendorProfile;

        if (! $vendorProfile) {
            return redirect()
                ->route('vendor.profile.create')
                ->with('error', 'Silakan lengkapi profil vendor terlebih dahulu.');
        }

        $validated = $request->validate([
            'legality_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $existingVendor = $request->user()->vendor;

        if ($existingVendor && $existingVendor->legality_document_path) {
            Storage::disk('public')->delete($existingVendor->legality_document_path);
        }

        $documentPath = $validated['legality_document']->store('vendor/legalities', 'public');

        $vendor = Vendor::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'company_name' => $vendorProfile->company_name,
                'legality_document_path' => $documentPath,
                'status' => 'Pending',
            ]
        );

        return redirect()
            ->route('vendor.documents.show', $vendor)
            ->with('success', 'Dokumen legalitas berhasil diunggah.');
    }

    public function show(Request $request, Vendor $document)
    {
        if ($document->user_id !== $request->user()->id) {
            abort(403);
        }

        return view('vendor_documents.show', [
            'vendor' => $document,
        ]);
    }
}