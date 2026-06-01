<?php

namespace App\Http\Controllers;

use App\Models\Spklu;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpkluController extends Controller
{
    public function index()
    {
        // Mengambil data koordinat dan info SPKLU dari database
        $spklus = Spklu::select('name', 'address', 'latitude', 'longitude')->get();

        // Mengirimkan data $spklus ke file resources/views/vendor/map.blade.php
        return view('vendor.map', compact('spklus'));
    }

    public function show(Spklu $spklu)
    {
        $spklu->load(['chargerMachines', 'vendor.profile', 'reviews.user']);
        return view('rider.spklu.show', compact('spklu'));
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

    /**
     * Additive endpoint: return markers with user's active vehicle connector matching info.
     * This method is added so existing functions are not modified and integrations can opt in.
     */
    public function getMarkersWithVehicleMatching()
    {
        $userId = Auth::id();
        $activeVehicle = null;
        $activeConnector = null;

        if ($userId) {
            $activeVehicle = Vehicle::where('user_id', $userId)->latest()->first();
            $activeConnector = $activeVehicle?->connector_type;
        }

        $spklus = Spklu::with('chargers.machines')->get()->map(function ($spklu) use ($activeConnector) {
            $available = 0;
            $total = 0;
            $allMachines = collect();

            foreach ($spklu->chargers as $charger) {
                foreach ($charger->machines as $machine) {
                    $total++;
                    $allMachines->push($machine);
                    if (isset($machine->status) && strtolower($machine->status) === 'available') {
                        $available++;
                    }
                }
            }

            $matched = collect();
            if ($activeConnector) {
                $matched = $allMachines->filter(function ($m) use ($activeConnector) {
                    return isset($m->connector_type) && strcasecmp($m->connector_type, $activeConnector) === 0;
                });
            }

            $status = 'offline';
            if ($total > 0) {
                $status = $available > 0 ? 'tersedia' : 'penuh';
            }

            return [
                'id' => $spklu->id,
                'name' => $spklu->name,
                'address' => $spklu->address ?? null,
                'latitude' => $spklu->latitude,
                'longitude' => $spklu->longitude,
                'status' => $status,
                'available' => $available,
                'total' => $total,
                'compatible' => $matched->isNotEmpty(),
                'matched_chargers' => $matched->map(function ($machine) {
                    return [
                        'id' => $machine->id,
                        'connector_type' => $machine->connector_type ?? null,
                        'capacity_kw' => $machine->capacity_kw ?? null,
                        'status' => $machine->status ?? null,
                    ];
                })->values(),
                'charger_machines' => $allMachines->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'connector_type' => $m->connector_type ?? null,
                        'capacity_kw' => $m->capacity_kw ?? null,
                        'status' => $m->status ?? null,
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'active_connector' => $activeConnector,
            'active_vehicle' => $activeVehicle ? [
                'id' => $activeVehicle->id,
                'merk' => $activeVehicle->merk,
                'model' => $activeVehicle->model,
                'connector_type' => $activeVehicle->connector_type,
            ] : null,
            'spklus' => $spklus,
        ]);
    }
}