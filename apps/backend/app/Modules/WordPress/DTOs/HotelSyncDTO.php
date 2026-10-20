<?php

namespace App\Modules\WordPress\DTOs;

/**
 * Data Transfer Object for Hotel Sync from WordPress
 */
class HotelSyncDTO
{
    public function __construct(
        public readonly ?int $wpPostId,
        public readonly string $slug,
        public readonly string $name,
        public readonly string $publishStatus,
        public readonly ?string $contentVersion,
        public readonly ?string $city = null,
        public readonly ?string $country = null,
        public readonly ?string $address = null,
        public readonly ?string $shortDescription = null,
        public readonly ?string $fullDescription = null,
        public readonly ?array $faq = null,
        public readonly ?array $tags = null,
        public readonly ?array $categories = null,
        public readonly ?string $featuredImageUrl = null,
        public readonly ?array $galleryImages = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
        public readonly ?array $facilities = null,
        public readonly ?float $rating = null,
        public readonly ?int $reviewCount = null,
        public readonly ?string $seoTitle = null,
        public readonly ?string $seoDescription = null,
        public readonly ?string $seoKeyword = null,
        public readonly ?string $seoImageUrl = null,
        public readonly ?string $canonicalUrl = null,
        public readonly ?array $metadata = null,
        public readonly ?string $publishedAt = null,
    ) {}

    /**
     * Create DTO from WordPress webhook payload
     */
    public static function fromWebhookPayload(array $payload): self
    {
        return new self(
            wpPostId: $payload['wp_post_id'] ?? null,
            slug: $payload['slug'] ?? '',
            name: $payload['name'] ?? '',
            publishStatus: $payload['publish_status'] ?? 'draft',
            contentVersion: $payload['content_version'] ?? null,
            city: $payload['city'] ?? null,
            country: $payload['country'] ?? null,
            address: $payload['address'] ?? null,
            shortDescription: $payload['short_description'] ?? null,
            fullDescription: $payload['full_description'] ?? null,
            faq: $payload['faq'] ?? null,
            tags: $payload['tags'] ?? null,
            categories: $payload['categories'] ?? null,
            featuredImageUrl: $payload['featured_image_url'] ?? null,
            galleryImages: $payload['gallery_images'] ?? null,
            latitude: isset($payload['latitude']) ? (float) $payload['latitude'] : null,
            longitude: isset($payload['longitude']) ? (float) $payload['longitude'] : null,
            facilities: $payload['facilities'] ?? null,
            rating: isset($payload['rating']) ? (float) $payload['rating'] : null,
            reviewCount: $payload['review_count'] ?? null,
            seoTitle: $payload['seo_title'] ?? null,
            seoDescription: $payload['seo_description'] ?? null,
            seoKeyword: $payload['seo_keyword'] ?? null,
            seoImageUrl: $payload['seo_image_url'] ?? null,
            canonicalUrl: $payload['canonical_url'] ?? null,
            metadata: $payload['metadata'] ?? null,
            publishedAt: $payload['published_at'] ?? null,
        );
    }

    /**
     * Convert to array for Hotel model
     * IMPORTANT: Filters out null values so database defaults are applied
     */
    public function toHotelArray(): array
    {
        $data = [
            'wp_post_id' => $this->wpPostId,
            'slug' => $this->slug,
            'name' => $this->name,
            'publish_status' => $this->publishStatus,
            'wp_content_version' => $this->contentVersion,
            'city' => $this->city,
            'country' => $this->country,
            'address' => $this->address,
            'short_description' => $this->shortDescription,
            'full_description' => $this->fullDescription,
            'faq' => $this->faq,
            'tags' => $this->tags,
            'categories' => $this->categories,
            'featured_image_url' => $this->featuredImageUrl,
            'gallery_images' => $this->galleryImages,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'facilities' => $this->facilities,
            'rating' => $this->rating,
            'review_count' => $this->reviewCount,
            'seo_title' => $this->seoTitle,
            'seo_description' => $this->seoDescription,
            'seo_keyword' => $this->seoKeyword,
            'seo_image_url' => $this->seoImageUrl,
            'canonical_url' => $this->canonicalUrl,
            'metadata' => $this->metadata,
            'published_at' => $this->publishedAt,
        ];

        // Filter out null values so database defaults are applied
        return array_filter($data, fn($value) => $value !== null);
    }
}
