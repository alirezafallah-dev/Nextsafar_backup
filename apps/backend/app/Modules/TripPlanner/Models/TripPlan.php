<?php

namespace App\Modules\TripPlanner\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class TripPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'destination',
        'country',
        'days',
        'budget_level',
        'interests',
        'travelers',
        'start_date',
        'trip_title',
        'trip_summary',
        'days_plan',
        'total_budget_min',
        'total_budget_max',
        'currency',
        'suggested_hotels',
        'suggested_tours',
        'suggested_restaurants',
        'model_used',
        'tokens_used',
        'generation_time_ms',
        'status',
        'attempts',
        'error_message',
        'expires_at',
        'last_viewed_at',
        'view_count',
    ];

    protected $casts = [
        'interests' => 'array',
        'days_plan' => 'array',
        'suggested_hotels' => 'array',
        'suggested_tours' => 'array',
        'suggested_restaurants' => 'array',
        'start_date' => 'date',
        'expires_at' => 'datetime',
        'last_viewed_at' => 'datetime',
        'days' => 'integer',
        'travelers' => 'integer',
        'total_budget_min' => 'integer',
        'total_budget_max' => 'integer',
        'tokens_used' => 'integer',
        'generation_time_ms' => 'integer',
        'attempts' => 'integer',
        'view_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ═══════════════════════════════════════════════════
    // SCOPES
    // ═══════════════════════════════════════════════════
    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeForUser($query, ?int $userId)
    {
        if (!$userId) {
            return $query;
        }
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, ?string $sessionId)
    {
        if (!$sessionId) {
            return $query;
        }
        return $query->where('session_id', $sessionId);
    }

    // ═══════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════
    
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
        $this->update(['last_viewed_at' => now()]);
    }
}
