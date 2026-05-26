<?php

namespace App\Http\Controllers;

use App\Models\Spklu; 
use Illuminate\Http\Request;

class SpkluController extends Controller
{
    public function index()
    {
        // Mengambil data koordinat dan info SPKLU dari database
        $spklus = Spklu::select('name', 'address', 'latitude', 'longitude')->get();

        // Mengirimkan data $spklus ke file resources/views/vendor/map.blade.php
        return view('vendor.map', compact('spklus'));
    }

    public function getMarkers()
    {
        $spklus = Spklu::with('chargerMachines')->get();
        return response()->json($spklus);
    }

    public function getSpkluData()
    {
        // Mengambil data SPKLU
        $spklus = Spklu::with('chargers')->get();
        return response()->json($spklus);
    }

    public function getDynamicMarkers(Request $request)
    {
        // 1. Inisiasi Query
        $query = Spklu::with('chargers.machines');

        // 2. Filter Pencarian Teks (Nama atau Alamat)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('address', 'LIKE', "%{$searchTerm}%");
            });
        }

        // 3. Mapping data dan Kalkulasi Status
        $spklus = $query->get()->map(function ($spklu) {
            $available = 0;
            $total = 0;
            $charger_machines = []; // Menyimpan data mesin untuk frontend

            foreach ($spklu->chargers as $charger) {
                foreach ($charger->machines as $machine) {
                    $total++;
                    if (strtolower($machine->status) === 'available') {
                        $available++;
                    }
                    
                    // Menyusun data port untuk popup peta
                    $charger_machines[] = [
                        'connector_type' => $machine->connector_type,
                        'capacity_kw' => $machine->capacity_kw
                    ];
                }
            }

            if ($total === 0) {
                $status = 'offline';
            } elseif ($available > 0) {
                $status = 'tersedia';
            } else {
                $status = 'penuh';
            }

            return [
                'id' => $spklu->id,
                'name' => $spklu->name,
                'latitude' => $spklu->latitude,
                'longitude' => $spklu->longitude,
                'status' => $status,
                'available' => $available,
                'total' => $total,
                'charger_machines' => $charger_machines, // Ditambahkan agar data popup port muncul
            ];
        });

        // 4. Filter berdasarkan Status setelah dikalkulasi
        if ($request->filled('status') && $request->status !== 'semua') {
            $spklus = $spklus->where('status', $request->status)->values();
        }

        return response()->json($spklus);
    }
}