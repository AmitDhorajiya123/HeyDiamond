<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user') ? ($this->route('user')->id ?? $this->route('user')) : null;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'mobile_no' => ['nullable', 'string', 'max:20'],
            'role' => ['nullable', 'string', 'in:admin,user,manager'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'password' => ['nullable', 'string', 'min:6'],
        ];
    }
}
