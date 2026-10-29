<?php

namespace App\Modules\Order\Requests;

use App\Modules\Order\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('bookings.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(Order::ALL_TYPES)],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'source' => ['nullable', 'string', 'max:50'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'tax_amount' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
            'customer_notes' => ['nullable', 'string', 'max:5000'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'نوع سفارش الزامی است.',
            'type.in' => 'نوع سفارش نامعتبر است.',
        ];
    }
}
