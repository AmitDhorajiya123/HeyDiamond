<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MstShapeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'shape_id' => $this->shape_id,
            'encrypted_id' => $this->encrypted_id ?? null,
            'shape_code' => $this->shape_code,
            'shape_name' => $this->shape_name,
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'created_date' => $this->created_date?->toIso8601String(),
            'modified_date' => $this->modified_date?->toIso8601String(),
        ];
    }
}