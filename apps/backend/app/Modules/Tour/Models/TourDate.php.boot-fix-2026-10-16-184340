<?php

namespace App\Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourDate extends Model
{
    protected $fillable = [
        'tour_id',
        'departure_date',
        'return_date',
        'total_capacity',
        'remaining_capacity',
        'reserved_count',
        'min_participants',
        'adult_price',
        'child_price',
        'infant_price',
        'single_supplement',
        'discount_amount',
        'discount_percent',
        'discount_valid_until',
        'status',
        'is_guaranteed',
        'notes',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'discount_valid_until' => 'datetime',
        'total_capacity' => 'integer',
        'remaining_capacity' => 'integer',
        'reserved_count' => 'integer',
        'min_participants' => 'integer',
        'adult_price' => 'integer',
        'child_price' => 'integer',
        'infant_price' => 'integer',
        'single_supplement' => 'integer',
        'discount_amount' => 'integer',
        'discount_percent' => 'integer',
        'is_guaranteed' => 'boolean',
    ];

    // === Constants ===
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_LIMITED = 'limited';
    public const STATUS_SOLD_OUT = 'sold_out';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';

    // === Relationships ===

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    // === Scopes ===

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', [self::STATUS_AVAILABLE, self::STATUS_LIMITED])
            ->where('departure_date', '>=', now()->toDateString());
    }

    public function scopeFuture($query)
    {
        return $query->where('departure_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('departure_date', '<', now()->toDateString());
    }

    public function scopeGuaranteed($query)
    {
        return $query->where('is_guaranteed', true);
    }

    public function scopeBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('departure_date', [$startDate, $endDate]);
    }

    public function scopePriceBetween($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('adult_price', [$minPrice, $maxPrice]);
    }

    // === Business Methods ===

    /**
     * Check if this date is available for booking
     */
    public function isAvailable(): bool
    {
        return in_array($this->status, [self::STATUS_AVAILABLE, self::STATUS_LIMITED])
            && $this->remaining_capacity > 0
            && $this->departure_date->isFuture();
    }

    /**
     * Check if this date has discount active
     */
    public function hasActiveDiscount(): bool
    {
        if ($this->discount_amount <= 0 && $this->discount_percent <= 0) {
            return false;
        }

        if ($this->discount_valid_until && $this->discount_valid_until->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Calculate final price for adult with discount
     */
    public function getFinalAdultPrice(): int
    {
        return $this->applyDiscount($this->adult_price);
    }

    /**
     * Calculate final price for child with discount
     */
    public function getFinalChildPrice(): int
    {
        return $this->applyDiscount($this->child_price);
    }

    /**
     * Calculate final price for infant with discount
     */
    public function getFinalInfantPrice(): int
    {
        return $this->applyDiscount($this->infant_price);
    }

    /**
     * Apply discount to a price
     */
    protected function applyDiscount(int $basePrice): int
    {
        if (!$this->hasActiveDiscount()) {
            return $basePrice;
        }

        $discounted = $basePrice;

        if ($this->discount_percent > 0) {
            $discounted = $basePrice - ($basePrice * $this->discount_percent / 100);
        } elseif ($this->discount_amount > 0) {
            $discounted = $basePrice - $this->discount_amount;
        }

        return max(0, (int) $discounted);
    }

    /**
     * Reserve capacity (called when creating booking)
     */
    public function reserveCapacity(int $count): bool
    {
        // Initialize reserved_count if null
        if ($this->reserved_count === null) {
            $this->reserved_count = 0;
        }
        
        if ($this->remaining_capacity < $count) {
            return false;
        }

        $this->reserved_count = $this->reserved_count + $count;
        $this->remaining_capacity = $this->total_capacity - $this->reserved_count;

        // Update status if needed
        if ($this->remaining_capacity === 0) {
            $this->status = self::STATUS_SOLD_OUT;
        } elseif ($this->remaining_capacity <= 3) {
            $this->status = self::STATUS_LIMITED;
        }

        return $this->save();
    }

    /**
     * Release capacity (called when cancelling booking)
     */
    public function releaseCapacity(int $count): bool
    {
        $this->reserved_count = max(0, $this->reserved_count - $count);
        $this->remaining_capacity = min($this->total_capacity, $this->remaining_capacity + $count);

        // Update status
        if ($this->status === self::STATUS_SOLD_OUT && $this->remaining_capacity > 0) {
            $this->status = $this->remaining_capacity <= 3 ? self::STATUS_LIMITED : self::STATUS_AVAILABLE;
        }

        return $this->save();
    }

    /**
     * Get capacity usage percentage
     */
    public function getCapacityUsagePercent(): float
    {
        if ($this->total_capacity <= 0) {
            return 0;
        }
        return round(($this->reserved_count / $this->total_capacity) * 100, 1);
    }

    /**
     * Get duration in days
     */
    public function getDurationDays(): int
    {
        return $this->departure_date->diffInDays($this->return_date) + 1;
    }

    /**
     * Get duration in nights
     */
    public function getDurationNights(): int
    {
        return $this->departure_date->diffInDays($this->return_date);
    }

    /**
     * Check if date is in the past
     */
    public function isPast(): bool
    {
        return $this->departure_date->isPast();
    }

    /**
     * Check if departure is within X days
     */
    public function isDepartingSoon(int $days = 7): bool
    {
        return $this->departure_date->diffInDays(now()) <= $days && $this->departure_date->isFuture();
    }

    // === Accessors ===

    public function getMinPriceAttribute(): int
    {
        return $this->getFinalAdultPrice();
    }

    public function getFormattedDepartureDateAttribute(): string
    {
        // TODO: Format in Persian calendar (Jalali)
        return $this->departure_date->format('Y-m-d');
    }
}
