<?php

namespace App\Modules\Order\Models;

use App\Models\Hotel;
use App\Modules\Tour\Models\Tour;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_type',
        'item_id',
        'title',
        'description',
        'code',
        'slug',
        'service_date',
        'service_end_date',
        'destination',
        'quantity',
        'unit_price',
        'discount_amount',
        'total_price',
        'currency',
        'extra_data',
        'status',
    ];

    protected $casts = [
        'service_date' => 'date',
        'service_end_date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'discount_amount' => 'integer',
        'total_price' => 'integer',
        'extra_data' => 'array',
    ];

    // === Constants ===
    public const ITEM_TOUR = 'tour';
    public const ITEM_HOTEL = 'hotel';
    public const ITEM_VISA = 'visa';
    public const ITEM_SERVICE = 'service';

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    // === Relationships ===
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo('item', 'item_type', 'item_id');
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(OrderPassenger::class);
    }

    // === Scopes ===
    public function scopeOfType($query, string $type)
    {
        return $query->where('item_type', $type);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    // === Methods ===

    /**
     * محاسبه مجدد قیمت کل
     */
    public function recalculateTotal(): self
    {
        $this->total_price = ($this->unit_price * $this->quantity) - $this->discount_amount;
        $this->save();
        return $this;
    }

    /**
     * دریافت اطلاعات مرتبط (shortcut)
     */
    public function getRelatedModel(): ?Model
    {
        return match ($this->item_type) {
            self::ITEM_TOUR => Tour::find($this->item_id),
            self::ITEM_HOTEL => Hotel::find($this->item_id),
            // self::ITEM_VISA => Visa::find($this->item_id), // در فاز بعدی
            default => null,
        };
    }

    /**
     * برچسب فارسی نوع آیتم
     */
    public function getItemTypeLabelAttribute(): string
    {
        return match ($this->item_type) {
            self::ITEM_TOUR => 'تور',
            self::ITEM_HOTEL => 'هتل',
            self::ITEM_VISA => 'ویزا',
            self::ITEM_SERVICE => 'خدمات',
            default => 'نامشخص',
        };
    }
}
