<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class VendorProfile extends Model
{
    use HasFactory; 

    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'company_description',
        'latitude',
        'longitude',
        'opens_at',
        'closes_at',
        'npwp',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}