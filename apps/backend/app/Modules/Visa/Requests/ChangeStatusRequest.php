<?php

namespace App\Modules\Visa\Requests;

use App\Modules\Visa\Enums\VisaCaseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('visa.manage');
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_column(VisaCaseStatus::cases(), 'value'))],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
