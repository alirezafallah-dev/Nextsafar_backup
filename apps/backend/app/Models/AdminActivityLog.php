<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class AdminActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_slug',
        'action',
        'resource_type',
        'resource_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'is_sensitive',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'is_sensitive' => 'boolean',
        'created_at' => 'datetime',
    ];

    // === Relationships ===

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // === Static Factory Methods ===

    public static function log(
        string $action,
        string $resourceType,
        ?int $resourceId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        bool $isSensitive = false
    ): self {
        $request = request();
        $user = auth()->user();

        return self::create([
            'user_id' => $user?->id,
            'role_slug' => $user?->roles()->first()?->slug,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'is_sensitive' => $isSensitive,
            'created_at' => now(),
        ]);
    }

    // === Scopes ===

    public function scopeSensitive($query)
    {
        return $query->where('is_sensitive', true);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForResource($query, string $type, ?int $id = null)
    {
        $query->where('resource_type', $type);
        if ($id !== null) {
            $query->where('resource_id', $id);
        }
        return $query;
    }
}
