<?php

namespace App\Modules\Lead\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadStatusHistory extends Model
{
    protected $table = 'lead_status_history';
    public $timestamps = false;

    protected $fillable = [
        'lead_id',
        'old_status',
        'new_status',
        'reason',
        'changed_by',
        'changed_by_name',
        'ip_address',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * ثبت تغییر وضعیت
     */
    public static function record(Lead $lead, string $newStatus, ?string $reason = null, ?User $changedBy = null): self
    {
        return self::create([
            'lead_id' => $lead->id,
            'old_status' => $lead->getOriginal('status'),
            'new_status' => $newStatus,
            'reason' => $reason,
            'changed_by' => $changedBy?->id,
            'changed_by_name' => $changedBy?->name ?? 'System',
            'ip_address' => request()?->ip(),
            'changed_at' => now(),
        ]);
    }
}
