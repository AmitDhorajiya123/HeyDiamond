<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MstFluorescenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'fluorescence_id' => $this->fluorescence_id,
            'encrypted_id' => $this->encrypted_id ?? null,
            'fluorescence_code' => $this->fluorescence_code,
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'created_date' => $this->created_date?->toIso8601String(),
            'modified_date' => $this->modified_date?->toIso8601String(),
        ];
    }
}