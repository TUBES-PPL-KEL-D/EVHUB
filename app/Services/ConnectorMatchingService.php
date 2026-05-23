<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Spklu;
use App\Models\ChargerMachine;
use Illuminate\Database\Eloquent\Collection;

class ConnectorMatchingService
{
    /**
     * Get available connector types
     */
    public static function getAvailableConnectors(): array
    {
        return [
            'CCS' => 'CCS (Combined Charging System)',
            'CHAdeMO' => 'CHAdeMO',
            'Type2' => 'Type 2 / Mennekes',
            'GB/T' => 'GB/T',
            'Tesla' => 'Tesla Connector',
        ];
    }

    /**
     * Check if vehicle connector matches with a specific charger machine
     */
    public static function isCompatible(Vehicle $vehicle, ChargerMachine $chargerMachine): bool
    {
        if (!$vehicle->connector_type || !$chargerMachine->connector_type) {
            return false;
        }

        return strtolower($vehicle->connector_type) === strtolower($chargerMachine->connector_type);
    }

    /**
     * Get compatible charger machines for a vehicle
     */
    public static function getCompatibleChargers(Vehicle $vehicle): Collection
    {
        if (!$vehicle->connector_type) {
            return collect();
        }

        return ChargerMachine::where('connector_type', $vehicle->connector_type)
            ->where('status', 'Available')
            ->get();
    }

    /**
     * Get compatible stations (SPKLU) for a vehicle
     */
    public static function getCompatibleStations(Vehicle $vehicle): Collection
    {
        if (!$vehicle->connector_type) {
            return collect();
        }

        return Spklu::whereHas('chargerMachines', function ($query) use ($vehicle) {
            $query->where('connector_type', $vehicle->connector_type)
                ->where('status', 'Available');
        })->get();
    }

    /**
     * Get matching status for a vehicle and station
     */
    public static function getMatchingStatus(Vehicle $vehicle, Spklu $station): array
    {
        $compatibleChargers = $station->chargerMachines()
            ->where('connector_type', $vehicle->connector_type ?? '')
            ->where('status', 'Available')
            ->get();

        return [
            'is_compatible' => $compatibleChargers->count() > 0,
            'count' => $compatibleChargers->count(),
            'chargers' => $compatibleChargers,
        ];
    }
}
