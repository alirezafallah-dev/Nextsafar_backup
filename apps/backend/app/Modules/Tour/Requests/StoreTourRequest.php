<?php

namespace App\Modules\Tour\Requests;

use App\Modules\Tour\Models\Tour;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('tours.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string'],
            'destination_country' => ['required', 'string', 'max:100'],
            'destination_city' => ['nullable', 'string', 'max:100'],
            'origin_city' => ['nullable', 'string', 'max:100'],
            'duration_nights' => ['nullable', 'integer', 'min:0', 'max:365'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'transport_type' => ['nullable', 'string', 'in:airplane,bus,train,car,ship'],
            'airline' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', Rule::in(Tour::ALL_STATUSES)],
            'is_featured' => ['nullable', 'boolean'],
            'currency' => ['nullable', 'string', 'max:10'],
            'category_id' => ['nullable', 'integer', 'exists:tour_categories,id'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'cancellation_policy' => ['nullable', 'string'],
            'important_notes' => ['nullable', 'string'],
            'required_documents' => ['nullable', 'array'],
            'required_documents.*' => ['string', 'max:255'],
            'min_age' => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_age' => ['nullable', 'integer', 'min:0', 'max:100'],
            
            // Dates
            'dates' => ['nullable', 'array'],
            'dates.*.departure_date' => ['required_with:dates', 'date', 'after:today'],
            'dates.*.return_date' => ['required_with:dates', 'date', 'after:dates.*.departure_date'],
            'dates.*.total_capacity' => ['required_with:dates', 'integer', 'min:1'],
            'dates.*.adult_price' => ['required_with:dates', 'integer', 'min:0'],
            'dates.*.child_price' => ['nullable', 'integer', 'min:0'],
            'dates.*.infant_price' => ['nullable', 'integer', 'min:0'],
            'dates.*.single_supplement' => ['nullable', 'integer', 'min:0'],
            'dates.*.discount_amount' => ['nullable', 'integer', 'min:0'],
            'dates.*.discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'dates.*.is_guaranteed' => ['nullable', 'boolean'],
            'dates.*.notes' => ['nullable', 'string'],
            
            // Itineraries
            'itineraries' => ['nullable', 'array'],
            'itineraries.*.day_number' => ['required_with:itineraries', 'integer', 'min:1'],
            'itineraries.*.title' => ['required_with:itineraries', 'string', 'max:255'],
            'itineraries.*.description' => ['required_with:itineraries', 'string'],
            'itineraries.*.has_breakfast' => ['nullable', 'boolean'],
            'itineraries.*.has_lunch' => ['nullable', 'boolean'],
            'itineraries.*.has_dinner' => ['nullable', 'boolean'],
            'itineraries.*.accommodation_name' => ['nullable', 'string', 'max:255'],
            'itineraries.*.activities' => ['nullable', 'array'],
            
            // Services
            'services' => ['nullable', 'array'],
            'services.*.type' => ['required_with:services', 'in:included,excluded'],
            'services.*.title' => ['required_with:services', 'string', 'max:255'],
            'services.*.icon' => ['nullable', 'string', 'max:50'],
            
            // Relations
            'destination_ids' => ['nullable', 'array'],
            'destination_ids.*' => ['integer', 'exists:tour_destinations,id'],
            'hotel_ids' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان تور الزامی است.',
            'destination_country.required' => 'کشور مقصد الزامی است.',
            'dates.*.departure_date.after' => 'تاریخ حرکت باید بعد از امروز باشد.',
            'dates.*.return_date.after' => 'تاریخ بازگشت باید بعد از تاریخ حرکت باشد.',
        ];
    }
}
