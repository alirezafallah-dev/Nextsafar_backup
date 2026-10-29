<?php

namespace App\Modules\Visa\Models;

use App\Modules\Visa\Enums\EntryType;
use App\Modules\Visa\Enums\VisaCategory;
use App\Modules\Visa\Enums\VisaStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * نوع ویزا
 * 
 * این مدل معادل post type "visa" در وردپرس است.
 * محتوا از وردپرس سینک می‌شود، ولی قیمت و قوانین تجاری در لاراول مدیریت می‌شوند.
 */
class VisaType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'wp_post_id', 'wp_slug', 'country_id', 'title', 'title_en', 'slug',
        'category', 'processing_days_min', 'processing_days_max',
        'validity_days', 'max_stay_days', 'entry_type',
        'base_price', 'embassy_fee', 'total_price', 'currency',
        'short_description', 'full_description', 'requirements', 'faqs',
        'seo_title', 'seo_description', 'seo_image',
        'status', 'is_featured', 'accepts_online_application',
        'sync_version', 'last_synced_at',
    ];

    protected $casts = [
        'category' => VisaCategory::class,
        'entry_type' => EntryType::class,
        'status' => VisaStatus::class,
        'requirements' => 'array',
        'faqs' => 'array',
        'base_price' => 'integer',
        'embassy_fee' => 'integer',
        'total_price' => 'integer',
        'processing_days_min' => 'integer',
        'processing_days_max' => 'integer',
        'validity_days' => 'integer',
        'max_stay_days' => 'integer',
        'is_featured' => 'boolean',
        'accepts_online_application' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    // === Boot ===
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (VisaType $visa) {
            // محاسبه خودکار total_price
            $visa->total_price = ($visa->base_price ?? 0) + ($visa->embassy_fee ?? 0);
        });
    }

    // === Relationships ===
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(VisaRule::class);
    }

    public function checklist(): HasMany
    {
        return $this->hasMany(VisaDocumentChecklist::class)->orderBy('sort_order');
    }

    // === Scopes ===
    public function scopePublished($query)
    {
        return $query->where('status', VisaStatus::PUBLISHED);
    }

    public function scopeAvailable($query)
    {
        return $query->published()
            ->where('accepts_online_application', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfCategory($query, VisaCategory $category)
    {
        return $query->where('category', $category);
    }

    public function scopeForCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeByCountrySlug($query, string $slug)
    {
        return $query->whereHas('country', fn($q) => $q->where('slug', $slug));
    }

    // === Methods ===
    public function isAvailable(): bool
    {
        return $this->status->isAvailable() && $this->accepts_online_application;
    }

    public function getProcessingTimeLabelAttribute(): string
    {
        if (!$this->processing_days_min && !$this->processing_days_max) {
            return 'نامشخص';
        }

        if ($this->processing_days_min === $this->processing_days_max) {
            return "{$this->processing_days_min} روز";
        }

        return "{$this->processing_days_min} تا {$this->processing_days_max} روز";
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return number_format($this->total_price) . ' ' . $this->currency;
    }

    public function getDisplayNameAttribute(): string
    {
        $country = $this->country?->name ?? '';
        return trim("ویزا {$country} {$this->category->label()}");
    }

    /**
     * آیا نیاز به سینک مجدد از وردپرس دارد؟
     */
    public function needsSync(): bool
    {
        if (!$this->last_synced_at) {
            return true;
        }
        return $this->last_synced_at->diffInHours(now()) > 24;
    }

    // === Static ===
    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)->first();
    }

    public static function findByWpPostId(int $wpPostId): ?self
    {
        return self::where('wp_post_id', $wpPostId)->first();
    }
}
