<?php

namespace App\Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TourDestination extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'country',
        'city',
        'description',
        'latitude',
        'longitude',
        'image_url',
        'is_popular',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // === Relationships ===

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'tour_destination_map')
            ->withPivot('day_number', 'sort_order')
            ->withTimestamps();
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

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    // === Methods ===

    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ];
        }
        return null;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->city) {
            return "{$this->city}، {$this->country}";
        }
        return $this->country;
    }
}
