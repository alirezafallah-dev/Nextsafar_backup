<?php

namespace App\Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourService extends Model
{
    protected $fillable = [
        'tour_id',
        'type',
        'title',
        'icon',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // === Constants ===
    public const TYPE_INCLUDED = 'included';
    public const TYPE_EXCLUDED = 'excluded';

    // === Relationships ===

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    // === Scopes ===

    public function scopeIncluded($query)
    {
        return $query->where('type', self::TYPE_INCLUDED);
    }

    public function scopeExcluded($query)
    {
        return $query->where('type', self::TYPE_EXCLUDED);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
