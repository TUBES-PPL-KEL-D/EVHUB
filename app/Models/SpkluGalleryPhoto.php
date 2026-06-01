<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpkluGalleryPhoto extends Model
{
    protected $fillable = [
        'spklu_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function spklu(): BelongsTo
    {
        return $this->belongsTo(Spklu::class);
    }
}