<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCustomDates;

class MstSizeRange extends Model
{
    use HasCustomDates;
    protected $table = 'mst_size_range';

    protected $primaryKey = 'size_range_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'size_range_code',
        'range_type',
        'min_carat',
        'max_carat',
        'display_order',
        'is_active',
        'created_date',
        'modified_date',
    ];

    protected $casts = [
        'min_carat' => 'decimal:3',
        'max_carat' => 'decimal:3',
        'display_order' => 'integer',
        'is_active' => 'boolean',
        'created_date' => 'datetime',
        'modified_date' => 'datetime',
    ];
}