<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Payment\Events\RefundCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRefundNotification implements ShouldQueue
{
    public $queue = 'notifications';
    public $tries = 3;

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(RefundCompleted $event): void
    {
        $refund = $event->refund;
        $user = $refund->user;

        if (!$user) {
            return;
        }

        $variables = [
            'user' => [
                'name' => $user->name ?? 'کاربر',
            ],
            'order' => [
                'code' => $refund->order?->code ?? 'N/A',
                'amount' => number_format($refund->refund_amount ?? $refund->amount),
            ],
        ];

        $this->notificationService->sendForEvent(
            NotificationEvent::ORDER_REFUNDED,
            $user,
            $variables,
            $refund
        );
    }
}
