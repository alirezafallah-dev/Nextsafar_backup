<?php

namespace App\Modules\Visa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('visa.assign');
    }

    public function rules(): array
    {
        return [
            'agent_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
