<?php

namespace App\Modules\Tour\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('tours.edit') ?? false;
    }

    public function rules(): array
    {
        // همان قوانین StoreTourRequest ولی همه optional
        $rules = (new StoreTourRequest())->rules();
        
        // تبدیل required به nullable برای update
        foreach ($rules as $key => $rule) {
            if (is_array($rule)) {
                $rules[$key] = array_map(function ($r) {
                    if ($r === 'required') return 'sometimes';
                    return $r;
                }, $rule);
            }
        }

        return $rules;
    }
}
