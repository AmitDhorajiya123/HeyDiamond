<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMstClarityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clarityId = $this->route('encrypted_id');
        
        return [
            'clarity_code' => ['sometimes', 'string', 'max:50', Rule::unique('mst_clarity', 'clarity_code')->ignore($clarityId, 'clarity_id')],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}