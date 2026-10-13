<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MstSizeRangeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'size_range_id' => $this->size_range_id,
            'encrypted_id' => $this->encrypted_id ?? null,
            'size_range_code' => $this->size_range_code,
            'range_type' => $this->range_type,
            'min_carat' => $this->min_carat,
            'max_carat' => $this->max_carat,
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'created_date' => $this->created_date?->toIso8601String(),
            'modified_date' => $this->modified_date?->toIso8601String(),
        ];
    }
}