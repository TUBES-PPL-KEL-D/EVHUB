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

        // Mengirimkan data $spklus ke file resources/views/welcome.blade.php
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

    public function getDynamicMarkers()
    {
        $spklus = Spklu::with('chargerMachines')->get()->map(function ($spklu) {
            $available = 0;
            $total = 0;
            $charger_machines = [];

            foreach ($spklu->chargerMachines as $machine) {
                $total++;
                // Sesuaikan string 'available' dengan isi database kamu
                if (strtolower($machine->status) === 'available') {
                    $available++;
                }

                $charger_machines[] = [
                    'connector_type' => $machine->connector_type,
                    'capacity_kw' => $machine->capacity_kw,
                    'status' => strtolower($machine->status)
                ];
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
                'charger_machines' => $charger_machines,
            ];
        });

        return response()->json($spklus);
    }
}