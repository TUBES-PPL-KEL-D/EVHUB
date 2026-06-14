<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorWithdrawal extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'vendor_id',
        'reference_code',
        'amount',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'notes',
        'status',
        'processed_at',
        'admin_notes',
        'receipt_path', 
    ];

    /**
     * Relasi ke entitas Vendor.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Relasi ke Admin yang memproses transaksi.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}