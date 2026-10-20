<?php

namespace App\Modules\Order\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_history';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'reason',
        'changed_by',
        'changed_by_name',
        'change_source',
        'ip_address',
        'metadata',
        'changed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'changed_at' => 'datetime',
    ];

    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_SYSTEM = 'system';
    public const SOURCE_PAYMENT = 'payment_callback';
    public const SOURCE_USER = 'user_request';

    // === Relationships ===
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // === Static Factory ===
    public static function record(
        Order $order,
        string $newStatus,
        ?string $reason = null,
        ?User $changedBy = null,
        string $source = self::SOURCE_MANUAL,
        ?array $metadata = null
    ): self {
        return self::create([
            'order_id' => $order->id,
            'old_status' => $order->getOriginal('status') ?? $order->status,
            'new_status' => $newStatus,
            'reason' => $reason,
            'changed_by' => $changedBy?->id,
            'changed_by_name' => $changedBy?->name ?? 'System',
            'change_source' => $source,
            'ip_address' => request()?->ip(),
            'metadata' => $metadata,
            'changed_at' => now(),
        ]);
    }
}
