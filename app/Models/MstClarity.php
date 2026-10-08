<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstClarity extends Model
{
    protected $table = 'mst_clarity';

    protected $primaryKey = 'clarity_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'clarity_code',
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