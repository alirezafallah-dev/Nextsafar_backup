<?php

namespace App\Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPassenger extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'type',
        'first_name',
        'last_name',
        'first_name_en',
        'last_name_en',
        'national_id',
        'passport_number',
        'birth_date',
        'gender',
        'nationality',
        'phone',
        'email',
        'relation_to_owner',
        'special_requests',
        'price',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'price' => 'integer',
    ];

    public const TYPE_ADULT = 'adult';
    public const TYPE_CHILD = 'child';
    public const TYPE_INFANT = 'infant';

    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';
    public const GENDER_OTHER = 'other';

    // === Relationships ===
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    // === Methods ===

    /**
     * نام کامل فارسی
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * نام کامل لاتین (برای بلیط)
     */
    public function getFullNameEnAttribute(): string
    {
        return trim("{$this->first_name_en} {$this->last_name_en}");
    }

    /**
     * سن مسافر (سال)
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }

    /**
     * برچسب فارسی نوع مسافر
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ADULT => 'بزرگسال',
            self::TYPE_CHILD => 'کودک',
            self::TYPE_INFANT => 'نوزاد',
            default => 'نامشخص',
        };
    }

    /**
     * برچسب فارسی جنسیت
     */
    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            self::GENDER_MALE => 'مرد',
            self::GENDER_FEMALE => 'زن',
            self::GENDER_OTHER => 'سایر',
            default => 'نامشخص',
        };
    }
}
