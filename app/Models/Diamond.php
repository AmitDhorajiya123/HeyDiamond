<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diamond extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function apiClient()
    {
        return $this->belongsTo(ApiClient::class, 'api_client_id');
    }
}
