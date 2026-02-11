<?php

namespace App\Modules\Claims\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_offering_id' => ['required', 'integer', 'exists:user_offerings,id'],
            'type' => ['required', 'string', 'max:50'],
            'details' => ['nullable', 'string'],
            'accident_date' => ['nullable', 'date'],
            'amount_requested' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:50'],
            'resolution_amount' => ['nullable', 'numeric', 'min:0'],
            'resolution_note' => ['nullable', 'string'],
            'submitted_at' => ['nullable', 'date'],
        ];
    }
}

