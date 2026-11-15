<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiClient extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Price-range markup slabs, ordered from the lowest $/ct tier.
     */
    public function markups()
    {
        return $this->hasMany(ApiClientMarkup::class)->orderBy('min_value');
    }
}
