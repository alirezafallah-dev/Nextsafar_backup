<?php

namespace App\Modules\Visa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaCaseNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'visa_case_id', 'type', 'content', 'visibility', 'is_pinned',
        'author_id', 'author_name', 'author_type', 'parent_id',
        'is_read_by_customer', 'is_read_by_agent',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_read_by_customer' => 'boolean',
        'is_read_by_agent' => 'boolean',
    ];

    public function visaCase(): BelongsTo
    {
        return $this->belongsTo(VisaCase::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(VisaCaseNote::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(VisaCaseNote::class, 'parent_id');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeInternal($query)
    {
        return $query->where('visibility', 'internal');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}
