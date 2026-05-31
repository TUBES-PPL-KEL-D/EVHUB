<?php

namespace App\Http\Controllers;

use App\Models\ChargerMachine;
use App\Models\Spklu;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChargerMachineController extends Controller
{
    /**
     * Cek status vendor. 
     * [PRE-PRODUCTION BYPASS AKTIF]
     */
    private function checkVendorStatus()
    {
        // --- BYPASS UNTUK TESTING / PRE-PRODUCTION ---
        // Memaksa sistem selalu menggunakan Vendor dengan ID 1
        $vendor = \App\Models\Vendor::find(1);
        
        return $vendor;

        // --- KODE ASLI UNTUK PRODUCTION NANTI (Jangan dihapus) ---
        /*
        $userId = Auth::id(); 
        $vendor = Vendor::where('user_id', $userId)->first();

        if (!$vendor || $vendor->status !== 'Approved') {
            return false;
        }
        return $vendor;
        */
    }

    public function index()
    {
        $vendor = $this->checkVendorStatus();
        if (!$vendor) {
            return redirect()->route('vendor.status')
                ->with('error', 'Akses ditolak! Vendor ID 1 tidak ditemukan di database.');
        }

        $chargers = ChargerMachine::with('spklu')
            ->where('vendor_id', $vendor->id)
            ->get();

        return view('vendor.chargers.index', compact('chargers'));
    }

    public function create()
    {
        $vendor = $this->checkVendorStatus();
        if (!$vendor) {
            return redirect()->route('vendor.status')->with('error', 'Akses ditolak!');
        }

        return view('vendor.chargers.create');
    }

    public function store(Request $request)
    {
        $vendor = $this->checkVendorStatus();
        if (!$vendor) {
            return redirect()->route('vendor.status')->with('error', 'Akses ditolak!');
        }

        $validatedData = $request->validate([
            'spklu_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'name' => 'required|string|max:255',
            'connector_type' => 'required|string|in:Type 1,Type 2,CCS1,CCS2,CHAdeMO,GB/T,NACS',
            'capacity_kw' => 'required|numeric|min:1',
            'price_per_kwh' => 'required|numeric|min:0',
            // Perubahan pada validasi waktu
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $spklu = Spklu::create([
            'vendor_id' => $vendor->id,
            'name' => $validatedData['spklu_name'],
            'address' => $validatedData['address'],
            'latitude' => $validatedData['latitude'],
            'longitude' => $validatedData['longitude'],
        ]);

        $path = $request->file('photo')->store('chargers', 'public');

        // Menggabungkan waktu buka dan tutup
        $operationalHours = $validatedData['open_time'] . ' - ' . $validatedData['close_time'];

        ChargerMachine::create([
            'vendor_id' => $vendor->id, 
            'spklu_id' => $spklu->id,
            'name' => $validatedData['name'],
            'connector_type' => $validatedData['connector_type'],
            'capacity_kw' => $validatedData['capacity_kw'],
            'price_per_kwh' => $validatedData['price_per_kwh'],
            'operational_hours' => $operationalHours,
            'photo_path' => $path,
            'status' => 'available',
        ]);

        return redirect()->route('vendor.chargers.index')->with('success', 'Infrastruktur SPKLU dan Mesin berhasil diletakkan pada peta!');
    }

    public function edit(ChargerMachine $charger)
    {
        $vendor = $this->checkVendorStatus();
        if (!$vendor) {
            return redirect()->route('vendor.status')->with('error', 'Akses ditolak!');
        }

        return view('vendor.chargers.edit', compact('charger'));
    }

    public function update(Request $request, ChargerMachine $charger)
    {
        $vendor = $this->checkVendorStatus();
        if (!$vendor) {
            return redirect()->route('vendor.status')->with('error', 'Akses ditolak!');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'connector_type' => 'required|string|in:Type 1,Type 2,CCS1,CCS2,CHAdeMO,GB/T,NACS',
            'capacity_kw' => 'required|numeric|min:1',
            'price_per_kwh' => 'required|numeric|min:0',
            // Perubahan pada validasi waktu
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
            'status' => 'required|in:available,unavailable,maintenance',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Menggabungkan waktu buka dan tutup
        $validatedData['operational_hours'] = $validatedData['open_time'] . ' - ' . $validatedData['close_time'];
        
        // Membersihkan array dari key yang tidak ada di kolom database
        unset($validatedData['open_time']);
        unset($validatedData['close_time']);

        if ($request->hasFile('photo')) {
            if (Storage::disk('public')->exists($charger->photo_path)) {
                Storage::disk('public')->delete($charger->photo_path);
            }
            $validatedData['photo_path'] = $request->file('photo')->store('chargers', 'public');
        }

        $charger->update($validatedData);

        return redirect()->route('vendor.chargers.index')->with('success', 'Detail mesin charger berhasil diperbarui!');
    }

    public function destroy(ChargerMachine $charger)
    {
        $vendor = $this->checkVendorStatus();
        if (!$vendor) {
            return redirect()->route('vendor.status')->with('error', 'Akses ditolak!');
        }

        $spklu_id = $charger->spklu_id;

        if (Storage::disk('public')->exists($charger->photo_path)) {
            Storage::disk('public')->delete($charger->photo_path);
        }
        
        $charger->delete();
        
        if ($spklu_id) {
            Spklu::where('id', $spklu_id)->delete();
        }

        return redirect()->route('vendor.chargers.index')->with('success', 'Aset mesin dan lokasi SPKLU berhasil dihapus permanen!'); // Pastikan untuk menghapus data terkait di database jika diperlukan
    }
}