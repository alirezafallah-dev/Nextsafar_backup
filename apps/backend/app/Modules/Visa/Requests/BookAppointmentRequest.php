<?php

namespace App\Modules\Visa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('visa.manage');
    }

    public function rules(): array
    {
        return [
            'datetime' => ['required', 'date', 'after:now'],
            'location' => ['required', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:100'],
        ];
    }
}
