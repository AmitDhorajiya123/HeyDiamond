<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MstSubcategory extends Model
{
    protected $table = 'mst_subcategory';

    protected $primaryKey = 'subcategory_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'subcategory_code',
        'subcategory_name',
        'is_active',
        'created_date',
        'modified_date',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'is_active' => 'boolean',
        'created_date' => 'datetime',
        'modified_date' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MstCategory::class, 'category_id', 'category_id');
    }
}