<?php

namespace App\Modules\Notification\Services;

use App\Core\Logging\AppLogger;
use App\Exceptions\ApiException;
use App\Core\ApiErrorCode;
use App\Models\User;
use App\Modules\Notification\Enums\NotificationChannel;
use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Enums\NotificationPriority;
use App\Modules\Notification\Enums\NotificationStatus;
use App\Modules\Notification\Jobs\SendNotificationJob;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Models\NotificationLog;
use App\Modules\Notification\Models\NotificationTemplate;
use App\Modules\Notification\Repositories\NotificationRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * NotificationService - سرویس اصلی اطلاع‌رسانی
 * 
 * طبق بخش ۱۸ سند:
 * - ارسال از طریق queue (18.3)
 * - قالب‌های قابل مدیریت (18.2)
 * - پشتیبانی از چند کانال
 * - لاگ کامل
 */
class NotificationService
{
    public function __construct(
        protected NotificationRepository $repository,
        protected TemplateEngine $templateEngine,
    ) {}

    /**
     * ارسال notification بر اساس event
     * 
     * این متد اصلی است که از event listener ها فراخوانی می‌شود
     */
    public function sendForEvent(
        NotificationEvent $event,
        ?User $user = null,
        array $variables = [],
        ?Model $notifiable = null,
        ?NotificationPriority $priority = null,
    ): array {
        // دریافت کانال‌های پیش‌فرض برای این event
        $channels = $event->defaultChannels();
        $sentNotifications = [];

        foreach ($channels as $channel) {
            try {
                $notification = $this->send(
                    channel: $channel,
                    user: $user,
                    event: $event,
                    variables: $variables,
                    notifiable: $notifiable,
                    priority: $priority ?? NotificationPriority::NORMAL,
                );

                $sentNotifications[] = $notification;

            } catch (\Throwable $e) {
                AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->error('service.send_failed', [
                    'event' => $event->value,
                    'channel' => $channel->value,
                    'user_id' => $user?->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $sentNotifications;
    }

    /**
     * ارسال notification به یک کانال خاص
     */
    public function send(
        NotificationChannel $channel,
        ?User $user,
        NotificationEvent|string $event,
        array $variables = [],
        ?Model $notifiable = null,
        NotificationPriority $priority = NotificationPriority::NORMAL,
        ?\Carbon\Carbon $scheduledAt = null,
    ): Notification {
        $eventType = $event instanceof NotificationEvent ? $event->value : $event;

        return DB::transaction(function () use ($channel, $user, $eventType, $variables, $notifiable, $priority, $scheduledAt) {
            // 1. پیدا کردن قالب مناسب
            $template = NotificationTemplate::findForEvent(
                $eventType,
                $channel,
                $variables['locale'] ?? 'fa'
            );

            // 2. تعیین recipient
            $recipient = $this->resolveRecipient($channel, $user, $variables);

            if (empty($recipient)) {
                throw new ApiException(
                    errorCode: ApiErrorCode::VALIDATION_FAILED,
                    userMessage: 'گیرنده برای این کانال یافت نشد.',
                    httpStatus: 422,
                    details: ['channel' => $channel->value],
                );
            }

            // 3. آماده‌سازی محتوا
            [$subject, $content] = $this->prepareContent($template, $variables, $channel);

            // 4. ساخت notification
            $notification = Notification::create([
                'event_type' => $eventType,
                'channel' => $channel,
                'user_id' => $user?->id,
                'recipient' => $recipient,
                'recipient_name' => $user?->name ?? ($variables['recipient_name'] ?? null),
                'subject' => $subject,
                'content' => $content,
                'variables' => $variables,
                'template_id' => $template?->id,
                'status' => NotificationStatus::PENDING,
                'priority' => $priority,
                'metadata' => $variables['metadata'] ?? null,
                'notifiable_type' => $notifiable ? get_class($notifiable) : null,
                'notifiable_id' => $notifiable?->id,
                'scheduled_at' => $scheduledAt,
            ]);

            // 5. ثبت لاگ ایجاد
            NotificationLog::record(
                $notification,
                'created',
                'Notification created',
                ['event' => $eventType, 'channel' => $channel->value, 'recipient' => $recipient],
                null,
                null,
                null,
                auth()->user()?->name ?? 'system'
            );

            AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->info('service.notification_created', [
                'notification_id' => $notification->id,
                'code' => $notification->code,
                'event' => $eventType,
                'channel' => $channel->value,
                'recipient' => $recipient,
                'user_id' => $user?->id,
            ]);

            // 6. ارسال به queue (یا بلافاصله برای internal)
            if ($channel === NotificationChannel::INTERNAL) {
                // اعلان داخلی بلافاصله "ارسال" می‌شود
                $notification->markAsSent();
                NotificationLog::record($notification, 'sent', 'Internal notification stored');
            } else {
                // برای SMS و Email به queue می‌فرستیم
                SendNotificationJob::dispatch($notification->id)
                    ->delay($priority->queueDelay());
                
                $notification->markAsQueued();
                NotificationLog::record($notification, 'queued', 'Dispatched to queue');
            }

            return $notification;
        });
    }

    /**
     * ارسال مستقیم بدون قالب (برای موارد خاص)
     */
    public function sendDirect(
        NotificationChannel $channel,
        string $recipient,
        string $content,
        ?string $subject = null,
        ?User $user = null,
        NotificationPriority $priority = NotificationPriority::NORMAL,
    ): Notification {
        return DB::transaction(function () use ($channel, $recipient, $content, $subject, $user, $priority) {
            $notification = Notification::create([
                'event_type' => 'direct_message',
                'channel' => $channel,
                'user_id' => $user?->id,
                'recipient' => $recipient,
                'recipient_name' => $user?->name,
                'subject' => $subject,
                'content' => $content,
                'status' => NotificationStatus::PENDING,
                'priority' => $priority,
            ]);

            NotificationLog::record($notification, 'created', 'Direct notification created');

            if ($channel === NotificationChannel::INTERNAL) {
                $notification->markAsSent();
            } else {
                SendNotificationJob::dispatch($notification->id);
                $notification->markAsQueued();
            }

            return $notification;
        });
    }

    /**
     * ارسال SMS مستقیم (shortcut)
     */
    public function sendSms(string $phone, string $message, ?User $user = null): Notification
    {
        return $this->sendDirect(NotificationChannel::SMS, $phone, $message, null, $user);
    }

    /**
     * ارسال Email مستقیم (shortcut)
     */
    public function sendEmail(string $email, string $subject, string $body, ?User $user = null): Notification
    {
        return $this->sendDirect(NotificationChannel::EMAIL, $email, $body, $subject, $user);
    }

    /**
     * ارسال اعلان داخلی (shortcut)
     */
    public function sendInternal(User $user, string $title, string $body, array $options = []): Notification
    {
        $variables = array_merge($options, [
            'recipient_name' => $user->name,
        ]);

        return $this->sendDirect(
            NotificationChannel::INTERNAL,
            (string) $user->id,
            $body,
            $title,
            $user
        );
    }

    /**
     * لغو notification (فقط pending/queued)
     */
    public function cancel(Notification $notification): bool
    {
        if ($notification->isTerminal()) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'این اعلان در وضعیت پایانی است و قابل لغو نیست.',
                httpStatus: 422,
            );
        }

        $notification->markAsCancelled();
        NotificationLog::record($notification, 'cancelled', 'Cancelled by user/admin');

        return true;
    }

    /**
     * تلاش مجدد برای ارسال اعلان ناموفق
     */
    public function retry(Notification $notification): Notification
    {
        if (!$notification->canRetry()) {
            throw new ApiException(
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                userMessage: 'امکان تلاش مجدد برای این اعلان وجود ندارد.',
                httpStatus: 422,
            );
        }

        // ریست وضعیت
        $notification->update([
            'status' => NotificationStatus::PENDING,
            'attempts' => 0,
            'next_retry_at' => null,
            'error_message' => null,
        ]);

        // ارسال مجدد به queue
        SendNotificationJob::dispatch($notification->id);
        $notification->markAsQueued();

        NotificationLog::record($notification, 'queued', 'Retry dispatched');

        return $notification->fresh();
    }

    // =====================================================================
    // Private Helpers
    // =====================================================================

    /**
     * تعیین recipient بر اساس کانال
     */
    private function resolveRecipient(NotificationChannel $channel, ?User $user, array $variables): ?string
    {
        // اگر recipient مستقیم داده شده
        if (!empty($variables['recipient'])) {
            return $variables['recipient'];
        }

        if (!$user) {
            return null;
        }

        return match ($channel) {
            NotificationChannel::SMS => $user->phone,
            NotificationChannel::EMAIL => $user->email,
            NotificationChannel::INTERNAL => (string) $user->id,
            NotificationChannel::PUSH => $user->push_token ?? null,
        };
    }

    /**
     * آماده‌سازی محتوا از روی قالب
     */
    private function prepareContent(?NotificationTemplate $template, array $variables, NotificationChannel $channel): array
    {
        if (!$template) {
            // اگر قالب نیست، از محتوای مستقیم استفاده کن
            return [
                $variables['subject'] ?? null,
                $variables['content'] ?? $variables['message'] ?? '',
            ];
        }

        $subject = $template->subject ? $this->templateEngine->render($template->subject, $variables) : null;
        $content = $this->templateEngine->render($template->content, $variables);

        return [$subject, $content];
    }
}
