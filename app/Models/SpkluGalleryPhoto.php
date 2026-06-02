<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkluGalleryPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'spklu_id',
        'image_path', // Diubah kembali ke penamaan asli database
        'caption',
        'sort_order',
    ];

    public function spklu()
    {
        return $this->belongsTo(Spklu::class);
    }
}