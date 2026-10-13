<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'mobile_no' => ['nullable', 'string', 'max:20'],
            'role' => ['nullable', 'string', 'in:admin,user,manager'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }
}
