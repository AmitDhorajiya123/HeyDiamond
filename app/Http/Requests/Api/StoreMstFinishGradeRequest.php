<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreMstFinishGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'finish_grade_code' => ['required', 'string', 'max:50', 'unique:mst_finish_grade,finish_grade_code'],
            'finish_grade_name' => ['required', 'string', 'max:100'],
            'grade_group' => ['required', 'string', 'max:50'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}