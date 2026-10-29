<?php

namespace App\Modules\Order\Requests;

use App\Modules\Order\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConvertLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('leads.convert_to_booking') ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(Order::ALL_TYPES)],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'tax_amount' => ['nullable', 'integer', 'min:0'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
