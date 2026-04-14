<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\VendorProfile;
use Illuminate\Http\Request;

class VendorProfileController extends Controller
{
    public function create()
    {
        if (request()->user()->vendorProfile()->exists()) {
            return redirect()
                ->route('vendor.profile.show', request()->user()->vendorProfile)
                ->with('success', 'Profil vendor Anda sudah tersimpan.');
        }

        return view('vendor_profiles.create');
    }

    public function store(Request $request)
    {
        if ($request->user()->vendorProfile()->exists()) {
            return redirect()
                ->route('vendor.profile.show', $request->user()->vendorProfile)
                ->with('success', 'Profil vendor Anda sudah tersimpan.');
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:30'],
            'company_address' => ['required', 'string', 'max:1000'],
            'company_description' => ['nullable', 'string', 'max:2000'],
        ]);

        $vendorProfile = new VendorProfile($validated);
        $vendorProfile->user_id = $request->user()->id;
        $vendorProfile->save();

        return redirect()
            ->route('vendor.profile.show', $vendorProfile)
            ->with('success', 'Profil vendor berhasil disimpan.');
    }

    public function show(VendorProfile $vendorProfile)
    {
        if ($vendorProfile->user_id !== request()->user()->id) {
            abort(403);
        }

        return view('vendor_profiles.show', compact('vendorProfile'));
    }
}
