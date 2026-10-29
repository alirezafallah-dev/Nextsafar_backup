<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Lead\Events\LeadCreated;
use App\Modules\Lead\Events\LeadStatusChanged;
use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLeadNotification implements ShouldQueue
{
    public $queue = 'notifications';
    public $tries = 3;

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handleCreated(LeadCreated $event): void
    {
        $lead = $event->lead;
        $user = $lead->user;

        if (!$user) {
            // اگر کاربر نداشت، از phone استفاده می‌کنیم
            $this->notificationService->sendForEvent(
                NotificationEvent::LEAD_CREATED,
                null,
                [
                    'recipient' => $lead->phone,
                    'user' => ['name' => $lead->full_name ?? 'کاربر'],
                    'lead' => [
                        'code' => $lead->code,
                        'type' => $lead->type_label ?? $lead->type,
                    ],
                ],
                $lead
            );
            return;
        }

        $this->notificationService->sendForEvent(
            NotificationEvent::LEAD_CREATED,
            $user,
            [
                'user' => ['name' => $user->name],
                'lead' => [
                    'code' => $lead->code,
                    'type' => $lead->type_label ?? $lead->type,
                ],
            ],
            $lead
        );
    }

    public function handleStatusChanged(LeadStatusChanged $event): void
    {
        $lead = $event->lead;
        $user = $lead->user;

        if (!$user) {
            return;
        }

        $this->notificationService->sendForEvent(
            NotificationEvent::LEAD_FOLLOW_UP,
            $user,
            [
                'user' => ['name' => $user->name],
                'lead' => [
                    'code' => $lead->code,
                    'status' => $event->newStatus,
                ],
            ],
            $lead
        );
    }
}
