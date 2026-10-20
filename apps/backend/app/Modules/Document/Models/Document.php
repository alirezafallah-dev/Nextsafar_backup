<?php

namespace App\Modules\Document\Models;

use App\Models\User;
use App\Modules\Document\Enums\DocumentCategory;
use App\Modules\Document\Enums\DocumentVisibility;
use App\Modules\Document\Enums\ScanStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'code', 'original_filename', 'stored_filename', 'path', 'disk',
        'mime_type', 'extension', 'size', 'checksum', 'category', 'visibility',
        'documentable_type', 'documentable_id', 'user_id', 'uploaded_by',
        'title', 'description', 'metadata', 'is_verified', 'scan_status',
        'verified_at', 'download_count', 'last_downloaded_at',
    ];

    protected $casts = [
        'category' => DocumentCategory::class,
        'visibility' => DocumentVisibility::class,
        'scan_status' => ScanStatus::class,
        'metadata' => 'array',
        'size' => 'integer',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
        'download_count' => 'integer',
    ];

    protected $hidden = [
        'stored_filename',
        'path',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Document $document) {
            if (empty($document->uuid)) {
                $document->uuid = (string) Str::uuid();
            }
            if (empty($document->code)) {
                $document->code = self::generateUniqueCode();
            }
        });
    }

    // === Relationships ===

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(DocumentAccessLog::class);
    }

    public function tempLinks(): HasMany
    {
        return $this->hasMany(DocumentTempLink::class);
    }

    // === Scopes ===

    public function scopePublic($query)
    {
        return $query->where('visibility', DocumentVisibility::PUBLIC);
    }

    public function scopePrivate($query)
    {
        return $query->where('visibility', DocumentVisibility::PRIVATE);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfCategory($query, DocumentCategory $category)
    {
        return $query->where('category', $category);
    }

    public function scopeForDocumentable($query, string $type, int $id)
    {
        return $query->where('documentable_type', $type)
            ->where('documentable_id', $id);
    }

    public function scopeClean($query)
    {
        return $query->where('scan_status', ScanStatus::CLEAN);
    }

    public function scopePendingScan($query)
    {
        return $query->where('scan_status', ScanStatus::PENDING);
    }

    // === Methods ===

    public function isPublic(): bool
    {
        return $this->visibility === DocumentVisibility::PUBLIC;
    }

    public function isPrivate(): bool
    {
        return $this->visibility === DocumentVisibility::PRIVATE;
    }

    public function isScanClean(): bool
    {
        return $this->scan_status === ScanStatus::CLEAN;
    }

    public function isInfected(): bool
    {
        return $this->scan_status === ScanStatus::INFECTED;
    }

    public function isVisaRelated(): bool
    {
        return $this->category->isVisaRelated();
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * افزایش شمارنده دانلود
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
        $this->update(['last_downloaded_at' => now()]);
    }

    // === Accessors ===

    public function getFormattedSizeAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->category->label();
    }

    public function getVisibilityLabelAttribute(): string
    {
        return $this->visibility->label();
    }

    // === Static ===

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'DOC-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public static function findByUuid(string $uuid): ?self
    {
        return self::where('uuid', $uuid)->first();
    }

    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }
}
