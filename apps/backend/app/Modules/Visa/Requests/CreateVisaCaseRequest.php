<?php

namespace App\Modules\Visa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateVisaCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'visa_type_id' => ['required', 'integer', 'exists:visa_types,id'],
            'intended_travel_date' => ['nullable', 'date', 'after:today'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_expiry_date' => ['nullable', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
