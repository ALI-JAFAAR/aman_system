<?php

namespace App\Modules\Insurance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnerOfferingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'package_id' => ['required', 'integer', 'exists:packages,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'contract_start' => ['nullable', 'date'],
            'contract_end' => ['nullable', 'date'],
            'auto_approve' => ['nullable', 'boolean'],
            'partner_must_fill_number' => ['nullable', 'boolean'],
        ];
    }
}

