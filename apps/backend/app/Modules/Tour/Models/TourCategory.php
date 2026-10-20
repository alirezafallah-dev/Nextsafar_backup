<?php

namespace App\Modules\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // === Relationships ===

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TourCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(TourCategory::class, 'parent_id');
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'category_id');
    }

    // === Scopes ===

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // === Methods ===

    public function getToursCount(): int
    {
        return $this->tours()->where('status', 'published')->count();
    }

    public static function getTree(): array
    {
        return self::with(['children' => function ($q) {
            $q->active()->ordered();
        }])
            ->root()
            ->active()
            ->ordered()
            ->get()
            ->toArray();
    }
}
