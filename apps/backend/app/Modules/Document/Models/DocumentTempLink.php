<?php

namespace App\Modules\Document\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DocumentTempLink extends Model
{
    protected $table = 'document_temp_links';

    protected $fillable = [
        'document_id', 'token', 'created_by', 'max_uses', 'uses_count',
        'expires_at', 'is_revoked', 'allowed_ip', 'allowed_user_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_revoked' => 'boolean',
        'max_uses' => 'integer',
        'uses_count' => 'integer',
    ];

    protected $hidden = ['token'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $link) {
            if (empty($link->token)) {
                $link->token = Str::random(64);
            }
        });
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isValid(): bool
    {
        return !$this->is_revoked
            && $this->expires_at->isFuture()
            && $this->uses_count < $this->max_uses;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function canBeUsedBy(?int $userId = null, ?string $ip = null): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->allowed_user_id && $this->allowed_user_id !== $userId) {
            return false;
        }

        if ($this->allowed_ip && $this->allowed_ip !== $ip) {
            return false;
        }

        return true;
    }

    public function useOnce(): void
    {
        $this->increment('uses_count');
    }

    public function revoke(): void
    {
        $this->update(['is_revoked' => true]);
    }

    /**
     * تولید URL کامل
     */
    public function getUrlAttribute(): string
    {
        return url("/api/v1/documents/temp/{$this->token}");
    }
}
