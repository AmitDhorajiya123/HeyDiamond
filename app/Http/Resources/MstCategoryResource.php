<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MstCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'category_id' => $this->category_id,
            'encrypted_id' => $this->encrypted_id ?? null,
            'category_code' => $this->category_code,
            'category_name' => $this->category_name,
            'is_active' => $this->is_active,
            'created_date' => $this->created_date?->toIso8601String(),
            'modified_date' => $this->modified_date?->toIso8601String(),
        ];
    }
}