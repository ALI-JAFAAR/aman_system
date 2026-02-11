<?php

namespace App\Modules\Insurance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'service_type' => ['required', 'string', 'max:100'],
            'initiator_type' => ['required', 'string', 'max:100'],
            'platform_rate' => ['nullable', 'numeric', 'min:0'],
            'organization_rate' => ['nullable', 'numeric', 'min:0'],
            'partner_rate' => ['nullable', 'numeric', 'min:0'],
            'contract_start' => ['nullable', 'date'],
            'contract_end' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'partner_offering_id' => ['nullable', 'integer', 'exists:partner_offerings,id'],
            'platform_share' => ['nullable', 'numeric', 'min:0'],
            'organization_share' => ['nullable', 'numeric', 'min:0'],
            'partner_share' => ['nullable', 'numeric', 'min:0'],
            'contract_version' => ['nullable', 'string', 'max:50'],
        ];
    }
}

