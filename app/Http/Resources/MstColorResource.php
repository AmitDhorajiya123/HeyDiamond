<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MstColorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'color_id' => $this->color_id,
            'encrypted_id' => $this->encrypted_id ?? null,
            'color_code' => $this->color_code,
            'color_type' => $this->color_type,
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'created_date' => $this->created_date?->toIso8601String(),
            'modified_date' => $this->modified_date?->toIso8601String(),
        ];
    }
}