<?php

namespace App\Modules\TripPlanner\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTripPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => 'nullable|string|max:64',
        ];
    }
}
