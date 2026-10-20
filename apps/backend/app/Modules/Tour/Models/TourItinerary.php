<?php

namespace App\Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourItinerary extends Model
{
    protected $fillable = [
        'tour_id',
        'day_number',
        'title',
        'description',
        'has_breakfast',
        'has_lunch',
        'has_dinner',
        'accommodation_name',
        'accommodation_type',
        'activities',
        'transport_info',
        'location_name',
        'latitude',
        'longitude',
        'image_url',
        'sort_order',
    ];

    protected $casts = [
        'day_number' => 'integer',
        'has_breakfast' => 'boolean',
        'has_lunch' => 'boolean',
        'has_dinner' => 'boolean',
        'activities' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'sort_order' => 'integer',
    ];

    // === Relationships ===

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    // === Scopes ===

    public function scopeOrdered($query)
    {
        return $query->orderBy('day_number');
    }

    // === Methods ===

    public function getMealsAttribute(): array
    {
        $meals = [];
        if ($this->has_breakfast) $meals[] = 'صبحانه';
        if ($this->has_lunch) $meals[] = 'ناهار';
        if ($this->has_dinner) $meals[] = 'شام';
        return $meals;
    }

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
}
