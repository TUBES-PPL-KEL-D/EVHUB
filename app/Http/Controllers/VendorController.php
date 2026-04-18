<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    protected function resolveUser(Request $request): User
    {
        if ($request->user()) {
            return $request->user();
        }

        if (! app()->environment('local')) {
            abort(401);
        }

        return User::firstOrCreate(
            ['email' => 'vendor.local@test.dev'],
            [
                'name' => 'Vendor Local',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
                'phone' => '081234567890',
            ]
        );
    }

    protected function ensureOwner(Request $request, Vendor $vendor): void
    {
        $user = $this->resolveUser($request);

        if ($vendor->user_id !== $user->id) {
            abort(403);
        }
    }

    public function create(Request $request)
    {
        $user = $this->resolveUser($request);
        $vendorProfile = $user->vendorProfile;

        if (! $vendorProfile) {
            return redirect()
                ->route('vendor.profile.create')
                ->with('error', 'Silakan lengkapi profil vendor terlebih dahulu.');
        }

        return view('vendor_documents.create', [
            'vendorProfile' => $vendorProfile,
            'vendor' => $user->vendor,
        ]);
    }

    public function store(Request $request)
    {
        $user = $this->resolveUser($request);
        $vendorProfile = $user->vendorProfile;

        if (! $vendorProfile) {
            return redirect()
                ->route('vendor.profile.create')
                ->with('error', 'Silakan lengkapi profil vendor terlebih dahulu.');
        }

        $validated = $request->validate([
            'legality_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $existingVendor = $user->vendor;

        if ($existingVendor && $existingVendor->legality_document_path) {
            Storage::disk('public')->delete($existingVendor->legality_document_path);
        }

        $documentPath = $validated['legality_document']->store('vendor/legalities', 'public');

        $vendor = Vendor::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $vendorProfile->company_name,
                'legality_document_path' => $documentPath,
                'status' => 'Pending',
            ]
        );

        return redirect()
            ->route('vendor.status')
            ->with('success', 'Dokumen legalitas berhasil diunggah.');
    }

    public function status(Request $request)
    {
        $user = $this->resolveUser($request);
        $vendor = $user->vendor;

        if (! $vendor) {
            return redirect()
                ->route('vendor.documents.create')
                ->with('error', 'Silakan unggah dokumen legalitas terlebih dahulu.');
        }

        return view('vendor_documents.status', [
            'vendor' => $vendor,
        ]);
    }

    public function edit(Request $request, Vendor $document)
    {
        $this->ensureOwner($request, $document);

        if ($document->status !== 'Rejected') {
            return redirect()
                ->route('vendor.status')
                ->with('error', 'Dokumen hanya bisa diperbaiki saat status ditolak.');
        }

        return view('vendor_documents.edit', [
            'vendor' => $document,
        ]);
    }

    public function update(Request $request, Vendor $document)
    {
        $this->ensureOwner($request, $document);

        if ($document->status !== 'Rejected') {
            return redirect()
                ->route('vendor.status')
                ->with('error', 'Dokumen hanya bisa diperbaiki saat status ditolak.');
        }

        $validated = $request->validate([
            'legality_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($document->legality_document_path) {
            Storage::disk('public')->delete($document->legality_document_path);
        }

        $document->legality_document_path = $validated['legality_document']->store('vendor/legalities', 'public');
        $document->status = 'Pending';
        $document->save();

        return redirect()
            ->route('vendor.status')
            ->with('success', 'Dokumen perbaikan berhasil diunggah. Status kembali menjadi Pending.');
    }

    public function show(Request $request, Vendor $document)
    {
        $this->ensureOwner($request, $document);

        return view('vendor_documents.show', [
            'vendor' => $document,
        ]);
    }
}