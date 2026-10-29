<?php

namespace App\Modules\Notification\Models;

use App\Modules\Notification\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'event_type', 'channel', 'subject', 'content',
        'variables', 'locale', 'is_active', 'is_system', 'description', 'metadata',
    ];

    protected $casts = [
        'channel' => NotificationChannel::class,
        'variables' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    // === Relationships ===
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // === Scopes ===
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEvent($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeForChannel($query, NotificationChannel $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeForLocale($query, string $locale = 'fa')
    {
        return $query->where('locale', $locale);
    }

    // === Static ===
    public static function findForEvent(string $eventType, NotificationChannel $channel, string $locale = 'fa'): ?self
    {
        return self::active()
            ->forEvent($eventType)
            ->forChannel($channel)
            ->forLocale($locale)
            ->first();
    }
}
