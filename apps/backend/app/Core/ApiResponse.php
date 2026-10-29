<?php

namespace App\Core;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

/**
 * Standard API Response wrapper.
 * Based on project spec Section 24.1 (Success Response).
 *
 * Structure:
 * {
 *   "success": true,
 *   "data": {...},
 *   "meta": {...},      // optional
 *   "message": "..."    // optional
 * }
 */
class ApiResponse implements Responsable, Arrayable
{
    public function __construct(
        protected mixed $data = null,
        protected ?string $message = null,
        protected ?array $meta = null,
        protected int $statusCode = 200,
        protected array $headers = [],
    ) {}

    /**
     * Create a success response with data.
     */
    public static function success(mixed $data = null, ?string $message = null, ?array $meta = null, int $statusCode = 200): self
    {
        return new self($data, $message, $meta, $statusCode);
    }

    /**
     * Create a created response (201).
     */
    public static function created(mixed $data = null, ?string $message = null): self
    {
        return new self($data, $message, null, 201);
    }

    /**
     * Create an accepted response (202) — useful for queued operations.
     */
    public static function accepted(mixed $data = null, ?string $message = null): self
    {
        return new self($data, $message, null, 202);
    }

    /**
     * Create a no-content response (204).
     */
    public static function noContent(): self
    {
        return new self(null, null, null, 204);
    }

    /**
     * Add metadata (pagination, filters, etc.).
     */
    public function withMeta(array $meta): self
    {
        $this->meta = array_merge($this->meta ?? [], $meta);
        return $this;
    }

    /**
     * Add pagination metadata from a LengthAwarePaginator.
     */
    public function withPagination($paginator): self
    {
        return $this->withMeta([
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    public function toArray(): array
    {
        $response = [
            'success' => true,
        ];

        if ($this->message !== null) {
            $response['message'] = $this->message;
        }

        if ($this->data !== null) {
            $response['data'] = $this->data instanceof Arrayable
                ? $this->data->toArray()
                : $this->data;
        }

        if (!empty($this->meta)) {
            $response['meta'] = $this->meta;
        }

        return $response;
    }

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse(
            data: $this->toArray(),
            status: $this->statusCode,
            headers: $this->headers,
        );
    }
}