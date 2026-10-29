<?php

namespace App\Modules\Payment\Listeners;

use App\Modules\Payment\Events\PaymentSuccessful;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendPaymentSuccessNotification implements ShouldQueue
{
    public $queue = 'notifications';
    public $tries = 3;

    public function handle(PaymentSuccessful $event): void
    {
        $payment = $event->payment;

        Log::info('Payment successful notification queued', [
            'payment_id' => $payment->id,
            'payment_code' => $payment->code,
            'amount' => $payment->amount,
            'user_id' => $payment->user_id,
        ]);

        // TODO: در فاز بعدی (ماژول اطلاع‌رسانی) اینجا SMS و Email ارسال می‌شود
        // NotificationService::sendSms($payment->user->phone, 'payment_success', [...]);
        // NotificationService::sendEmail($payment->user->email, 'payment_success', [...]);
    }
}
