<?php

namespace App\Modules\Lead\Models;

use App\Models\Hotel;
use App\Models\User;
use App\Modules\Tour\Models\Tour;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'full_name',
        'phone',
        'email',
        'national_id',
        'message',
        'source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'landing_page',
        'user_agent',
        'ip_address',
        'tour_id',
        'hotel_id',
        'destination',
        'preferred_date',
        'return_date',
        'adults',
        'children',
        'infants',
        'budget',
        'preferred_contact_time',
        'status',
        'priority',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'next_follow_up_at',
        'user_id',
        'converted_booking_id',
        'converted_at',
        'duplicate_of',
        'is_duplicate',
        'first_contacted_at',
        'last_contacted_at',
        'contact_attempts',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'return_date' => 'date',
        'adults' => 'integer',
        'children' => 'integer',
        'infants' => 'integer',
        'budget' => 'integer',
        'assigned_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'converted_at' => 'datetime',
        'is_duplicate' => 'boolean',
        'first_contacted_at' => 'datetime',
        'last_contacted_at' => 'datetime',
        'contact_attempts' => 'integer',
    ];

    // === Constants ===
    public const TYPE_TOUR = 'tour_request';
    public const TYPE_HOTEL = 'hotel_request';
    public const TYPE_VISA = 'visa_request';
    public const TYPE_CONSULTATION = 'consultation';
    public const TYPE_CALL = 'call_request';
    public const TYPE_PRICE = 'price_inquiry';

    public const ALL_TYPES = [
        self::TYPE_TOUR,
        self::TYPE_HOTEL,
        self::TYPE_VISA,
        self::TYPE_CONSULTATION,
        self::TYPE_CALL,
        self::TYPE_PRICE,
    ];

    public const STATUS_NEW = 'new';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_WAITING = 'waiting_response';
    public const STATUS_PHONE_BOOKED = 'phone_booked';
    public const STATUS_CONVERTED = 'converted_to_booking';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_RESPONSE = 'no_response';
    public const STATUS_DUPLICATE = 'duplicate';
    public const STATUS_CLOSED = 'closed';

    public const ALL_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_REVIEWING,
        self::STATUS_CONTACTED,
        self::STATUS_WAITING,
        self::STATUS_PHONE_BOOKED,
        self::STATUS_CONVERTED,
        self::STATUS_CANCELLED,
        self::STATUS_NO_RESPONSE,
        self::STATUS_DUPLICATE,
        self::STATUS_CLOSED,
    ];

    public const OPEN_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_REVIEWING,
        self::STATUS_CONTACTED,
        self::STATUS_WAITING,
    ];

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    // === Boot ===
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Lead $lead) {
            if (empty($lead->code)) {
                $lead->code = self::generateUniqueCode();
            }
            if (empty($lead->status)) {
                $lead->status = self::STATUS_NEW;
            }
            if (empty($lead->priority)) {
                $lead->priority = self::PRIORITY_NORMAL;
            }
            if (empty($lead->adults) && $lead->adults !== 0) {
                $lead->adults = 1;
            }
        });
    }

    // === Relationships ===
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(LeadStatusHistory::class)->orderByDesc('changed_at');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->orderByDesc('is_pinned')->orderByDesc('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LeadAttachment::class);
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'duplicate_of');
    }

    public function duplicates(): HasMany
    {
        return $this->hasMany(Lead::class, 'duplicate_of');
    }

    // === Scopes ===
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', self::OPEN_STATUSES);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereNotIn('status', self::OPEN_STATUSES);
    }

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeDueForFollowUp(Builder $query): Builder
    {
        return $query->whereIn('status', self::OPEN_STATUSES)
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now());
    }

    public function scopeHighPriority(Builder $query): Builder
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('full_name', 'ilike', "%{$term}%")
                ->orWhere('phone', 'ilike', "%{$term}%")
                ->orWhere('email', 'ilike', "%{$term}%")
                ->orWhere('code', 'ilike', "%{$term}%")
                ->orWhere('destination', 'ilike', "%{$term}%");
        });
    }

    public function scopeNotDuplicate(Builder $query): Builder
    {
        return $query->where('is_duplicate', false);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    // === Methods ===
    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES);
    }

    public function isConverted(): bool
    {
        return $this->status === self::STATUS_CONVERTED;
    }

    public function recordContact(): void
    {
        $this->contact_attempts++;
        if (!$this->first_contacted_at) {
            $this->first_contacted_at = now();
        }
        $this->last_contacted_at = now();
        $this->save();
    }

    public function scheduleFollowUp($dateTime): void
    {
        $this->next_follow_up_at = $dateTime;
        $this->save();
    }

    public function needsFollowUp(): bool
    {
        return $this->isOpen()
            && $this->next_follow_up_at
            && $this->next_follow_up_at->isPast();
    }

    public function getAgeInDays(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_TOUR => 'درخواست تور',
            self::TYPE_HOTEL => 'درخواست هتل',
            self::TYPE_VISA => 'درخواست ویزا',
            self::TYPE_CONSULTATION => 'مشاوره',
            self::TYPE_CALL => 'درخواست تماس',
            self::TYPE_PRICE => 'استعلام قیمت',
            default => 'نامشخص',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => 'جدید',
            self::STATUS_REVIEWING => 'در حال بررسی',
            self::STATUS_CONTACTED => 'تماس گرفته شد',
            self::STATUS_WAITING => 'در انتظار پاسخ',
            self::STATUS_PHONE_BOOKED => 'رزرو تلفنی',
            self::STATUS_CONVERTED => 'تبدیل به سفارش',
            self::STATUS_CANCELLED => 'لغو شده',
            self::STATUS_NO_RESPONSE => 'بی‌پاسخ',
            self::STATUS_DUPLICATE => 'تکراری',
            self::STATUS_CLOSED => 'بسته شده',
            default => 'نامشخص',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => 'blue',
            self::STATUS_REVIEWING => 'indigo',
            self::STATUS_CONTACTED => 'purple',
            self::STATUS_WAITING => 'yellow',
            self::STATUS_PHONE_BOOKED => 'green',
            self::STATUS_CONVERTED => 'emerald',
            self::STATUS_CANCELLED => 'gray',
            self::STATUS_NO_RESPONSE => 'orange',
            self::STATUS_DUPLICATE => 'gray',
            self::STATUS_CLOSED => 'slate',
            default => 'gray',
        };
    }

    // === Static Methods ===
    public static function generateUniqueCode(): string
    {
        do {
            $code = 'LD-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public static function findDuplicates(string $phone, ?int $excludeId = null)
    {
        return self::where('phone', $phone)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where('created_at', '>=', now()->subDays(30))
            ->where('is_duplicate', false)
            ->get();
    }
}
