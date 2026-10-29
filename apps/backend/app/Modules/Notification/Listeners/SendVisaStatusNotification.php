<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Visa\Events\VisaCaseStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVisaStatusNotification implements ShouldQueue
{
    public $queue = 'notifications';
    public $tries = 3;

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(VisaCaseStatusChanged $event): void
    {
        $case = $event->visaCase;
        $user = $case->user;

        if (!$user) {
            return;
        }

        $variables = [
            'user' => [
                'name' => $user->name ?? 'کاربر',
            ],
            'visa' => [
                'country' => $case->visaType?->country?->name ?? 'نامشخص',
                'type' => $case->visaType?->category?->label() ?? 'نامشخص',
                'status' => $event->newStatus->label(),
                'case_code' => $case->code,
            ],
        ];

        $this->notificationService->sendForEvent(
            NotificationEvent::VISA_STATUS_CHANGED,
            $user,
            $variables,
            $case
        );
    }
}
