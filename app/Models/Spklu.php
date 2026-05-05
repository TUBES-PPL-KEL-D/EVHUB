<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spklu extends Model
{
    // Tambahkan izin fillable ini agar fungsi create() di controller bisa berjalan
    protected $fillable = [
        'vendor_id',
        'name',
        'address',
        'latitude',
        'longitude',
    ];
    
    public function chargerMachines()
    {
        return $this->hasMany(ChargerMachine::class, 'spklu_id');
    }

    // buat modal
    public function chargers()
    {
        return $this->hasMany(Charger::class, 'spklu_id');
    }
}