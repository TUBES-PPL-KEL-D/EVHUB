<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChargerMachine extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'spklu_id',
        'name',
        'connector_type',
        'capacity_kw',
        'price_per_kwh',
        'operational_hours',
        'photo_path',
        'status',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    // Relasi: Menghubungkan Mesin Charger ke SPKLU
    public function spklu(): BelongsTo
    {
        return $this->belongsTo(Spklu::class, 'spklu_id');
    }
}