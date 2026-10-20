<?php

namespace App\Modules\Visa\Models;

use App\Modules\Visa\Enums\VisaStatusForIranians;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name', 'name_en', 'code', 'code3', 'phone_code',
        'continent', 'capital', 'currency', 'timezone',
        'flag_emoji', 'flag_image', 'slug',
        'visa_status_for_iranians',
        'seo_title', 'seo_description',
        'is_active', 'is_popular', 'sort_order',
    ];

    protected $casts = [
        'visa_status_for_iranians' => VisaStatusForIranians::class,
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'sort_order' => 'integer',
    ];

    // === Relationships ===
    public function visaTypes(): HasMany
    {
        return $this->hasMany(VisaType::class);
    }

    // === Scopes ===
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeWithVisaRequired($query)
    {
        return $query->where('visa_status_for_iranians', VisaStatusForIranians::REQUIRED);
    }

    // === Accessors ===
    public function getVisaStatusLabelAttribute(): string
    {
        return $this->visa_status_for_iranians?->label() ?? 'نامشخص';
    }

    public function getDisplayNameAttribute(): string
    {
        return ($this->flag_emoji ? $this->flag_emoji . ' ' : '') . $this->name;
    }

    // === Static ===
    public static function findByCode(string $code): ?self
    {
        return self::where('code', strtoupper($code))->first();
    }

    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)->first();
    }
}
