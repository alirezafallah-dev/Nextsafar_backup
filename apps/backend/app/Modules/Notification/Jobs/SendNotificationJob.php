<?php

namespace App\Modules\Notification\Jobs;

use App\Core\Logging\AppLogger;
use App\Modules\Notification\Enums\NotificationChannel;
use App\Modules\Notification\Enums\NotificationStatus;
use App\Modules\Notification\Gateways\GatewayFactory;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Models\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\RateLimited;
use Throwable;

/**
 * SendNotificationJob - ارسال notification از طریق queue
 * 
 * طبق بخش ۱۸.۳ سند:
 * - ارسال‌ها باید از طریق صف انجام شوند
 * - retry با exponential backoff
 * - لاگ کامل
 */
class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * تعداد تلاش مجدد
     */
    public int $tries = 3;

    /**
     * Timeout به ثانیه
     */
    public int $timeout = 60;

    /**
     * Exponential backoff (30s, 5m, 30m)
     */
    public array $backoff = [30, 300, 1800];

    public function __construct(
        protected int $notificationId
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Get the middleware the job should pass through
     */
    public function middleware(): array
    {
        return [new RateLimited('notifications')];
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        $notification = Notification::find($this->notificationId);

        if (!$notification) {
            AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->warning('job.notification_not_found', [
                'notification_id' => $this->notificationId,
            ]);
            return;
        }

        // اگر از قبل ارسال شده، رد کن
        if ($notification->isTerminal()) {
            return;
        }

        // تغییر وضعیت به processing
        $notification->markAsProcessing();

        // ثبت لاگ شروع
        NotificationLog::record(
            $notification,
            'processing',
            "Attempt {$notification->attempts} of {$notification->max_attempts}",
            null,
            null,
            null,
            null,
            'job'
        );

        try {
            $result = match ($notification->channel) {
                NotificationChannel::SMS => $this->sendSms($notification),
                NotificationChannel::EMAIL => $this->sendEmail($notification),
                NotificationChannel::INTERNAL => $this->sendInternal($notification),
                default => ['success' => false, 'error' => 'Unsupported channel'],
            };

            if ($result['success']) {
                $notification->markAsSent($result['message_id'] ?? null);

                NotificationLog::record(
                    $notification,
                    'sent',
                    'Notification sent successfully',
                    $result['payload'] ?? null,
                    $result['raw_response'] ?? null,
                    $result['message_id'] ?? null,
                    $result['duration_ms'] ?? null,
                    'gateway'
                );

                AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->info('job.notification_sent', [
                    'notification_id' => $notification->id,
                    'code' => $notification->code,
                    'channel' => $notification->channel->value,
                    'message_id' => $result['message_id'] ?? null,
                    'attempts' => $notification->attempts,
                ]);
            } else {
                throw new \RuntimeException($result['error'] ?? 'Unknown error');
            }

        } catch (Throwable $e) {
            $this->handleFailure($notification, $e);
            throw $e; // برای retry توسط queue
        }
    }

    /**
     * ارسال پیامک
     */
    private function sendSms(Notification $notification): array
    {
        $gateway = GatewayFactory::sms();
        $startTime = microtime(true);

        $result = $gateway->send(
            $notification->recipient,
            $notification->content,
            $notification->metadata ?? []
        );

        $result['duration_ms'] = (int) ((microtime(true) - $startTime) * 1000);
        $result['payload'] = [
            'to' => $notification->recipient,
            'message' => $notification->content,
        ];

        return $result;
    }

    /**
     * ارسال ایمیل
     */
    private function sendEmail(Notification $notification): array
    {
        $gateway = GatewayFactory::email();
        $startTime = microtime(true);

        $result = $gateway->send(
            $notification->recipient,
            $notification->subject ?? 'Notification',
            $notification->content,
            array_merge($notification->metadata ?? [], [
                'html_body' => $notification->metadata['html_body'] ?? null,
            ])
        );

        $result['duration_ms'] = (int) ((microtime(true) - $startTime) * 1000);
        $result['payload'] = [
            'to' => $notification->recipient,
            'subject' => $notification->subject,
        ];

        return $result;
    }

    /**
     * ارسال اعلان داخلی
     */
    private function sendInternal(Notification $notification): array
    {
        // اعلان داخلی قبلاً در دیتابیس ذخیره شده
        // فقط وضعیت را آپدیت می‌کنیم
        return [
            'success' => true,
            'message_id' => (string) $notification->id,
            'duration_ms' => 0,
        ];
    }

    /**
     * مدیریت خطا
     */
    private function handleFailure(Notification $notification, Throwable $e): void
    {
        $willRetry = $this->attempts() < $this->tries;
        $notification->markAsFailed($e->getMessage(), $willRetry);

        NotificationLog::record(
            $notification,
            $willRetry ? 'retrying' : 'failed',
            $e->getMessage(),
            null,
            null,
            null,
            null,
            'system'
        );

        AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->error('job.notification_failed', [
            'notification_id' => $notification->id,
            'code' => $notification->code,
            'attempt' => $this->attempts(),
            'max_attempts' => $this->tries,
            'will_retry' => $willRetry,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Called when job fails after all retries
     */
    public function failed(Throwable $exception): void
    {
        $notification = Notification::find($this->notificationId);
        
        if ($notification && !$notification->isTerminal()) {
            $notification->markAsFailed($exception->getMessage(), false);

            AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->error('job.final_failure', [
                'notification_id' => $notification->id,
                'code' => $notification->code,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
