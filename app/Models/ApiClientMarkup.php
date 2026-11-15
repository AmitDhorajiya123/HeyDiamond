<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiClientMarkup extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'min_value' => 'float',
        'max_value' => 'float',
        'markup' => 'float',
    ];

    public function apiClient()
    {
        return $this->belongsTo(ApiClient::class);
    }
}
