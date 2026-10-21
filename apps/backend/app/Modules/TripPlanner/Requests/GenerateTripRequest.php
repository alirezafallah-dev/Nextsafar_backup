<?php

namespace App\Modules\TripPlanner\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    public function rules(): array
    {
        return [
            'destination' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'days' => 'required|integer|min:1|max:30',
            'budget_level' => 'required|in:economy,medium,luxury',
            'interests' => 'nullable|array',
            'interests.*' => 'string|max:50',
            'travelers' => 'required|integer|min:1|max:20',
            'start_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'destination.required' => 'مقصد سفر الزامی است',
            'days.required' => 'تعداد روزهای سفر الزامی است',
            'days.min' => 'حداقل 1 روز',
            'days.max' => 'حداکثر 30 روز',
            'budget_level.required' => 'سطح بودجه الزامی است',
            'budget_level.in' => 'سطح بودجه نامعتبر (economy, medium, luxury)',
            'travelers.required' => 'تعداد مسافران الزامی است',
            'travelers.min' => 'حداقل 1 مسافر',
            'travelers.max' => 'حداکثر 20 مسافر',
            'start_date.after_or_equal' => 'تاریخ شروع باید امروز یا بعد از آن باشد',
        ];
    }
}
