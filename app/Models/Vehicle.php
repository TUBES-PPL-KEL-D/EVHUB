<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\ConnectorMatchingService;
use Illuminate\Support\Facades\Storage;

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
        'vehicle_photo_path',
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
<<<<<<< HEAD
        if (! $this->battery_service_date) {
            return false;
        }

        return $this->battery_service_date->isPast() || $this->battery_service_date->lessThanOrEqualTo(Carbon::now()->addDays(30));
    }

    public function batteryServiceStatus(): string
    {
        if (! $this->battery_service_date) {
            return 'Jadwal servis belum diatur';
        }

        if ($this->battery_service_date->isPast()) {
            return 'Lewat jatuh tempo';
        }

        if ($this->battery_service_date->lessThanOrEqualTo(Carbon::now()->addDays(30))) {
            return 'Segera servis';
        }

        return 'Akan datang';
    }

    public function calculateRemainingRange(): ?int
    {
        if ($this->battery_percentage === null || $this->estimated_full_range_km === null) {
            return null;
        }

        return (int) round($this->estimated_full_range_km * ($this->battery_percentage / 100));
    }

    public function getRemainingRangeAttribute(): ?int
    {
        return $this->calculateRemainingRange();
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->vehicle_photo_path) {
            return null;
        }

        if (! Storage::disk('public')->exists($this->vehicle_photo_path)) {
            return null;
        }

        return '/storage/' . ltrim($this->vehicle_photo_path, '/');
    }

    public function batteryServiceDueInDays(): ?int
    {
        if (! $this->battery_service_date) {
            return null;
        }

        return $this->battery_service_date->diffInDays(Carbon::now(), false);
=======
        return $this->hasMany(Transaction::class, 'vehicle_id');
>>>>>>> 89575638015ab5532f605eaaa1fc37522bf3a1f1
    }
}
