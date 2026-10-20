<?php

namespace App\Modules\Order\Requests;

use App\Modules\Order\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('bookings.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable'],
            'type' => ['nullable', Rule::in(Order::ALL_TYPES)],
            'source' => ['nullable', 'string', 'max:50'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'assigned_to' => ['nullable'],
            'from_lead' => ['nullable', 'boolean'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'min_amount' => ['nullable', 'integer', 'min:0'],
            'max_amount' => ['nullable', 'integer', 'min:0'],
            'sort' => ['nullable', 'in:newest,oldest,amount_desc,amount_asc,priority'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
