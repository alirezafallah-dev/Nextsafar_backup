<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends Model
{
    protected $fillable = [
        'sync_type',
        'event_type',
        'wp_post_id',
        'laravel_record_id',
        'status',
        'attempt_count',
        'max_attempts',
        'received_at',
        'processed_at',
        'next_retry_at',
        'content_version',
        'payload',
        'response',
        'error_message',
        'error_trace',
        'webhook_signature',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'wp_post_id' => 'integer',
        'laravel_record_id' => 'integer',
        'attempt_count' => 'integer',
        'max_attempts' => 'integer',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
        'next_retry_at' => 'datetime',
        'payload' => 'array',
        'response' => 'array',
        'metadata' => 'array',
    ];

    // === Relationships ===

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'laravel_record_id');
    }

    // === Scopes ===

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRetryable($query)
    {
        return $query->where('status', 'failed')
            ->where('attempt_count', '<', 3)
            ->whereNotNull('next_retry_at')
            ->where('next_retry_at', '<=', now());
    }

    // === Methods ===

    public function canRetry(): bool
    {
        return $this->status === 'failed' 
            && $this->attempt_count < $this->max_attempts;
    }

    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'processed_at' => now(),
        ]);
    }

    public function markAsSuccess(array $response = []): void
    {
        $this->update([
            'status' => 'success',
            'processed_at' => now(),
            'response' => $response,
        ]);
    }

    public function markAsFailed(string $error, ?string $trace = null): void
    {
        $attemptCount = $this->attempt_count + 1;
        $nextRetry = null;
        
        if ($attemptCount < $this->max_attempts) {
            // Exponential backoff: 1min, 5min, 15min
            $delayMinutes = match($attemptCount) {
                1 => 1,
                2 => 5,
                3 => 15,
                default => 30,
            };
            $nextRetry = now()->addMinutes($delayMinutes);
        }

        $this->update([
            'status' => $attemptCount < $this->max_attempts ? 'retrying' : 'failed',
            'attempt_count' => $attemptCount,
            'error_message' => $error,
            'error_trace' => $trace,
            'next_retry_at' => $nextRetry,
        ]);
    }
}
