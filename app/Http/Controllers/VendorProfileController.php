<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VendorProfileController extends Controller
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

    public function create(Request $request)
    {
        $user = $this->resolveUser($request);

        if ($user->vendorProfile()->exists()) {
            return redirect()
                ->route('vendor.profile.show', $user->vendorProfile)
                ->with('success', 'Profil vendor Anda sudah tersimpan.');
        }

        return view('vendor_profiles.create');
    }

    public function store(Request $request)
    {
        $user = $this->resolveUser($request);

        if ($user->vendorProfile()->exists()) {
            return redirect()
                ->route('vendor.profile.show', $user->vendorProfile)
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
    $vendorProfile->user_id = $user->id;
        $vendorProfile->save();

        return redirect()
            ->route('vendor.profile.show', $vendorProfile)
            ->with('success', 'Profil vendor berhasil disimpan.');
    }

    public function show(Request $request, VendorProfile $vendorProfile)
    {
        $user = $this->resolveUser($request);

        if (! app()->environment('local') && $vendorProfile->user_id !== $user->id) {
            abort(403);
        }

        return view('vendor_profiles.show', compact('vendorProfile'));
    }
}
