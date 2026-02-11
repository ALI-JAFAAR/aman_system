<?php

namespace App\Modules\Insurance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnerOfferingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => ['sometimes', 'integer', 'exists:organizations,id'],
            'package_id' => ['sometimes', 'integer', 'exists:packages,id'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'contract_start' => ['sometimes', 'nullable', 'date'],
            'contract_end' => ['sometimes', 'nullable', 'date'],
            'auto_approve' => ['sometimes', 'nullable', 'boolean'],
            'partner_must_fill_number' => ['sometimes', 'nullable', 'boolean'],
        ];
    }
}

