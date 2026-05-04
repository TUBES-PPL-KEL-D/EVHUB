<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // tambah ini
use Illuminate\Database\Eloquent\Model;

class VendorProfile extends Model
{
    use HasFactory; // tambah ini

    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'company_description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}