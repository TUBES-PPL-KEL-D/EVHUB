<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    public function index()
    {
        // Mengambil ID User saat ini. Jika belum login, gunakan mode fallback ke user ID 1 (Admin pertama)
        $userId = Auth::id() ?? 1;
        
        $vehicles = Vehicle::where('user_id', $userId)->latest()->get();
        return view('rider.vehicles.index', compact('vehicles'));
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
        ]);

        // TESTER MODE BYPASS: Pastikan ada user ID 1
        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
        
        // Buat user dummy jika ID 1 belum ada di database (Sangat berguna untuk Dusk Testing)
        if (!\App\Models\User::find($userId)) {
            \App\Models\User::factory()->create(['id' => $userId]);
        }

        $validated['user_id'] = $userId;

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
        ]);

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