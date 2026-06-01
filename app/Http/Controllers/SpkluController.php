<?php

namespace App\Http\Controllers;

use App\Models\Spklu;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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
        $spklu->load(['chargerMachines', 'vendor.profile']);

        if (Schema::hasTable('spklu_gallery_photos')) {
            $spklu->load('galleryPhotos');
        } else {
            $spklu->setRelation('galleryPhotos', collect());
        }

        $reviews = $spklu->reviews()->with('user')->latest()->paginate(5);
        return view('rider.spklu.show', compact('spklu', 'reviews'));
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
        // 1. Kumpulan Data Dummy SPKLU (diinjeksi agar peta langsung penuh tanpa query database)
        $dummyData = collect([
            [
                'id' => 1,
                'name' => 'SPKLU Gedung Sate',
                'address' => 'Jl. Diponegoro No.22, Citarum, Bandung',
                'latitude' => -6.902481,
                'longitude' => 107.618810,
                'status' => 'tersedia',
                'available' => 2,
                'total' => 4,
                'avg_rating' => 4.8,
                'review_count' => 15,
                'charger_machines' => [
                    ['connector_type' => 'CCS2', 'capacity_kw' => 50],
                    ['connector_type' => 'CHAdeMO', 'capacity_kw' => 50],
                ]
            ],
            [
                'id' => 2,
                'name' => 'SPKLU Braga CityWalk',
                'address' => 'Jl. Braga No.99-101, Braga, Sumur Bandung',
                'latitude' => -6.917464,
                'longitude' => 107.609559,
                'status' => 'penuh',
                'available' => 0,
                'total' => 2,
                'avg_rating' => 4.5,
                'review_count' => 8,
                'charger_machines' => [
                    ['connector_type' => 'Type 2 AC', 'capacity_kw' => 22],
                ]
            ],
            [
                'id' => 3,
                'name' => 'SPKLU Cihampelas Walk',
                'address' => 'Jl. Cihampelas No.160, Cipaganti, Coblong',
                'latitude' => -6.896483,
                'longitude' => 107.604622,
                'status' => 'tersedia',
                'available' => 1,
                'total' => 3,
                'avg_rating' => 4.2,
                'review_count' => 24,
                'charger_machines' => [
                    ['connector_type' => 'CCS2', 'capacity_kw' => 200],
                ]
            ],
            [
                'id' => 4,
                'name' => 'SPKLU Trans Studio Mall',
                'address' => 'Jl. Gatot Subroto No.289, Cibangkong, Batununggal',
                'latitude' => -6.925096,
                'longitude' => 107.636494,
                'status' => 'tersedia',
                'available' => 3,
                'total' => 5,
                'avg_rating' => 4.9,
                'review_count' => 56,
                'charger_machines' => [
                    ['connector_type' => 'CCS2', 'capacity_kw' => 50],
                    ['connector_type' => 'Type 2 AC', 'capacity_kw' => 22],
                ]
            ],
            [
                'id' => 5,
                'name' => 'SPKLU Paris Van Java',
                'address' => 'Jl. Sukajadi No.131-139, Cipedes, Sukajadi',
                'latitude' => -6.889241,
                'longitude' => 107.596007,
                'status' => 'offline',
                'available' => 0,
                'total' => 4,
                'avg_rating' => 3.8,
                'review_count' => 12,
                'charger_machines' => [
                    ['connector_type' => 'CCS2', 'capacity_kw' => 150],
                ]
            ],
            [
                'id' => 6,
                'name' => 'SPKLU Alun-Alun Bandung',
                'address' => 'Jl. Asia Afrika, Balonggede, Regol',
                'latitude' => -6.921851,
                'longitude' => 107.606226,
                'status' => 'tersedia',
                'available' => 1,
                'total' => 2,
                'avg_rating' => 4.6,
                'review_count' => 30,
                'charger_machines' => [
                    ['connector_type' => 'CHAdeMO', 'capacity_kw' => 50],
                ]
            ],
            [
                'id' => 7,
                'name' => 'SPKLU Pasteur Gateway',
                'address' => 'Jl. Dr. Djunjunan No.143-149, Pajajaran, Cicendo',
                'latitude' => -6.890662,
                'longitude' => 107.588686,
                'status' => 'penuh',
                'available' => 0,
                'total' => 3,
                'avg_rating' => 4.1,
                'review_count' => 19,
                'charger_machines' => [
                    ['connector_type' => 'CCS2', 'capacity_kw' => 50],
                ]
            ],
            [
                'id' => 8,
                'name' => 'SPKLU Buah Batu Square',
                'address' => 'Jl. Buah Batu, Turangga, Lengkong',
                'latitude' => -6.945890,
                'longitude' => 107.625800,
                'status' => 'tersedia',
                'available' => 4,
                'total' => 4,
                'avg_rating' => 4.7,
                'review_count' => 42,
                'charger_machines' => [
                    ['connector_type' => 'Type 2 AC', 'capacity_kw' => 22],
                    ['connector_type' => 'CCS2', 'capacity_kw' => 200],
                ]
            ]
        ]);

        // 2. Filter Pencarian Teks (Nama atau Alamat)
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $dummyData = $dummyData->filter(function ($item) use ($searchTerm) {
                return str_contains(strtolower($item['name']), $searchTerm) || 
                       str_contains(strtolower($item['address']), $searchTerm);
            });
        }

        // 3. Filter berdasarkan Status
        if ($request->filled('status') && $request->status !== 'semua') {
            $dummyData = $dummyData->where('status', $request->status);
        }

        return response()->json($dummyData->values()->all());
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

        $spklus = Spklu::with('chargerMachines')->get()->map(function ($spklu) use ($activeConnector) {
            $available = 0;
            $total = 0;
            $allMachines = collect();

            foreach ($spklu->chargerMachines as $machine) {
                $total++;
                $allMachines->push($machine);
                if (isset($machine->status) && strtolower($machine->status) === 'available') {
                    $available++;
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