<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMstColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $colorId = $this->route('encrypted_id');
        
        return [
            'color_code' => ['sometimes', 'string', 'max:50', Rule::unique('mst_color', 'color_code')->ignore($colorId, 'color_id')],
            'color_type' => ['sometimes', 'string', 'max:100'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}