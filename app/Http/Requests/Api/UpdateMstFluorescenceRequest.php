<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMstFluorescenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('encrypted_id');
        
        return [
            'fluorescence_code' => ['sometimes', 'string', 'max:50', Rule::unique('mst_fluorescence', 'fluorescence_code')->ignore($id, 'fluorescence_id')],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}