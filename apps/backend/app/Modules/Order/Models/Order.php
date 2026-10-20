<?php

namespace App\Modules\Order\Models;

use App\Models\User;
use App\Modules\Lead\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'user_id',
        'lead_id',
        'source',
        'status',
        'priority',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'currency',
        'coupon_code',
        'coupon_id',
        'adults_count',
        'children_count',
        'infants_count',
        'paid_at',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
        'assigned_to',
        'metadata',
        'admin_notes',
        'customer_notes',
        'ip_address',
        'user_agent',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'discount_amount' => 'integer',
        'tax_amount' => 'integer',
        'total_amount' => 'integer',
        'adults_count' => 'integer',
        'children_count' => 'integer',
        'infants_count' => 'integer',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // === Type Constants ===
    public const TYPE_TOUR = 'tour';
    public const TYPE_HOTEL = 'hotel';
    public const TYPE_VISA = 'visa';
    public const TYPE_MIXED = 'mixed';

    public const ALL_TYPES = [
        self::TYPE_TOUR,
        self::TYPE_HOTEL,
        self::TYPE_VISA,
        self::TYPE_MIXED,
    ];

    // === Status Constants ===
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID = 'paid';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUND_REQUESTED = 'refund_requested';
    public const STATUS_REFUNDED = 'refunded';

    public const ALL_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PENDING_PAYMENT,
        self::STATUS_PAID,
        self::STATUS_PROCESSING,
        self::STATUS_CONFIRMED,
        self::STATUS_FAILED,
        self::STATUS_PENDING_REVIEW,
        self::STATUS_CANCELLED,
        self::STATUS_REFUND_REQUESTED,
        self::STATUS_REFUNDED,
    ];

    // === Status Groups ===
    public const ACTIVE_STATUSES = [
        self::STATUS_PENDING_PAYMENT,
        self::STATUS_PAID,
        self::STATUS_PROCESSING,
        self::STATUS_CONFIRMED,
        self::STATUS_PENDING_REVIEW,
    ];

    public const TERMINAL_STATUSES = [
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
        self::STATUS_REFUNDED,
    ];

    public const PAID_STATUSES = [
        self::STATUS_PAID,
        self::STATUS_PROCESSING,
        self::STATUS_CONFIRMED,
        self::STATUS_REFUND_REQUESTED,
        self::STATUS_REFUNDED,
    ];

    // === Valid Status Transitions ===
    public const VALID_TRANSITIONS = [
        self::STATUS_DRAFT => [self::STATUS_PENDING_PAYMENT, self::STATUS_CANCELLED],
        self::STATUS_PENDING_PAYMENT => [self::STATUS_PAID, self::STATUS_FAILED, self::STATUS_CANCELLED],
        self::STATUS_PAID => [self::STATUS_PROCESSING, self::STATUS_CONFIRMED, self::STATUS_REFUND_REQUESTED, self::STATUS_CANCELLED],
        self::STATUS_PROCESSING => [self::STATUS_CONFIRMED, self::STATUS_REFUND_REQUESTED, self::STATUS_CANCELLED],
        self::STATUS_CONFIRMED => [self::STATUS_REFUND_REQUESTED],
        self::STATUS_PENDING_REVIEW => [self::STATUS_PENDING_PAYMENT, self::STATUS_CANCELLED],
        self::STATUS_REFUND_REQUESTED => [self::STATUS_REFUNDED, self::STATUS_CONFIRMED],
        self::STATUS_FAILED => [self::STATUS_PENDING_PAYMENT],
    ];

    // === Boot ===
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order) {
            if (empty($order->code)) {
                $order->code = self::generateUniqueCode();
            }
        });
    }

    // === Relationships ===
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(OrderPassenger::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderByDesc('changed_at');
    }

    public function payments(): HasMany
    {
        // در فاز بعدی با ماژول Payment
        return $this->hasMany(\App\Modules\Payment\Models\Payment::class);
    }

    // === Scopes ===
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeTerminal(Builder $query): Builder
    {
        return $query->whereIn('status', self::TERMINAL_STATUSES);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->whereIn('status', self::PAID_STATUSES);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeFromLead(Builder $query, int $leadId): Builder
    {
        return $query->where('lead_id', $leadId);
    }

    public function scopePendingPayment(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING_PAYMENT);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('code', 'ilike', "%{$term}%")
                ->orWhereHas('user', function ($q) use ($term) {
                    $q->where('name', 'ilike', "%{$term}%")
                        ->orWhere('phone', 'ilike', "%{$term}%")
                        ->orWhere('email', 'ilike', "%{$term}%");
                })
                ->orWhereHas('passengers', function ($q) use ($term) {
                    $q->where('first_name', 'ilike', "%{$term}%")
                        ->orWhere('last_name', 'ilike', "%{$term}%")
                        ->orWhere('national_id', 'ilike', "%{$term}%");
                });
        });
    }

    public function scopeDateRange(Builder $query, $from, $to): Builder
    {
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);
        return $query;
    }

    // === Methods ===

    /**
     * آیا می‌توان به این وضعیت منتقل شد؟
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
     * سفارش پرداخت شده است؟
     */
    public function isPaid(): bool
    {
        return in_array($this->status, self::PAID_STATUSES);
    }

    /**
     * سفارش فعال است؟
     */
    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES);
    }

    /**
     * سفارش در وضعیت پایانی است؟
     */
    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL_STATUSES);
    }

    /**
     * محاسبه مجدد مبلغ کل
     */
    public function recalculateTotals(): self
    {
        $this->subtotal = $this->items()->sum('total_price');
        $this->total_amount = $this->subtotal - $this->discount_amount + $this->tax_amount;
        $this->save();

        return $this;
    }

    /**
     * تعداد کل مسافران
     */
    public function getTotalPassengersCountAttribute(): int
    {
        return $this->adults_count + $this->children_count + $this->infants_count;
    }

    /**
     * فرمت نمایش قیمت
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount) . ' ' . ($this->currency === 'IRR' ? 'ریال' : $this->currency);
    }

    /**
     * برچسب فارسی وضعیت
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'پیش‌نویس',
            self::STATUS_PENDING_PAYMENT => 'در انتظار پرداخت',
            self::STATUS_PAID => 'پرداخت شده',
            self::STATUS_PROCESSING => 'در حال صدور',
            self::STATUS_CONFIRMED => 'صادر شده',
            self::STATUS_FAILED => 'ناموفق',
            self::STATUS_PENDING_REVIEW => 'نیازمند بررسی',
            self::STATUS_CANCELLED => 'لغو شده',
            self::STATUS_REFUND_REQUESTED => 'درخواست استرداد',
            self::STATUS_REFUNDED => 'استرداد شده',
            default => 'نامشخص',
        };
    }

    /**
     * رنگ وضعیت برای UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_PENDING_PAYMENT => 'yellow',
            self::STATUS_PAID => 'blue',
            self::STATUS_PROCESSING => 'indigo',
            self::STATUS_CONFIRMED => 'green',
            self::STATUS_FAILED => 'red',
            self::STATUS_PENDING_REVIEW => 'orange',
            self::STATUS_CANCELLED => 'slate',
            self::STATUS_REFUND_REQUESTED => 'amber',
            self::STATUS_REFUNDED => 'purple',
            default => 'gray',
        };
    }

    /**
     * برچسب فارسی نوع سفارش
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_TOUR => 'تور',
            self::TYPE_HOTEL => 'هتل',
            self::TYPE_VISA => 'ویزا',
            self::TYPE_MIXED => 'ترکیبی',
            default => 'نامشخص',
        };
    }

    // === Static Methods ===

    /**
     * Generate unique code: NS-YYMMDD-XXXX
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = 'NS-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Find by code
     */
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }
}
