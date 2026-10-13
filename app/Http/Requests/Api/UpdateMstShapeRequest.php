<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMstShapeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $shapeId = $this->route('encrypted_id');
        
        return [
            'shape_code' => ['sometimes', 'string', 'max:50', Rule::unique('mst_shape', 'shape_code')->ignore($shapeId, 'shape_id')],
            'shape_name' => ['sometimes', 'string', 'max:100'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}