<?php

namespace App\Modules\Document\Requests;

use App\Modules\Document\Enums\DocumentCategory;
use App\Modules\Document\Enums\DocumentVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:20480'], // 20MB
            'category' => ['required', Rule::in(array_column(DocumentCategory::cases(), 'value'))],
            'visibility' => ['nullable', Rule::in(array_column(DocumentVisibility::cases(), 'value'))],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function getCategory(): DocumentCategory
    {
        return DocumentCategory::from($this->input('category'));
    }

    public function getVisibility(): DocumentVisibility
    {
        $visibility = $this->input('visibility');
        
        if ($visibility) {
            return DocumentVisibility::from($visibility);
        }

        // اگر visibility مشخص نشده، بر اساس category حدس بزن
        $category = $this->getCategory();
        return $category->isPublic() ? DocumentVisibility::PUBLIC : DocumentVisibility::PRIVATE;
    }
}
