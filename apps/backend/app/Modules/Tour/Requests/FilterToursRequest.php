<?php

namespace App\Modules\Tour\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterToursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:tour_categories,id'],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'min_price' => ['nullable', 'integer', 'min:0'],
            'max_price' => ['nullable', 'integer', 'min:0', 'gte:min_price'],
            'min_duration' => ['nullable', 'integer', 'min:1'],
            'max_duration' => ['nullable', 'integer', 'min:1', 'gte:min_duration'],
            'departure_from' => ['nullable', 'date'],
            'departure_to' => ['nullable', 'date', 'after_or_equal:departure_from'],
            'featured_only' => ['nullable', 'boolean'],
            'guaranteed_only' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'string', 'in:newest,oldest,price_asc,price_desc,duration_asc,duration_desc,popular'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function getFilters(): array
    {
        return $this->only([
            'category_id',
            'country',
            'city',
            'min_price',
            'max_price',
            'min_duration',
            'max_duration',
            'departure_from',
            'departure_to',
            'featured_only',
            'guaranteed_only',
            'sort',
        ]);
    }
}
