<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreMstSizeRangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'size_range_code' => ['required', 'string', 'max:50', 'unique:mst_size_range,size_range_code'],
            'range_type' => ['required', 'string', 'max:50'],
            'min_carat' => ['required', 'numeric', 'min:0'],
            'max_carat' => ['required', 'numeric', 'min:0'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}