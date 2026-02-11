<?php

namespace App\Modules\Affiliations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAffiliationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'status' => ['required', 'string', 'max:50'],
            'joined_at' => ['nullable', 'date'],
            'identity_number' => ['nullable', 'string', 'max:100'],
        ];
    }
}

