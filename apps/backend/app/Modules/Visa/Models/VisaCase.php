<?php

namespace App\Modules\Visa\Models;

use App\Models\User;
use App\Modules\Lead\Models\Lead;
use App\Modules\Order\Models\Order;
use App\Modules\Visa\Enums\VisaCaseStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * پرونده ویزا (قلب ماژول ویزا)
 * 
 * طبق بخش ۱۶.۲ سند، ۱۴ وضعیت دارد و قلب فرآیند ویزا است.
 */
class VisaCase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'uuid', 'user_id', 'visa_type_id', 'order_id', 'lead_id',
        'status', 'priority',
        'intended_travel_date', 'visa_issue_date', 'visa_expiry_date',
        'appointment_datetime', 'appointment_location', 'appointment_reference',
        'passport_number', 'passport_issuer', 'passport_issue_date', 'passport_expiry_date',
        'embassy_tracking_number', 'visa_sticker_number',
        'assigned_to', 'assigned_by', 'assigned_at', 'next_follow_up_at',
        'service_fee', 'embassy_fee', 'total_amount', 'paid_amount', 'payment_status',
        'total_checklist_items', 'uploaded_documents_count', 'approved_documents_count',
        'documents_completion_percentage',
        'sla_days', 'elapsed_days', 'is_overdue',
        'documents_completed_at', 'submitted_to_embassy_at', 'biometrics_completed_at',
        'result_received_at', 'completed_at', 'cancelled_at', 'cancellation_reason',
        'internal_notes', 'rejection_reason', 'correction_notes',
    ];

    protected $casts = [
        'status' => VisaCaseStatus::class,
        'intended_travel_date' => 'date',
        'visa_issue_date' => 'date',
        'visa_expiry_date' => 'date',
        'appointment_datetime' => 'datetime',
        'passport_issue_date' => 'date',
        'passport_expiry_date' => 'date',
        'assigned_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'service_fee' => 'integer',
        'embassy_fee' => 'integer',
        'total_amount' => 'integer',
        'paid_amount' => 'integer',
        'total_checklist_items' => 'integer',
        'uploaded_documents_count' => 'integer',
        'approved_documents_count' => 'integer',
        'documents_completion_percentage' => 'integer',
        'sla_days' => 'integer',
        'elapsed_days' => 'integer',
        'is_overdue' => 'boolean',
        'documents_completed_at' => 'datetime',
        'submitted_to_embassy_at' => 'datetime',
        'biometrics_completed_at' => 'datetime',
        'result_received_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // === Boot ===
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (VisaCase $case) {
            if (empty($case->code)) {
                $case->code = self::generateUniqueCode();
            }
            if (empty($case->uuid)) {
                $case->uuid = (string) Str::uuid();
            }
        });
    }

    // === Relationships ===
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visaType(): BelongsTo
    {
        return $this->belongsTo(VisaType::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(VisaCasePassenger::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VisaCaseDocument::class);
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(VisaCaseTimeline::class)->orderByDesc('occurred_at');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(VisaCaseNote::class)
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');
    }

    // === Scopes ===
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', array_map(fn($s) => $s->value, VisaCaseStatus::activeStatuses()));
    }

    public function scopeTerminal(Builder $query): Builder
    {
        return $query->whereIn('status', array_map(fn($s) => $s->value, VisaCaseStatus::terminalStatuses()));
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->whereIn('status', array_map(fn($s) => $s->value, VisaCaseStatus::successfulStatuses()));
    }

    public function scopeRequiresAction(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('status', VisaCaseStatus::DOCUMENTS_INCOMPLETE)
                ->orWhere('status', VisaCaseStatus::NEEDS_APPOINTMENT)
                ->orWhere('status', VisaCaseStatus::NEEDS_BIOMETRICS)
                ->orWhere('status', VisaCaseStatus::NEEDS_CORRECTION);
        });
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('is_overdue', true);
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
        return $query->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now());
    }

    public function scopeByStatus(Builder $query, VisaCaseStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCountry(Builder $query, int $countryId): Builder
    {
        return $query->whereHas('visaType', fn($q) => $q->where('country_id', $countryId));
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('code', 'ilike', "%{$term}%")
                ->orWhere('passport_number', 'ilike', "%{$term}%")
                ->orWhere('embassy_tracking_number', 'ilike', "%{$term}%")
                ->orWhereHas('user', fn($q) => $q->where('name', 'ilike', "%{$term}%")
                    ->orWhere('phone', 'ilike', "%{$term}%"))
                ->orWhereHas('passengers', fn($q) => $q->where('first_name', 'ilike', "%{$term}%")
                    ->orWhere('last_name', 'ilike', "%{$term}%")
                    ->orWhere('passport_number', 'ilike', "%{$term}%"));
        });
    }

    // === Methods ===

    /**
     * آیا می‌توان به وضعیت جدید منتقل شد؟
     */
    public function canTransitionTo(VisaCaseStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    /**
     * آیا پرونده فعال است؟
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * آیا پرونده در وضعیت پایانی است؟
     */
    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    /**
     * آیا پرونده موفق بوده؟
     */
    public function isSuccessful(): bool
    {
        return $this->status->isSuccessful();
    }

    /**
     * آیا پرونده نیاز به اقدام فوری دارد؟
     */
    public function requiresAction(): bool
    {
        return $this->status->requiresAction();
    }

    /**
     * محاسبه درصد تکمیل مدارک
     */
    public function calculateDocumentsCompletion(): int
    {
        if ($this->total_checklist_items === 0) {
            return 0;
        }

        return (int) round(($this->approved_documents_count / $this->total_checklist_items) * 100);
    }

    /**
     * آیا مدارک کامل است؟
     */
    public function isDocumentsComplete(): bool
    {
        return $this->documents_completion_percentage >= 100;
    }

    /**
     * روزهای سپری شده از شروع پرونده
     */
    public function calculateElapsedDays(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * آیا SLA گذشته است؟
     */
    public function isSlaOverdue(): bool
    {
        if (!$this->sla_days) {
            return false;
        }
        return $this->calculateElapsedDays() > $this->sla_days;
    }

    // === Accessors ===

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getStatusIconAttribute(): string
    {
        return $this->status->icon();
    }

    public function getDisplayNameAttribute(): string
    {
        $country = $this->visaType?->country?->name ?? '';
        $type = $this->visaType?->category?->label() ?? '';
        return trim("ویزا {$country} {$type} - {$this->code}");
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format($this->total_amount) . ' ریال';
    }

    public function getDocumentsCompletionLabelAttribute(): string
    {
        return "{$this->documents_completion_percentage}٪";
    }

    // === Static ===

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'VIS-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    public static function findByUuid(string $uuid): ?self
    {
        return self::where('uuid', $uuid)->first();
    }
}
