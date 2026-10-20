<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // WordPress Sync
        'wp_post_id',
        'wp_post_type',
        'wp_last_synced_at',
        'wp_content_version',
        
        // Basic Info
        'slug',
        'name',
        'city',
        'country',
        'address',
        
        // Status
        'publish_status',
        'display_status',
        'sync_status',
        'content_source',
        
        // Content
        'short_description',
        'full_description',
        'faq',
        'tags',
        'categories',
        
        // Media
        'featured_image_url',
        'gallery_images',
        
        // Location
        'latitude',
        'longitude',
        
        // Facilities & Rating
        'facilities',
        'rating',
        'review_count',
        
        // SEO
        'seo_title',
        'seo_description',
        'seo_keyword',
        'seo_image_url',
        'canonical_url',
        
        // Metadata
        'metadata',
        'published_at',
    ];

    protected $casts = [
        'wp_post_id' => 'integer',
        'wp_last_synced_at' => 'datetime',
        'published_at' => 'datetime',
        'faq' => 'array',
        'tags' => 'array',
        'categories' => 'array',
        'gallery_images' => 'array',
        'facilities' => 'array',
        'rating' => 'decimal:2',
        'review_count' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'metadata' => 'array',
    ];

    // === Relationships ===

    public function slugs(): HasMany
    {
        return $this->hasMany(HotelSlug::class);
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(SyncLog::class, 'laravel_record_id');
    }

    // === Scopes ===

    public function scopePublished($query)
    {
        return $query->where('publish_status', 'published')
            ->where('display_status', 'active');
    }

    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    public function scopeFromWordPress($query)
    {
        return $query->where('content_source', 'wordpress');
    }

    // === Methods ===

    public function isActive(): bool
    {
        return $this->publish_status === 'published' 
            && $this->display_status === 'active';
    }

    public function needsUpdate(): bool
    {
        return $this->display_status === 'needs_update'
            || $this->sync_status === 'out_of_sync';
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->address, $this->city, $this->country]);
        return implode(', ', $parts);
    }

    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ];
        }
        return null;
    }
}
