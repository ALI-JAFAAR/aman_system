<?php

namespace App\Modules\Claims\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'string', 'max:50'],
            'details' => ['sometimes', 'nullable', 'string'],
            'accident_date' => ['sometimes', 'nullable', 'date'],
            'amount_requested' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'string', 'max:50'],
            'resolution_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'resolution_note' => ['sometimes', 'nullable', 'string'],
        ];
    }
}

