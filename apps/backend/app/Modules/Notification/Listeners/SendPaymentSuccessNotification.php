<?php

namespace App\Modules\Notification\Listeners;

use App\Core\Logging\AppLogger;
use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Payment\Events\PaymentSuccessful;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPaymentSuccessNotification implements ShouldQueue
{
    public $queue = 'notifications';
    public $tries = 3;

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(PaymentSuccessful $event): void
    {
        $payment = $event->payment;
        $user = $payment->user;

        if (!$user) {
            AppLogger::channel(\App\Core\Logging\LogChannel::NOTIFICATION)->warning('listener.no_user_for_payment', [
                'payment_id' => $payment->id,
            ]);
            return;
        }

        $variables = [
            'user' => [
                'name' => $user->name ?? 'کاربر',
                'phone' => $user->phone,
                'email' => $user->email,
            ],
            'order' => [
                'code' => $payment->order?->code ?? 'N/A',
                'amount' => number_format($payment->amount),
            ],
            'payment' => [
                'reference' => $payment->reference_id ?? $payment->code,
                'gateway' => $payment->gateway?->value ?? 'N/A',
            ],
        ];

        $this->notificationService->sendForEvent(
            NotificationEvent::PAYMENT_SUCCESS,
            $user,
            $variables,
            $payment
        );
    }
}
