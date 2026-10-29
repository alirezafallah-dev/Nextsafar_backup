<?php

namespace App\Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourMedia extends Model
{
    protected $table = 'tour_media';

    protected $fillable = [
        'tour_id',
        'type',
        'path',
        'title',
        'alt_text',
        'caption',
        'is_cover',
        'is_public',
        'sort_order',
        'file_size',
        'mime_type',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
        'is_public' => 'boolean',
        'sort_order' => 'integer',
        'file_size' => 'integer',
    ];

    // === Constants ===
    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_BROCHURE = 'brochure';

    // === Relationships ===

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    // === Scopes ===

    public function scopeImages($query)
    {
        return $query->where('type', self::TYPE_IMAGE);
    }

    public function scopeVideos($query)
    {
        return $query->where('type', self::TYPE_VIDEO);
    }

    public function scopeCover($query)
    {
        return $query->where('is_cover', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // === Methods ===

    public function getUrlAttribute(): string
    {
        // TODO: Integrate with Object Storage (Phase 4)
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }
        return asset('storage/' . $this->path);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '—';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
