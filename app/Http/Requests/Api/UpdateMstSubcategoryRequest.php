<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMstSubcategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('encrypted_id');
        
        return [
            'category_id' => ['sometimes', 'integer', 'exists:mst_category,category_id'],
            'subcategory_code' => ['sometimes', 'string', 'max:50', Rule::unique('mst_subcategory', 'subcategory_code')->ignore($id, 'subcategory_id')],
            'subcategory_name' => ['sometimes', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}