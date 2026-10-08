<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstBgmBlackOption extends Model
{
    protected $table = 'mst_bgm_black_option';

    protected $primaryKey = 'option_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'option_code',
        'option_name',
        'is_active',
        'created_date',
        'modified_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_date' => 'datetime',
        'modified_date' => 'datetime',
    ];
}