<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMarkup extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'min_value' => 'float',
        'max_value' => 'float',
        'markup' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
