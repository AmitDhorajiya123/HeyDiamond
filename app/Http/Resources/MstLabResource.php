<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MstLabResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'lab_id' => $this->lab_id,
            'encrypted_id' => $this->encrypted_id ?? null,
            'lab_code' => $this->lab_code,
            'lab_name' => $this->lab_name,
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'created_date' => $this->created_date?->toIso8601String(),
            'modified_date' => $this->modified_date?->toIso8601String(),
        ];
    }
}