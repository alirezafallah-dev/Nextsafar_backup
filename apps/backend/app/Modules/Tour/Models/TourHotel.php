<?php

namespace App\Modules\Tour\Models;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourHotel extends Model
{
    protected $table = 'tour_hotels';

    protected $fillable = [
        'tour_id',
        'hotel_id',
        'relation_type',
        'priority',
        'nights',
        'room_type',
        'notes',
    ];

    protected $casts = [
        'priority' => 'integer',
        'nights' => 'integer',
    ];

    // === Constants ===
    public const TYPE_MAIN = 'main';
    public const TYPE_ALTERNATIVE = 'alternative';
    public const TYPE_SIMILAR = 'similar';

    // === Relationships ===

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    // === Scopes ===

    public function scopeMain($query)
    {
        return $query->where('relation_type', self::TYPE_MAIN);
    }

    public function scopeAlternative($query)
    {
        return $query->where('relation_type', self::TYPE_ALTERNATIVE);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority');
    }
}
