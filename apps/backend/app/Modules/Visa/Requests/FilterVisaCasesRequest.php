<?php

namespace App\Modules\Visa\Requests;

use App\Modules\Visa\Enums\VisaCaseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterVisaCasesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('visa.view');
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(array_column(VisaCaseStatus::cases(), 'value'))],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'visa_type_id' => ['nullable', 'integer', 'exists:visa_types,id'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'search' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'overdue' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
