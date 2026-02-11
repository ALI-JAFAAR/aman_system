<?php

namespace App\Modules\Insurance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInsuranceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'partner_filled_number' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'in:applied,active,rejected,pending'],
            'activated_at' => ['sometimes', 'nullable', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}

