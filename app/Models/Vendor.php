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
        'legality_document_path',
        'status',
    ];

    /**
     * Relasi ke User (Pemilik akun vendor).
     * Setiap vendor terhubung ke satu akun user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke profil detail vendor (Pekerjaan Fakhri - PBI 5).
     * Memungkinkan admin melihat detail profil tambahan perusahaan.
     */
    public function profile()
    {
        return $this->hasOne(VendorProfile::class);
    }

    /**
     * Relasi ke mesin charger (Pekerjaan Riehand - PBI 15).
     * Memungkinkan sistem melacak mesin mana saja yang dimiliki vendor ini.
     */
    public function chargers()
    {
        return $this->hasMany(ChargerMachine::class);
    }
}