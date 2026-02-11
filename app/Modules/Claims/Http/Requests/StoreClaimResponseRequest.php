<?php

namespace App\Modules\Claims\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'max:100'],
            'actor_type' => ['required', 'in:employee,user'],
            'actor_id' => ['required', 'integer'],
            'message' => ['nullable', 'string'],
        ];
    }
}

