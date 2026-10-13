<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'location_id' => $this->location_id,
            'encrypted_id' => $this->encrypted_id ?? \App\Traits\EncryptedId::encryptIdStatic($this->location_id),
            'location_code' => $this->location_code,
            'location_name' => $this->location_name,
            'display_order' => $this->display_order,
            'is_active' => (bool) $this->is_active,
            'created_date' => $this->created_date?->toIso8601String(),
            'modified_date' => $this->modified_date?->toIso8601String(),
        ];
    }
}
