<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleHistory extends Model
{
    protected $table = 'role_history';
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'old_role_id',
        'new_role_id',
        'changed_by',
        'reason',
        'ip_address',
        'user_agent',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    // === Relationships ===

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function oldRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'old_role_id');
    }

    public function newRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'new_role_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // === Constants ===
    public const ACTION_ASSIGNED = 'assigned';
    public const ACTION_REMOVED = 'removed';
    public const ACTION_CHANGED = 'changed';
}
