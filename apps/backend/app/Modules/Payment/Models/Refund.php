<?php

namespace App\Modules\Payment\Models;

use App\Models\User;
use App\Modules\Booking\Models\Booking;
use App\Modules\Order\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Refund Model
 * 
 * طبق بخش ۱۵ سند، ۷ وضعیت دارد:
 * - registered: ثبت شده
 * - reviewing: در حال بررسی
 * - approved: تأیید شده
 * - rejected: رد شده
 * - processing: در حال بازگشت وجه
 * - completed: انجام شد
 * - cancelled: لغو شده
 */
class Refund extends Model
{
    protected $fillable = [
        'code', 'payment_id', 'booking_id', 'order_id', 'user_id',
        'type', 'amount', 'original_amount', 'penalty_amount', 'refund_amount',
        'currency', 'reason', 'penalty_reason', 'status', 'admin_notes',
        'processed_by', 'processed_at', 'approved_by', 'approved_at',
        'rejected_by', 'rejected_at', 'rejection_reason', 'requested_at',
        'gateway_ref_id', 'metadata',
    ];

    protected $casts = [
        'amount' => 'integer',
        'original_amount' => 'integer',
        'penalty_amount' => 'integer',
        'refund_amount' => 'integer',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'requested_at' => 'datetime',
        'metadata' => 'array',
    ];

    // === Constants - Status (7 statuses per spec 15.2) ===
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const ALL_STATUSES = [
        self::STATUS_REGISTERED,
        self::STATUS_REVIEWING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_PROCESSING,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    public const PENDING_STATUSES = [
        self::STATUS_REGISTERED,
        self::STATUS_REVIEWING,
    ];

    public const ACTIVE_STATUSES = [
        self::STATUS_REGISTERED,
        self::STATUS_REVIEWING,
        self::STATUS_APPROVED,
        self::STATUS_PROCESSING,
    ];

    public const TERMINAL_STATUSES = [
        self::STATUS_REJECTED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    // === Valid Transitions ===
    public const VALID_TRANSITIONS = [
        self::STATUS_REGISTERED => [self::STATUS_REVIEWING, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_CANCELLED],
        self::STATUS_REVIEWING => [self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_CANCELLED],
        self::STATUS_APPROVED => [self::STATUS_PROCESSING, self::STATUS_CANCELLED],
        self::STATUS_PROCESSING => [self::STATUS_COMPLETED],
        self::STATUS_REJECTED => [],
        self::STATUS_COMPLETED => [],
        self::STATUS_CANCELLED => [],
    ];

    // === Type Constants ===
    public const TYPE_FULL = 'full';
    public const TYPE_PARTIAL = 'partial';
    public const TYPE_PENALTY = 'penalty_deduction';

    // === Boot ===
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Refund $refund) {
            if (empty($refund->code)) {
                $refund->code = self::generateUniqueCode();
            }
            if (empty($refund->requested_at)) {
                $refund->requested_at = now();
            }
            if (empty($refund->status)) {
                $refund->status = self::STATUS_REGISTERED;
            }
        });
    }

    // === Relationships ===
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // === Scopes ===
    public function scopePending($query)
    {
        return $query->whereIn('status', self::PENDING_STATUSES);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForOrder($query, int $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    // === Methods ===

    /**
     * آیا این استرداد در وضعیت پایانی است؟
     */
    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL_STATUSES);
    }

    /**
     * آیا این استرداد تکمیل شده؟
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * آیا این استرداد در انتظار است؟
     */
    public function isPending(): bool
    {
        return in_array($this->status, self::PENDING_STATUSES);
    }

    /**
     * آیا این استرداد تأیید شده؟
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * آیا این استرداد رد شده؟
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * آیا انتقال به وضعیت جدید مجاز است؟
     */
    public function canTransitionTo(string $newStatus): bool
    {
        if ($this->status === $newStatus) {
            return true;
        }

        $allowed = self::VALID_TRANSITIONS[$this->status] ?? [];
        return in_array($newStatus, $allowed);
    }

    /**
     * محاسبه درصد جریمه
     */
    public function getPenaltyPercentageAttribute(): float
    {
        if (!$this->original_amount || $this->original_amount == 0) {
            return 0;
        }
        return round(($this->penalty_amount / $this->original_amount) * 100, 2);
    }

    /**
     * دریافت مرتبط (order یا booking)
     */
    public function getRelated(): ?Model
    {
        return $this->order ?? $this->booking;
    }

    // === Accessors - Labels ===

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_REGISTERED => 'ثبت شده',
            self::STATUS_REVIEWING => 'در حال بررسی',
            self::STATUS_APPROVED => 'تأیید شده',
            self::STATUS_REJECTED => 'رد شده',
            self::STATUS_PROCESSING => 'در حال بازگشت وجه',
            self::STATUS_COMPLETED => 'انجام شد',
            self::STATUS_CANCELLED => 'لغو شده',
            default => 'نامشخص',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_REGISTERED => 'blue',
            self::STATUS_REVIEWING => 'yellow',
            self::STATUS_APPROVED => 'indigo',
            self::STATUS_REJECTED => 'red',
            self::STATUS_PROCESSING => 'orange',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'gray',
            default => 'gray',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_FULL => 'استرداد کامل',
            self::TYPE_PARTIAL => 'استرداد جزئی',
            self::TYPE_PENALTY => 'با کسر جریمه',
            default => 'نامشخص',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount) . ' ' . $this->currency;
    }

    public function getFormattedRefundAmountAttribute(): string
    {
        return number_format($this->refund_amount ?? 0) . ' ' . $this->currency;
    }

    public function getFormattedPenaltyAmountAttribute(): string
    {
        return number_format($this->penalty_amount) . ' ' . $this->currency;
    }

    // === Static Methods ===

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'REF-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }
}
