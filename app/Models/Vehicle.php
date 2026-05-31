<?php

namespace App\Models;

use Carbon\Carbon;
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
        'battery_service_date',
        'battery_percentage',
        'estimated_full_range_km',
    ];

    protected $casts = [
        'battery_service_date' => 'date',
        'battery_percentage' => 'integer',
        'estimated_full_range_km' => 'integer',
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'vehicle_id');
    }
}
