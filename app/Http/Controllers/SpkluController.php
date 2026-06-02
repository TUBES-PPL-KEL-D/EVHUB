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

    /**
     * Mengambil data marker secara dinamis dari database untuk merespons Peta Leaflet Rider.
     * [TELAH DIPERBAIKI: MENGHAPUS HARDCODED DUMMY, SEKARANG MEMBACA DATABASE RIIL VENDOR]
     */
    public function getDynamicMarkers(Request $request)
    {
        // 1. Tarik seluruh data SPKLU riil dari database beserta relasi mesin chargernya
        $spkluRows = Spklu::with(['chargerMachines', 'reviews'])->get();

        // 2. Transformasikan data database menjadi format JSON bento-box yang dibutuhkan Leaflet
        $formattedData = $spkluRows->map(function ($spklu) {
            $totalMachines = $spklu->chargerMachines->count();
            
            // Hitung jumlah mesin yang statusnya 'available' (Tersedia)
            $availableMachines = $spklu->chargerMachines->filter(function ($machine) {
                return strtolower($machine->status ?? '') === 'available';
            })->count();

            // Hitung rata-rata rating ulasan dari pengendara
            $avgRating = $spklu->reviews->count() > 0 ? round($spklu->reviews->avg('rating'), 1) : 0;

            // Tentukan status visual stasiun berdasarkan kondisi mesin charger
            $status = 'offline';
            if ($totalMachines > 0) {
                $status = $availableMachines > 0 ? 'tersedia' : 'penuh';
            }

            // Susun struktur array mesin di dalam stasiun terkait
            $machinesArray = $spklu->chargerMachines->map(function ($machine) {
                return [
                    'connector_type' => $machine->connector_type ?? 'Unknown',
                    'capacity_kw' => (int) ($machine->capacity_kw ?? 0),
                ];
            })->values()->all();

            return [
                'id' => $spklu->id,
                'name' => $spklu->name,
                'address' => $spklu->address ?? 'Alamat tidak tertera',
                'latitude' => (float) $spklu->latitude,
                'longitude' => (float) $spklu->longitude,
                'status' => $status,
                'available' => $availableMachines,
                'total' => $totalMachines,
                'avg_rating' => $avgRating ?: 5.0, // fallback visual rating jika belum ada ulasan
                'review_count' => $spklu->reviews->count(),
                'charger_machines' => $machinesArray,
            ];
        });

        // 3. Masukkan filter pencarian teks (Nama atau Alamat) jika pengendara mengetik di search bar
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $formattedData = $formattedData->filter(function ($item) use ($searchTerm) {
                return str_contains(strtolower($item['name']), $searchTerm) || 
                       str_contains(strtolower($item['address']), $searchTerm);
            });
        }

        // 4. Masukkan filter dropdown berdasarkan Status ketersediaan mesin
        if ($request->filled('status') && $request->status !== 'semua') {
            $formattedData = $formattedData->where('status', $request->status);
        }

        return response()->json($formattedData->values()->all());
    }

    /**
     * Endpoint tambahan untuk pencocokan tipe konektor kendaraan aktif milik pengendara.
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