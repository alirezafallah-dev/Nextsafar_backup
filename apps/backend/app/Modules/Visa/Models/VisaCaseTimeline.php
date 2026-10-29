<?php

namespace App\Modules\Visa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VisaCaseTimeline extends Model
{
    public $timestamps = false;
    protected $table = 'visa_case_timeline';

    protected $fillable = [
        'visa_case_id', 'event_type', 'title', 'description',
        'old_status', 'new_status', 'related_type', 'related_id',
        'user_id', 'user_name', 'user_type', 'metadata', 'visibility', 'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function visaCase(): BelongsTo
    {
        return $this->belongsTo(VisaCase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function isVisibleToCustomer(): bool
    {
        return in_array($this->visibility, ['public', 'both']);
    }

    public function isVisibleToAgent(): bool
    {
        return in_array($this->visibility, ['internal', 'both']);
    }

    /**
     * ثبت رویداد جدید در تایم‌لاین
     */
    public static function record(
        VisaCase $case,
        string $eventType,
        string $title,
        ?string $description = null,
        array $metadata = [],
        string $visibility = 'both',
        ?Model $related = null
    ): self {
        return self::create([
            'visa_case_id' => $case->id,
            'event_type' => $eventType,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
            'visibility' => $visibility,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'user_type' => auth()->check() ? 'agent' : 'system',
            'related_type' => $related ? get_class($related) : null,
            'related_id' => $related?->id,
            'occurred_at' => now(),
        ]);
    }
}
