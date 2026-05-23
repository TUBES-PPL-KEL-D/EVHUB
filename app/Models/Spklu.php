<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spklu extends Model
{
    protected $fillable = [
        'vendor_id',
        'name',
        'latitude',
        'longitude',
        'address',
    ];    

    public function chargerMachines()
    {
        return $this->hasMany(ChargerMachine::class, 'spklu_id', 'id');
    }

    // buat modal
    public function chargers()
    {
        return $this->hasMany(Charger::class, 'spklu_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}