<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Tiket ini dimiliki oleh satu pengguna (user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}