<?php

namespace App\Modules\Visa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:5000'],
            'type' => ['nullable', 'in:general,document_issue,embassy_note,customer_query,internal,call_log,warning,important'],
            'visibility' => ['nullable', 'in:internal,public'],
            'parent_id' => ['nullable', 'integer', 'exists:visa_case_notes,id'],
        ];
    }
}
