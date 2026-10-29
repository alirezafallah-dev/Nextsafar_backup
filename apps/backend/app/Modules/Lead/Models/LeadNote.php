<?php

namespace App\Modules\Lead\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadNote extends Model
{
    protected $fillable = [
        'lead_id',
        'type',
        'content',
        'visibility',
        'is_pinned',
        'author_id',
        'author_name',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public const TYPE_NOTE = 'note';
    public const TYPE_CALL = 'call';
    public const TYPE_EMAIL = 'email';
    public const TYPE_SMS = 'sms';
    public const TYPE_MEETING = 'meeting';
    public const TYPE_TASK = 'task';
    public const TYPE_SYSTEM = 'system';

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeInternal($query)
    {
        return $query->where('visibility', 'internal');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_NOTE => 'یادداشت',
            self::TYPE_CALL => 'تماس',
            self::TYPE_EMAIL => 'ایمیل',
            self::TYPE_SMS => 'پیامک',
            self::TYPE_MEETING => 'جلسه',
            self::TYPE_TASK => 'وظیفه',
            self::TYPE_SYSTEM => 'سیستمی',
            default => 'یادداشت',
        };
    }
}
