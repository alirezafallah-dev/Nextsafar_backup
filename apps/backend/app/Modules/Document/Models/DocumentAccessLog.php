<?php

namespace App\Modules\Document\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAccessLog extends Model
{
    public $timestamps = false;
    protected $table = 'document_access_logs';

    protected $fillable = [
        'document_id', 'action', 'user_id', 'ip_address', 'user_agent',
        'token_used', 'result', 'reason', 'accessed_at',
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        Document $document,
        string $action,
        ?int $userId = null,
        string $result = 'success',
        ?string $reason = null,
        ?string $token = null
    ): self {
        return self::create([
            'document_id' => $document->id,
            'action' => $action,
            'user_id' => $userId ?? auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
            'token_used' => $token,
            'result' => $result,
            'reason' => $reason,
            'accessed_at' => now(),
        ]);
    }
}
