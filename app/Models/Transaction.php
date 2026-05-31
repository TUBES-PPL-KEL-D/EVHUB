<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'charger_machine_id',
        'vehicle_id',
        'energy_consumed',
        'total_price',
        'started_at',
        'finished_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'energy_consumed' => 'float',
        'total_price' => 'decimal:2',
    ];

    // Relasi balik ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Charger Machine (Mesin pengisi daya)
    public function chargerMachine()
    {
        return $this->belongsTo(ChargerMachine::class);
    }

    // Relasi ke Wallet History (Jika transaksi ini memicu mutasi dompet)
    public function walletHistory()
    {
        return $this->hasOne(WalletHistory::class, 'reference_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}