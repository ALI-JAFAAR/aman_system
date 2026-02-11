<?php

namespace App\Modules\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? null;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password' => ['sometimes', 'string', 'min:8'],
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'family_relation' => ['sometimes', 'nullable', 'string', 'max:50'],
        ];
    }
}

