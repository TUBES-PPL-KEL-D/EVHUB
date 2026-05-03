<?php

namespace App\Http\Controllers;

use App\Models\ChargerMachine;
use App\Models\Spklu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChargerMachineController extends Controller
{
    // Read: Menampilkan daftar mesin charger milik vendor
    public function index()
{
    // Mengambil data charger milik vendor yang sedang login beserta info SPKLU-nya
    $chargers = ChargerMachine::with('spklu')
        ->where('vendor_id', auth()->id())
        ->get();

    return view('vendor.chargers.index', compact('chargers'));
}

    // Create: Menampilkan form tambah
    public function create()
{
    // Mengambil daftar stasiun SPKLU untuk dipilih di formulir
    $spklus = Spklu::all();
    return view('vendor.chargers.create', compact('spklus'));
}

    // Store: Memproses data simpan
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'spklu_id' => 'required|exists:spklus,id',
            'name' => 'required|string|max:255',
            'connector_type' => 'required|string|max:100',
            'capacity_kw' => 'required|numeric|min:1',
            'price_per_kwh' => 'required|numeric|min:0',
            'operational_hours' => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('photo')->store('chargers', 'public');

        ChargerMachine::create([
            'vendor_id' => Auth::id() ?? 1,
            'spklu_id' => $validatedData['spklu_id'],
            'name' => $validatedData['name'],
            'connector_type' => $validatedData['connector_type'],
            'capacity_kw' => $validatedData['capacity_kw'],
            'price_per_kwh' => $validatedData['price_per_kwh'],
            'operational_hours' => $validatedData['operational_hours'],
            'photo_path' => $path,
            'status' => 'available', // Default saat create
        ]);

        return redirect()->route('vendor.chargers.index')->with('success', 'Mesin charger berhasil ditambahkan!');
    }

    // Edit: Menampilkan form edit
    public function edit(ChargerMachine $charger)
    {
        $spklus = Spklu::all();
        return view('vendor.chargers.edit', compact('charger', 'spklus'));
    }

    // Update: Memproses pembaruan data
    public function update(Request $request, ChargerMachine $charger)
    {
        $validatedData = $request->validate([
            'spklu_id' => 'required|exists:spklus,id',
            'name' => 'required|string|max:255',
            'connector_type' => 'required|string|max:100',
            'capacity_kw' => 'required|numeric|min:1',
            'price_per_kwh' => 'required|numeric|min:0',
            'operational_hours' => 'required|string|max:255',
            'status' => 'required|in:available,unavailable,maintenance',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Hapus foto lama
            if (Storage::disk('public')->exists($charger->photo_path)) {
                Storage::disk('public')->delete($charger->photo_path);
            }
            $validatedData['photo_path'] = $request->file('photo')->store('chargers', 'public');
        }

        $charger->update($validatedData);

        return redirect()->route('vendor.chargers.index')->with('success', 'Mesin charger berhasil diperbarui!');
    }

    // Delete: Memproses penghapusan data
    public function destroy(ChargerMachine $charger)
    {
        if (Storage::disk('public')->exists($charger->photo_path)) {
            Storage::disk('public')->delete($charger->photo_path);
        }
        $charger->delete();

        return redirect()->route('vendor.chargers.index')->with('success', 'Mesin charger berhasil dihapus!');
    }
}