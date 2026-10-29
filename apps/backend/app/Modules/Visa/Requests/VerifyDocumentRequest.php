<?php

namespace App\Modules\Visa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('visa.manage');
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:approved,needs_correction,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
