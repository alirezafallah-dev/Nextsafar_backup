<?php

namespace App\Modules\Ledger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LedgerAccount extends Model
{
    protected $fillable = [
        'code', 'name', 'name_en', 'type', 'normal_balance',
        'parent_id', 'category', 'description', 'is_active',
        'is_system', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'sort_order' => 'integer',
    ];

    // === Constants ===
    public const TYPE_ASSET = 'asset';
    public const TYPE_LIABILITY = 'liability';
    public const TYPE_EQUITY = 'equity';
    public const TYPE_REVENUE = 'revenue';
    public const TYPE_EXPENSE = 'expense';

    public const BALANCE_DEBIT = 'debit';
    public const BALANCE_CREDIT = 'credit';

    // حساب‌های کلیدی سیستمی
    public const CODE_CASH = '1100';
    public const CODE_GATEWAY_BALANCE = '1110';
    public const CODE_RECEIVABLES = '1200';
    public const CODE_PAYABLES = '2100';
    public const CODE_PROVIDER_PAYABLES = '2110';
    public const CODE_CUSTOMER_ADVANCES = '2200';
    public const CODE_TOUR_REVENUE = '4100';
    public const CODE_HOTEL_REVENUE = '4110';
    public const CODE_VISA_REVENUE = '4120';
    public const CODE_COMMISSION = '4200';
    public const CODE_TOUR_COGS = '5100';
    public const CODE_HOTEL_COGS = '5110';
    public const CODE_GATEWAY_FEES = '5200';
    public const CODE_REFUND_EXPENSE = '5300';

    // === Relationships ===
    public function parent(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(LedgerAccount::class, 'parent_id');
    }

    public function debitEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class, 'account_debit', 'code');
    }

    public function creditEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class, 'account_credit', 'code');
    }

    // === Scopes ===
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAssets($query)
    {
        return $query->where('type', self::TYPE_ASSET);
    }

    public function scopeLiabilities($query)
    {
        return $query->where('type', self::TYPE_LIABILITY);
    }

    public function scopeRevenues($query)
    {
        return $query->where('type', self::TYPE_REVENUE);
    }

    public function scopeExpenses($query)
    {
        return $query->where('type', self::TYPE_EXPENSE);
    }

    // === Methods ===

    /**
     * محاسبه مانده حساب
     */
    public function getBalance(?string $fromDate = null, ?string $toDate = null): int
    {
        $query = LedgerEntry::query();

        if ($fromDate) $query->where('transaction_date', '>=', $fromDate);
        if ($toDate) $query->where('transaction_date', '<=', $toDate);

        $debitSum = (clone $query)->where('account_debit', $this->code)->sum('amount');
        $creditSum = (clone $query)->where('account_credit', $this->code)->sum('amount');

        // اگر مانده طبیعی بدهکار باشد (asset, expense)
        if ($this->normal_balance === self::BALANCE_DEBIT) {
            return $debitSum - $creditSum;
        }

        // اگر مانده طبیعی بستانکار باشد (liability, equity, revenue)
        return $creditSum - $debitSum;
    }

    /**
     * آیا این یک حساب دارایی یا هزینه است؟ (مانده طبیعی بدهکار)
     */
    public function hasDebitNormalBalance(): bool
    {
        return $this->normal_balance === self::BALANCE_DEBIT;
    }

    /**
     * برچسب فارسی نوع حساب
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ASSET => 'دارایی',
            self::TYPE_LIABILITY => 'بدهی',
            self::TYPE_EQUITY => 'سرمایه',
            self::TYPE_REVENUE => 'درآمد',
            self::TYPE_EXPENSE => 'هزینه',
            default => 'نامشخص',
        };
    }

    // === Static ===

    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }
}
