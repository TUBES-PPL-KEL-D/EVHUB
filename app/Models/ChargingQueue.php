<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargingQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'charger_machine_id',
        'status',
        'queued_at',
    ];

    protected $casts = [
        'queued_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chargerMachine()
    {
        return $this->belongsTo(ChargerMachine::class);
    }
}