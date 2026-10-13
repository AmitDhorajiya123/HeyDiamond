<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMstCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('encrypted_id');
        
        return [
            'category_code' => ['sometimes', 'string', 'max:50', Rule::unique('mst_category', 'category_code')->ignore($id, 'category_id')],
            'category_name' => ['sometimes', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}