<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMstSizeRangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('encrypted_id');
        
        return [
            'size_range_code' => ['sometimes', 'string', 'max:50', Rule::unique('mst_size_range', 'size_range_code')->ignore($id, 'size_range_id')],
            'range_type' => ['sometimes', 'string', 'max:50'],
            'min_carat' => ['sometimes', 'numeric', 'min:0'],
            'max_carat' => ['sometimes', 'numeric', 'min:0'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}