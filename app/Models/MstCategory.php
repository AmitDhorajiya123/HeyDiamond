<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasCustomDates;

class MstCategory extends Model
{
    use HasCustomDates;
    protected $table = 'mst_category';

    protected $primaryKey = 'category_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'category_code',
        'category_name',
        'is_active',
        'created_date',
        'modified_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_date' => 'datetime',
        'modified_date' => 'datetime',
    ];

    public function subcategories(): HasMany
    {
        return $this->hasMany(MstSubcategory::class, 'category_id', 'category_id');
    }
}