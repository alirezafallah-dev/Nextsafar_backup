<?php

namespace App\Modules\Notification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    public $timestamps = false;
    protected $table = 'notification_logs';

    protected $fillable = [
        'notification_id', 'event', 'message', 'payload', 'response',
        'provider_message_id', 'duration_ms', 'actor', 'logged_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'duration_ms' => 'integer',
        'logged_at' => 'datetime',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * ثبت لاگ جدید
     */
    public static function record(
        Notification $notification,
        string $event,
        ?string $message = null,
        ?array $payload = null,
        ?array $response = null,
        ?string $providerMessageId = null,
        ?int $durationMs = null,
        string $actor = 'system'
    ): self {
        return self::create([
            'notification_id' => $notification->id,
            'event' => $event,
            'message' => $message,
            'payload' => $payload,
            'response' => $response,
            'provider_message_id' => $providerMessageId,
            'duration_ms' => $durationMs,
            'actor' => $actor,
            'logged_at' => now(),
        ]);
    }
}
