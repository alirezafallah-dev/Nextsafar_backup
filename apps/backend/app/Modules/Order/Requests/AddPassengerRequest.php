<?php

namespace App\Modules\Order\Requests;

use App\Modules\Order\Models\OrderPassenger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddPassengerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('bookings.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'order_item_id' => ['nullable', 'integer', 'exists:order_items,id'],
            'type' => ['required', Rule::in([OrderPassenger::TYPE_ADULT, OrderPassenger::TYPE_CHILD, OrderPassenger::TYPE_INFANT])],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'first_name_en' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z\s\-]+$/'],
            'last_name_en' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z\s\-]+$/'],
            'national_id' => ['nullable', 'string', 'max:20'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in([OrderPassenger::GENDER_MALE, OrderPassenger::GENDER_FEMALE, OrderPassenger::GENDER_OTHER])],
            'nationality' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'relation_to_owner' => ['nullable', 'string', 'max:50'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
            'price' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name_en.regex' => 'نام لاتین فقط می‌تواند شامل حروف انگلیسی باشد.',
            'last_name_en.regex' => 'نام خانوادگی لاتین فقط می‌تواند شامل حروف انگلیسی باشد.',
        ];
    }
}
