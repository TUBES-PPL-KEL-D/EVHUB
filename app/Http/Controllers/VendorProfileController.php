<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $localUserId = $request->session()->get('local_vendor_user_id');

        if ($localUserId) {
            $existingLocalUser = User::find($localUserId);
            if ($existingLocalUser) {
                return $existingLocalUser;
            }
        }

        $user = User::create([
            'name' => 'Vendor Local '.now()->format('His'),
            'email' => 'vendor.local+'.Str::lower((string) Str::uuid()).'@test.dev',
            'password' => Hash::make('password123'),
            'role' => 'vendor',
            'phone' => '081234567890',
        ]);

        $request->session()->put('local_vendor_user_id', $user->id);

        return $user;
    }

    public function create(Request $request)
    {
        $user = $this->resolveUser($request);

        return view('vendor_profiles.create', [
            'vendorProfile' => $user->vendorProfile,
        ]);
    }

    public function store(Request $request)
    {
        $user = $this->resolveUser($request);
        $hadProfile = $user->vendorProfile()->exists();

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:30'],
            'company_address' => ['required', 'string', 'max:1000'],
            'company_description' => ['nullable', 'string', 'max:2000'],
        ]);

        $vendorProfile = VendorProfile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        if ($user->vendor) {
            $user->vendor->update([
                'company_name' => $vendorProfile->company_name,
            ]);
        }

        if ($hadProfile) {
            return redirect()
                ->route('vendor.profile.show', $vendorProfile)
                ->with('success', 'Profil vendor berhasil diperbarui.');
        }

        return redirect()
            ->route('vendor.documents.create')
            ->with('success', 'Profil vendor berhasil disimpan. Lanjut upload dokumen legalitas.');
    }

    public function show(Request $request, VendorProfile $vendorProfile)
    {
        $user = $this->resolveUser($request);
        $currentUserProfile = $user->vendorProfile;

        if (! $currentUserProfile) {
            return redirect()
                ->route('vendor.profile.create')
                ->with('error', 'Silakan isi profil vendor terlebih dahulu.');
        }

        if ($vendorProfile->user_id !== $user->id) {
            // Avoid redirect loops by always rendering the active user's own profile.
            $vendorProfile = $currentUserProfile;
        }

        return view('vendor_profiles.show', compact('vendorProfile'));
    }
}
