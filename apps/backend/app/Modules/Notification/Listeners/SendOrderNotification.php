<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Order\Events\OrderCreated;
use App\Modules\Order\Events\OrderStatusChanged;
use App\Modules\Order\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderNotification implements ShouldQueue
{
    public $queue = 'notifications';
    public $tries = 3;

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handleCreated(OrderCreated $event): void
    {
        // Order created - فعلاً notification نمی‌فرستیم (در payment success می‌فرستیم)
    }

    public function handleStatusChanged(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $user = $order->user;

        if (!$user) {
            return;
        }

        // مپ کردن status سفارش به notification event
        $notificationEvent = $this->mapStatusToEvent($event->newStatus);
        
        if (!$notificationEvent) {
            return; // این وضعیت نیاز به notification ندارد
        }

        $variables = [
            'user' => [
                'name' => $user->name ?? 'کاربر',
            ],
            'order' => [
                'code' => $order->code,
                'amount' => number_format($order->total_amount),
                'type' => $order->type_label,
                'status' => $order->status_label,
            ],
        ];

        $this->notificationService->sendForEvent(
            $notificationEvent,
            $user,
            $variables,
            $order
        );
    }

    private function mapStatusToEvent(string $status): ?NotificationEvent
    {
        return match ($status) {
            Order::STATUS_CONFIRMED => NotificationEvent::ORDER_ISSUED,
            Order::STATUS_CANCELLED => NotificationEvent::ORDER_CANCELLED,
            default => null,
        };
    }
}
