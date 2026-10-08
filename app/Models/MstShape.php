<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstShape extends Model
{
    protected $table = 'mst_shape';

    protected $primaryKey = 'shape_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'shape_code',
        'shape_name',
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