<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedDiamondAd extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'position' => 'integer',
    ];

    public function diamond()
    {
        return $this->belongsTo(Diamond::class);
    }
}
