<?php

namespace App\Modules\Organizations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'details' => ['nullable'],
            'organization_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'code' => ['nullable', 'string', 'max:50'],
            'next_identity_sequence' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

