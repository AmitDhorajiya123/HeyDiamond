<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\EncryptedId;

class UpdateLocationRequest extends FormRequest
{
    use EncryptedId;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locationId = $this->decryptId((string) $this->route('encrypted_id'));

        return [
            'location_code' => ['sometimes', 'required', 'string', 'max:10', Rule::unique('mst_location', 'location_code')->ignore($locationId, 'location_id')],
            'location_name' => ['sometimes', 'required', 'string', 'max:100'],
            'display_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
