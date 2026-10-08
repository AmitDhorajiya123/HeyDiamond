<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstFluorescence extends Model
{
    protected $table = 'mst_fluorescence';

    protected $primaryKey = 'fluorescence_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'fluorescence_code',
        'display_order',
        'is_active',
        'created_date',
        'modified_date',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
        'created_date' => 'datetime',
        'modified_date' => 'datetime',
    ];
}