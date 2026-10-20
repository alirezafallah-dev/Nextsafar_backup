<?php

namespace App\Modules\Order\Requests;

use App\Modules\Order\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('bookings.change_status') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(Order::ALL_STATUSES)],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'وضعیت جدید الزامی است.',
            'status.in' => 'وضعیت نامعتبر است.',
        ];
    }
}
