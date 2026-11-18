<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\EncryptedId;

class UpdateUserRequest extends FormRequest
{
    use EncryptedId;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->decryptId((string) $this->route('encrypted_id'));

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
