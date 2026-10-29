<?php

namespace App\Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourOldSlug extends Model
{
    protected $table = 'tour_old_slugs';

    public $timestamps = false;

    protected $fillable = [
        'tour_id',
        'slug',
        'changed_at',
        'redirect_count',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'redirect_count' => 'integer',
    ];

    // === Relationships ===

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    // === Methods ===

    public function incrementRedirectCount(): void
    {
        $this->increment('redirect_count');
    }

    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)->first();
    }
}
