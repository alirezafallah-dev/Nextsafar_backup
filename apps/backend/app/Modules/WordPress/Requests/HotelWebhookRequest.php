<?php

namespace App\Modules\WordPress\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    public function rules(): array
    {
        return [
            'event' => ['required', 'string', 'in:created,updated,deleted,trashed,published,drafted'],
            'wp_post_id' => ['required', 'integer', 'min:1'],
            'content_version' => ['nullable', 'string', 'max:100'],
            
            // Hotel data (required for created/updated)
            'slug' => ['required_unless:event,deleted,trashed', 'nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/'],
            'name' => ['required_unless:event,deleted,trashed', 'nullable', 'string', 'max:255'],
            'publish_status' => ['nullable', 'string', 'in:draft,published,private,trash,deleted'],
            
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            
            'short_description' => ['nullable', 'string', 'max:2000'],
            'full_description' => ['nullable', 'string'],
            'faq' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'categories' => ['nullable', 'array'],
            
            'featured_image_url' => ['nullable', 'string', 'url', 'max:500'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['string', 'url', 'max:500'],
            
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            
            'facilities' => ['nullable', 'array'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
            'review_count' => ['nullable', 'integer', 'min:0'],
            
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keyword' => ['nullable', 'string', 'max:255'],
            'seo_image_url' => ['nullable', 'string', 'url', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'url', 'max:500'],
            
            'metadata' => ['nullable', 'array'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'event.required' => 'نوع رویداد الزامی است.',
            'event.in' => 'نوع رویداد معتبر نیست.',
            'wp_post_id.required' => 'شناسه پست وردپرس الزامی است.',
            'slug.required_unless' => 'اسلاگ برای این رویداد الزامی است.',
            'slug.regex' => 'اسلاگ فقط می‌تواند شامل حروف کوچک، اعداد و خط تیره باشد.',
            'name.required_unless' => 'نام هتل برای این رویداد الزامی است.',
            'latitude.between' => 'عرض جغرافیایی باید بین -90 و 90 باشد.',
            'longitude.between' => 'طول جغرافیایی باید بین -180 و 180 باشد.',
            'rating.between' => 'امتیاز باید بین 0 و 5 باشد.',
        ];
    }
}
