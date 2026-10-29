<?php

namespace App\Modules\Order\Requests;

use App\Modules\Order\Models\OrderItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('bookings.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'item_type' => ['required', Rule::in([OrderItem::ITEM_TOUR, OrderItem::ITEM_HOTEL, OrderItem::ITEM_VISA, OrderItem::ITEM_SERVICE])],
            'item_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'code' => ['nullable', 'string', 'max:50'],
            'service_date' => ['nullable', 'date'],
            'service_end_date' => ['nullable', 'date', 'after_or_equal:service_date'],
            'destination' => ['nullable', 'string', 'max:200'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'unit_price' => ['required', 'integer', 'min:0'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'extra_data' => ['nullable', 'array'],
        ];
    }
}
