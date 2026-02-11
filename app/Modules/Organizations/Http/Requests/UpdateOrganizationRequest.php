<?php

namespace App\Modules\Organizations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:100'],
            'details' => ['sometimes', 'nullable'],
            'organization_id' => ['sometimes', 'nullable', 'integer', 'exists:organizations,id'],
            'code' => ['sometimes', 'nullable', 'string', 'max:50'],
            'next_identity_sequence' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }
}

