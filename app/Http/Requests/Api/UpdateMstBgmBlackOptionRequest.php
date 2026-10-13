<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMstBgmBlackOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('encrypted_id');
        
        return [
            'option_code' => ['sometimes', 'string', 'max:50', Rule::unique('mst_bgm_black_option', 'option_code')->ignore($id, 'option_id')],
            'option_name' => ['sometimes', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}