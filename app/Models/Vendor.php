<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    // Mengizinkan kolom-kolom ini diisi data secara massal (mass assignment)
    protected $fillable = [
        'user_id',
        'company_name',
        'legality_document_path',
        'status',
    ];

    // Mendaftarkan relasi: Setiap Vendor "dimiliki" (belongsTo) oleh satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Opsional: Relasi ke tabel Chargers (Merging PBI Rehan)
    public function chargers()
    {
        return $this->hasMany(ChargerMachine::class);
    }
}