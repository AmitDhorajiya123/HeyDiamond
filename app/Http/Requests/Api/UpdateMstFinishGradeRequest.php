<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMstFinishGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('encrypted_id');
        
        return [
            'finish_grade_code' => ['sometimes', 'string', 'max:50', Rule::unique('mst_finish_grade', 'finish_grade_code')->ignore($id, 'finish_grade_id')],
            'finish_grade_name' => ['sometimes', 'string', 'max:100'],
            'grade_group' => ['sometimes', 'string', 'max:50'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}