<?php

namespace App\Modules\Visa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaRule extends Model
{
    protected $fillable = [
        'visa_type_id', 'rule_key', 'title', 'description',
        'value_type', 'value', 'importance', 'is_mandatory', 'sort_order',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function visaType(): BelongsTo
    {
        return $this->belongsTo(VisaType::class);
    }

    /**
     * دریافت مقدار با cast مناسب
     */
    public function getTypedValueAttribute(): mixed
    {
        return match ($this->value_type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'number' => (float) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
