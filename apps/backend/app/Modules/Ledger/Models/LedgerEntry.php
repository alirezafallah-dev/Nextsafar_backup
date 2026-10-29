<?php

namespace App\Modules\Ledger\Models;

use App\Models\User;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LedgerEntry extends Model
{
    protected $fillable = [
        'code', 'batch_id', 'type', 'order_id', 'payment_id', 'refund_id',
        'account_debit', 'account_credit', 'amount', 'currency',
        'exchange_rate', 'amount_in_base_currency', 'description', 'notes',
        'reference_type', 'reference_code', 'created_by', 'transaction_date',
        'metadata', 'is_reversed', 'reversed_by_entry',
    ];

    protected $casts = [
        'amount' => 'integer',
        'exchange_rate' => 'decimal:6',
        'amount_in_base_currency' => 'integer',
        'metadata' => 'array',
        'is_reversed' => 'boolean',
        'transaction_date' => 'datetime',
    ];

    // === Constants ===
    public const TYPE_PAYMENT_RECEIVED = 'payment_received';
    public const TYPE_REFUND_ISSUED = 'refund_issued';
    public const TYPE_COMMISSION = 'commission_earned';
    public const TYPE_TAX = 'tax_collected';
    public const TYPE_PROVIDER_PAYMENT = 'provider_payment';
    public const TYPE_ADJUSTMENT = 'adjustment';
    public const TYPE_REVERSAL = 'reversal';
    public const TYPE_MANUAL = 'manual';

    public const ALL_TYPES = [
        self::TYPE_PAYMENT_RECEIVED,
        self::TYPE_REFUND_ISSUED,
        self::TYPE_COMMISSION,
        self::TYPE_TAX,
        self::TYPE_PROVIDER_PAYMENT,
        self::TYPE_ADJUSTMENT,
        self::TYPE_REVERSAL,
        self::TYPE_MANUAL,
    ];

    // === Boot ===
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (LedgerEntry $entry) {
            if (empty($entry->code)) {
                $entry->code = self::generateUniqueCode();
            }
            if (empty($entry->batch_id)) {
                $entry->batch_id = self::generateBatchId();
            }
            if (empty($entry->amount_in_base_currency)) {
                $entry->amount_in_base_currency = $entry->amount * $entry->exchange_rate;
            }
        });
    }

    // === Relationships ===
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'account_debit', 'code');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'account_credit', 'code');
    }

    public function reversalEntry(): BelongsTo
    {
        return $this->belongsTo(LedgerEntry::class, 'reversed_by_entry');
    }

    // === Scopes ===
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForOrder($query, int $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeForPayment($query, int $paymentId)
    {
        return $query->where('payment_id', $paymentId);
    }

    public function scopeDateRange($query, $from, $to)
    {
        if ($from) $query->where('transaction_date', '>=', $from);
        if ($to) $query->where('transaction_date', '<=', $to);
        return $query;
    }

    public function scopeNotReversed($query)
    {
        return $query->where('is_reversed', false);
    }

    public function scopeBatch($query, string $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    // === Methods ===
    public function isReversed(): bool
    {
        return $this->is_reversed;
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount) . ' ' . $this->currency;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_PAYMENT_RECEIVED => 'دریافت پرداخت',
            self::TYPE_REFUND_ISSUED => 'استرداد',
            self::TYPE_COMMISSION => 'کارمزد',
            self::TYPE_TAX => 'مالیات',
            self::TYPE_PROVIDER_PAYMENT => 'پرداخت به پروایدر',
            self::TYPE_ADJUSTMENT => 'تعدیل',
            self::TYPE_REVERSAL => 'برگشت',
            self::TYPE_MANUAL => 'دستی',
            default => 'نامشخص',
        };
    }

    // === Static ===
    public static function generateUniqueCode(): string
    {
        do {
            $code = 'LED-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public static function generateBatchId(): string
    {
        return 'BATCH-' . now()->format('ymdHis') . '-' . strtoupper(Str::random(4));
    }

    /**
     * یافتن همه تراکنش‌های یک دسته
     */
    public static function getBatch(string $batchId)
    {
        return self::batch($batchId)->orderBy('id')->get();
    }
}
