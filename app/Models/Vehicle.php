<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\ConnectorMatchingService;

class Vehicle extends Model
{
    protected $fillable = [
        'user_id',
        'merk',
        'model',
        'license_plate',
        'connector_type',
    ];

    // Relasi ke User (Pemilik Kendaraan)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if vehicle connector is compatible with a charger machine
     */
    public function isCompatibleWith(ChargerMachine $chargerMachine): bool
    {
        return ConnectorMatchingService::isCompatible($this, $chargerMachine);
    }

    /**
     * Get compatible charger machines for this vehicle
     */
    public function getCompatibleChargers()
    {
        return ConnectorMatchingService::getCompatibleChargers($this);
    }

    /**
     * Get compatible stations (SPKLU) for this vehicle
     */
    public function getCompatibleStations()
    {
        return ConnectorMatchingService::getCompatibleStations($this);
    }

    /**
     * Get matching status for this vehicle and a station
     */
    public function getMatchingStatus(Spklu $station): array
    {
        return ConnectorMatchingService::getMatchingStatus($this, $station);
    }

    /**
     * Get connector display name
     */
    public function getConnectorDisplayName(): string
    {
        $connectors = ConnectorMatchingService::getAvailableConnectors();
        return $connectors[$this->connector_type] ?? 'Unknown';
    }
}