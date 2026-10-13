<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MstSubcategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'subcategory_id' => $this->subcategory_id,
            'encrypted_id' => $this->encrypted_id ?? null,
            'category_id' => $this->category_id,
            'subcategory_code' => $this->subcategory_code,
            'subcategory_name' => $this->subcategory_name,
            'is_active' => $this->is_active,
            'created_date' => $this->created_date?->toIso8601String(),
            'modified_date' => $this->modified_date?->toIso8601String(),
        ];
    }
}