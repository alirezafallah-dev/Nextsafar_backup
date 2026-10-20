<?php

namespace App\Modules\Payment\Models;

use App\Models\User;
use App\Modules\Booking\Models\Booking;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Enums\PaymentGateway;
use App\Modules\Payment\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'code', 'booking_id', 'order_id', 'user_id', 'gateway', 'amount', 'currency',
        'status', 'authority_code', 'transaction_id', 'reference_id', 'card_number',
        'card_hash', 'ip_address', 'user_agent', 'gateway_request', 'gateway_response',
        'callback_data', 'description', 'paid_at', 'idempotency_key', 'gateway_signature',
        'verification_status', 'verified_amount', 'verification_attempts', 'initiated_at',
        'verified_at', 'verification_log',
    ];

    protected $casts = [
        'gateway' => PaymentGateway::class,
        'status' => PaymentStatus::class,
        'amount' => 'decimal:2',
        'verified_amount' => 'integer',
        'gateway_request' => 'array',
        'gateway_response' => 'array',
        'callback_data' => 'array',
        'verification_log' => 'array',
        'paid_at' => 'datetime',
        'initiated_at' => 'datetime',
        'verified_at' => 'datetime',
        'verification_attempts' => 'integer',
    ];

    protected $hidden = [
        'gateway_request',
        'gateway_response',
        'callback_data',
        'card_hash',
        'gateway_signature',
    ];

    // === Constants ===
    public const VERIFICATION_NOT_VERIFIED = 'not_verified';
    public const VERIFICATION_VERIFIED = 'verified';
    public const VERIFICATION_MISMATCH = 'mismatch_amount';
    public const VERIFICATION_FAILED = 'verification_failed';

    // === Boot ===
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Payment $payment) {
            if (empty($payment->code)) {
                $payment->code = self::generateUniqueCode();
            }
            if (empty($payment->transaction_id)) {
                $payment->transaction_id = self::generateTransactionId();
            }
        });
    }

    // === Relationships ===
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

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    // === Static Methods ===
    public static function generateUniqueCode(): string
    {
        do {
            $code = 'PMT-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public static function generateTransactionId(): string
    {
        return 'NS-PAY-' . date('ymdHis') . '-' . strtoupper(substr(md5((string) mt_rand()), 0, 6));
    }

    /**
     * تولید Idempotency Key
     */
    public static function generateIdempotencyKey(int $userId, ?int $orderId, ?int $bookingId): string
    {
        return hash('sha256', implode('-', [
            $userId,
            $orderId ?? 'null',
            $bookingId ?? 'null',
            now()->format('Y-m-d-H'), // هر ساعت یک بار
        ]));
    }

    // === Methods ===
    public function isSuccessful(): bool
    {
        return $this->status->isSuccessful();
    }

    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    public function isVerified(): bool
    {
        return $this->verification_status === self::VERIFICATION_VERIFIED;
    }

    /**
     * آیا مبلغ تأیید شده با مبلغ درخواستی مطابقت دارد؟
     */
    public function isAmountMatched(): bool
    {
        if (!$this->verified_amount) {
            return false;
        }

        return abs((float) $this->amount - $this->verified_amount) < 1; // تلورانس ۱ ریال
    }

    /**
     * دریافت مرتبط (booking یا order)
     */
    public function getRelated(): ?Model
    {
        return $this->order ?? $this->booking;
    }

    /**
     * برچسب وضعیت تأیید
     */
    public function getVerificationStatusLabelAttribute(): string
    {
        return match ($this->verification_status) {
            self::VERIFICATION_NOT_VERIFIED => 'تأیید نشده',
            self::VERIFICATION_VERIFIED => 'تأیید شده',
            self::VERIFICATION_MISMATCH => 'عدم تطابق مبلغ',
            self::VERIFICATION_FAILED => 'خطا در تأیید',
            default => 'نامشخص',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount) . ' ' . $this->currency;
    }

    public function getFormattedVerifiedAmountAttribute(): string
    {
        if (!$this->verified_amount) {
            return '—';
        }
        return number_format($this->verified_amount) . ' ' . $this->currency;
    }
}
