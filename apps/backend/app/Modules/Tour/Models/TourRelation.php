<?php

namespace App\Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourRelation extends Model
{
    protected $table = 'tour_relations';

    protected $fillable = [
        'tour_id',
        'related_tour_id',
        'relation_type',
        'priority',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    // === Constants ===
    public const TYPE_SIMILAR = 'similar';
    public const TYPE_COMPLEMENTARY = 'complementary';
    public const TYPE_ALTERNATIVE = 'alternative';
    public const TYPE_UPGRADE = 'upgrade';

    // === Relationships ===

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function relatedTour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'related_tour_id');
    }

    // === Scopes ===

    public function scopeSimilar($query)
    {
        return $query->where('relation_type', self::TYPE_SIMILAR);
    }

    public function scopeAlternative($query)
    {
        return $query->where('relation_type', self::TYPE_ALTERNATIVE);
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('priority');
    }
}
