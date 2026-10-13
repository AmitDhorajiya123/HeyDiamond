<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MstFinishGradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'finish_grade_id' => $this->finish_grade_id,
            'encrypted_id' => $this->encrypted_id ?? null,
            'finish_grade_code' => $this->finish_grade_code,
            'finish_grade_name' => $this->finish_grade_name,
            'grade_group' => $this->grade_group,
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'created_date' => $this->created_date?->toIso8601String(),
            'modified_date' => $this->modified_date?->toIso8601String(),
        ];
    }
}