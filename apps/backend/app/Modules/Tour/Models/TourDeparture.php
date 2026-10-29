<?php

namespace App\Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourDeparture extends Model
{
    protected $table = 'tour_departures';

    protected $fillable = [
        'tour_id',
        'departure_date',
        'return_date',
        'total_capacity',
        'available_seats',
        'price_adult',
        'price_child',
        'price_infant',
        'price_single',
        'status',
        'notes',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'total_capacity' => 'integer',
        'available_seats' => 'integer',
        'price_adult' => 'integer',
        'price_child' => 'integer',
        'price_infant' => 'integer',
        'price_single' => 'integer',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && $this->available_seats > 0;
    }

    public function isFull(): bool
    {
        return $this->available_seats <= 0;
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
            ->where('available_seats', '>', 0);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('departure_date', '>=', now()->toDateString());
    }
}
