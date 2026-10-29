<?php

namespace App\Modules\Notification\Gateways\Internal;

use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Enums\NotificationChannel;
use App\Modules\Notification\Enums\NotificationStatus;

/**
 * DatabaseInternalGateway - ذخیره اعلان داخلی در دیتابیس
 */
class DatabaseInternalGateway implements InternalGatewayInterface
{
    public function getName(): string
    {
        return 'database';
    }

    public function send(int $userId, string $title, string $body, array $options = []): array
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'channel' => NotificationChannel::INTERNAL,
            'event_type' => $options['event_type'] ?? 'internal_notification',
            'recipient' => (string) $userId,
            'recipient_name' => $options['recipient_name'] ?? null,
            'subject' => $title,
            'content' => $body,
            'variables' => $options['variables'] ?? null,
            'template_id' => $options['template_id'] ?? null,
            'status' => NotificationStatus::SENT, // اعلان داخلی بلافاصله ارسال می‌شود
            'sent_at' => now(),
            'metadata' => array_merge($options['metadata'] ?? [], [
                'type' => $options['type'] ?? 'info', // info, success, warning, error
                'icon' => $options['icon'] ?? null,
                'action_url' => $options['action_url'] ?? null,
            ]),
            'notifiable_type' => $options['notifiable_type'] ?? null,
            'notifiable_id' => $options['notifiable_id'] ?? null,
        ]);

        return [
            'success' => true,
            'notification_id' => $notification->id,
            'error' => null,
        ];
    }

    public function markAsRead(int $notificationId): bool
    {
        return Notification::where('id', $notificationId)
            ->where('channel', NotificationChannel::INTERNAL)
            ->update(['delivered_at' => now()]) > 0;
    }

    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('channel', NotificationChannel::INTERNAL)
            ->whereNull('delivered_at')
            ->update(['delivered_at' => now()]);
    }

    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('channel', NotificationChannel::INTERNAL)
            ->whereNull('delivered_at')
            ->count();
    }
}
