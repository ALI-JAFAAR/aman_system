<?php

namespace App\Modules\Insurance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => ['sometimes', 'integer', 'exists:organizations,id'],
            'service_type' => ['sometimes', 'string', 'max:100'],
            'initiator_type' => ['sometimes', 'string', 'max:100'],
            'platform_rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'organization_rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'partner_rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'contract_start' => ['sometimes', 'nullable', 'date'],
            'contract_end' => ['sometimes', 'nullable', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'partner_offering_id' => ['sometimes', 'nullable', 'integer', 'exists:partner_offerings,id'],
            'platform_share' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'organization_share' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'partner_share' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'contract_version' => ['sometimes', 'nullable', 'string', 'max:50'],
        ];
    }
}

