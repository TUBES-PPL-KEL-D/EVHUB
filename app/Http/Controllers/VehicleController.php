<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    public function index()
    {
        // Menampilkan kendaraan milik user yang sedang login
        $vehicles = Vehicle::where('user_id', Auth::id())->latest()->get();
        return view('rider.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('rider.vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'merk' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate',
        ]);

        $validated['user_id'] = 1;

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Kendaraan EV berhasil ditambahkan ke garasi.');
    }

    public function edit(Vehicle $vehicle)
    {
        // Keamanan: Cegah user edit kendaraan orang lain
        if ($vehicle->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('rider.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'merk' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate,' . $vehicle->id,
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil dihapus dari garasi.');
    }
}