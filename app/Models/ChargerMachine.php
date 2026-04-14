<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargerMachine extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'location',
        'connector_type',
        'capacity_kw',
        'photo_path',
        'status',
    ];

    // Relasi: Setiap mesin dimiliki oleh satu vendor (User)
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}