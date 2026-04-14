<?php

namespace App\Http\Controllers;

use App\Models\ChargerMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChargerMachineController extends Controller
{
    // Menampilkan halaman form create
    public function create()
    {
        return view('vendor.chargers.create');
    }

    // Memproses data yang di-submit
    public function store(Request $request)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'connector_type' => 'required|string|max:100',
            'capacity_kw' => 'required|numeric|min:1',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ]);

        // 2. Proses unggah file
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('chargers', 'public');
            $validatedData['photo_path'] = $path;
        }

        // 3. Simpan data ke database
        // Asumsi autentikasi vendor menggunakan sistem default auth
        ChargerMachine::create([
            'vendor_id' => Auth::id() ?? 1, // Fallback ID 1 jika belum implementasi Auth
            'name' => $validatedData['name'],
            'location' => $validatedData['location'],
            'connector_type' => $validatedData['connector_type'],
            'capacity_kw' => $validatedData['capacity_kw'],
            'photo_path' => $validatedData['photo_path'],
        ]);

        // 4. Redirect dengan pesan sukses
        return redirect()->route('vendor.chargers.create')->with('success', 'Infrastruktur mesin charger berhasil ditambahkan!');
    }
}