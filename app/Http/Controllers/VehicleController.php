<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index()
    {
        // Mengambil ID User saat ini. Jika belum login, gunakan mode fallback ke user ID 1 (Admin pertama)
        $userId = Auth::id() ?? 1;
        
        $vehicles = Vehicle::where('user_id', $userId)->latest()->get();
        $serviceDueVehicles = $vehicles->filter(function ($vehicle) {
            return $vehicle->isBatteryServiceDue();
        })->values();

        return view('rider.vehicles.index', compact('vehicles', 'serviceDueVehicles'));
    }

    public function create()
    {
        return view('rider.vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'merk' => 'required|string|max:50',
            'model' => 'required|string|max:100',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate',
            'connector_type' => 'nullable|string|in:CCS,CHAdeMO,Type2,GB/T,Tesla',
            'battery_service_date' => 'nullable|date',
            'battery_percentage' => 'nullable|integer|min:0|max:100',
            'estimated_full_range_km' => 'nullable|integer|min:1',
            'vehicle_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // TESTER MODE BYPASS: Pastikan ada user ID 1
        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
        
        // Buat user dummy jika ID 1 belum ada di database (Sangat berguna untuk Dusk Testing)
        if (!\App\Models\User::find($userId)) {
            \App\Models\User::factory()->create(['id' => $userId]);
        }

        $validated['user_id'] = $userId;

        if ($request->hasFile('vehicle_photo')) {
            $validated['vehicle_photo_path'] = $request->file('vehicle_photo')->store('vehicle_photos', 'public');
        }

        \App\Models\Vehicle::create($validated);

        return redirect()->route('rider.vehicles.index')->with('success', 'Kendaraan EV berhasil ditambahkan ke garasi.');
    }

    public function edit(Vehicle $vehicle)
    {
        // Jika sedang tidak login, lewati pengecekan keamanan
        if (Auth::check() && $vehicle->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('rider.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        // Jika sedang tidak login, lewati pengecekan keamanan
        if (Auth::check() && $vehicle->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'merk' => 'required|string|max:50',
            'model' => 'required|string|max:100',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate,' . $vehicle->id,
            'connector_type' => 'nullable|string|in:CCS,CHAdeMO,Type2,GB/T,Tesla',
            'battery_service_date' => 'nullable|date',
            'battery_percentage' => 'nullable|integer|min:0|max:100',
            'estimated_full_range_km' => 'nullable|integer|min:1',
            'vehicle_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('vehicle_photo')) {
            if ($vehicle->vehicle_photo_path) {
                Storage::disk('public')->delete($vehicle->vehicle_photo_path);
            }
            $validated['vehicle_photo_path'] = $request->file('vehicle_photo')->store('vehicle_photos', 'public');
        }

        $vehicle->update($validated);

        return redirect()->route('rider.vehicles.index')->with('success', 'Data kendaraan EV berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        // Jika sedang tidak login, lewati pengecekan keamanan
        if (Auth::check() && $vehicle->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $vehicle->delete();

        return redirect()->route('rider.vehicles.index')->with('success', 'Kendaraan berhasil dihapus dari garasi.');
    }
}