<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCustomDates;

class MstFinishGrade extends Model
{
    use HasCustomDates;
    protected $table = 'mst_finish_grade';

    protected $primaryKey = 'finish_grade_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'finish_grade_code',
        'finish_grade_name',
        'grade_group',
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