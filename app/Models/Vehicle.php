<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $fillable = [
        'user_id',
        'merk',
        'model',
        'license_plate',
    ];

    // Relasi ke User (Pemilik Kendaraan)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}