<?php

namespace App\Modules\WordPress\Controllers;

use App\Core\ApiResponse;
use App\Core\Logging\AppLogger;
use App\Models\SyncLog;
use App\Modules\WordPress\DTOs\HotelSyncDTO;
use App\Modules\WordPress\Requests\HotelWebhookRequest;
use App\Modules\WordPress\Services\HotelSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Throwable;

/**
 * Webhook Controller for WordPress → Laravel sync
 * 
 * Endpoints:
 * - POST /api/v1/webhooks/wordpress/hotel
 */
class WebhookController extends Controller
{
    public function __construct(
        protected HotelSyncService $hotelSyncService,
    ) {}

    /**
     * Handle hotel webhook from WordPress
     */
    public function hotel(HotelWebhookRequest $request): JsonResponse
    {
        $payload = $request->validated();
        
        // Create sync log entry (قانون ۷.۷)
        $syncLog = SyncLog::create([
            'sync_type' => 'hotel',
            'event_type' => $payload['event'],
            'wp_post_id' => $payload['wp_post_id'],
            'status' => 'pending',
            'received_at' => now(),
            'payload' => $payload,
            'webhook_signature' => $request->header('X-Webhook-Signature'),
            'ip_address' => $request->ip(),
            'content_version' => $payload['content_version'] ?? null,
        ]);

        try {
            $result = match ($payload['event']) {
                'created' => $this->handleCreated($payload, $syncLog),
                'updated', 'published' => $this->handleUpdated($payload, $syncLog),
                'deleted', 'trashed' => $this->handleDeleted($payload, $syncLog),
                'drafted' => $this->handleDrafted($payload, $syncLog),
                default => throw new \InvalidArgumentException("Unknown event: {$payload['event']}"),
            };

            return ApiResponse::success([
                'sync_log_id' => $syncLog->id,
                'status' => 'success',
                'hotel_id' => $result?->id ?? null,
            ], 'وب‌هوک با موفقیت پردازش شد.')->toResponse($request);

        } catch (Throwable $e) {
            // قانون ۷.۶.۶: اگر خطا داشت، در صف تلاش مجدد قرار گیرد
            $syncLog->markAsFailed(
                $e->getMessage(),
                app()->hasDebugModeEnabled() ? $e->getTraceAsString() : null
            );

            AppLogger::sync()
                ->withException($e)
                ->error('webhook.hotel.processing_failed', [
                    'sync_log_id' => $syncLog->id,
                    'wp_post_id' => $payload['wp_post_id'],
                    'event' => $payload['event'],
                ]);

            // اگر قابل retry است، 202 Accepted برگردان (بعداً در Job پردازش می‌شود)
            if ($syncLog->canRetry()) {
                // TODO: dispatch ProcessFailedSyncJob
                return ApiResponse::accepted([
                    'sync_log_id' => $syncLog->id,
                    'status' => 'queued_for_retry',
                    'next_retry_at' => $syncLog->next_retry_at?->toIso8601String(),
                ], 'وب‌هوک برای تلاش مجدد در صف قرار گرفت.')->toResponse($request);
            }

            // در غیر این صورت، خطای 500 برگردان (حداکثر تلاش‌ها تمام شده)
            return ApiResponse::success([
                'sync_log_id' => $syncLog->id,
                'status' => 'failed_permanently',
                'error' => app()->hasDebugModeEnabled() ? $e->getMessage() : 'خطای داخلی',
            ], 'پردازش وب‌هوک با خطای دائمی مواجه شد.')->toResponse($request);
        }
    }

    protected function handleCreated(array $payload, SyncLog $syncLog)
    {
        $dto = HotelSyncDTO::fromWebhookPayload($payload);
        return $this->hotelSyncService->handleCreated($dto, $syncLog);
    }

    protected function handleUpdated(array $payload, SyncLog $syncLog)
    {
        $dto = HotelSyncDTO::fromWebhookPayload($payload);
        return $this->hotelSyncService->handleUpdated($dto, $syncLog);
    }

    protected function handleDeleted(array $payload, SyncLog $syncLog)
    {
        return $this->hotelSyncService->handleDeleted($payload['wp_post_id'], $syncLog);
    }

    protected function handleDrafted(array $payload, SyncLog $syncLog)
    {
        return $this->hotelSyncService->handleStatusChange(
            $payload['wp_post_id'],
            'draft',
            $syncLog
        );
    }

    /**
     * Health check endpoint for webhook monitoring
     */
    public function health(): JsonResponse
    {
        return ApiResponse::success([
            'status' => 'ok',
            'pending_syncs' => SyncLog::pending()->count(),
            'failed_syncs' => SyncLog::failed()->count(),
            'retryable_syncs' => SyncLog::retryable()->count(),
        ])->toResponse(request());
    }
}
