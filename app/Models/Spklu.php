<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spklu extends Model
{
    protected $fillable = ['vendor_id', 'name', 'address', 'latitude', 'longitude'];

    // Relasi ke tabel chargers
    public function chargers()
    {
        return $this->hasMany(Charger::class, 'spklu_id');
    }
}