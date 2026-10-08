<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstColor extends Model
{
    protected $table = 'mst_color';

    protected $primaryKey = 'color_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'color_code',
        'color_type',
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