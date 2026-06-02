<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     * Status yang didukung: 'Pending', 'Approved', 'Rejected', 'Suspended'.
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'legality_document_path', // Proteksi SUDAH DIBUKA agar tidak bernilai NULL lagi
        'status',
    ];

    /**
     * Relasi ke User (Pemilik akun vendor).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke profil detail vendor.
     */
    public function profile()
    {
        return $this->hasOne(VendorProfile::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke mesin charger.
     */
    public function chargers()
    {
        return $this->hasMany(ChargerMachine::class);
    }

    public function warnings()
    {
        return $this->hasMany(VendorWarning::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(VendorVendorWithdrawal::class);
    }
}