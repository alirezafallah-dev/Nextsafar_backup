<?php

namespace App\Modules\Notification\Gateways\Internal;

interface InternalGatewayInterface
{
    public function send(int $userId, string $title, string $body, array $options = []): array;
    public function markAsRead(int $notificationId): bool;
    public function markAllAsRead(int $userId): int;
    public function getUnreadCount(int $userId): int;
    public function getName(): string;
}
