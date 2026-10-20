<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelSlug extends Model
{
    protected $fillable = [
        'hotel_id',
        'slug',
        'changed_at',
    ];

    protected $casts = [
        'hotel_id' => 'integer',
        'changed_at' => 'datetime',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
