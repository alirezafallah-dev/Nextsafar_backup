<?php

namespace App\Modules\Visa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'document_id' => ['required', 'integer', 'exists:documents,id'],
            'checklist_item_id' => ['nullable', 'integer', 'exists:visa_document_checklist,id'],
            'passenger_id' => ['nullable', 'integer', 'exists:visa_case_passengers,id'],
            'is_original' => ['nullable', 'boolean'],
            'needs_translation' => ['nullable', 'boolean'],
        ];
    }
}
