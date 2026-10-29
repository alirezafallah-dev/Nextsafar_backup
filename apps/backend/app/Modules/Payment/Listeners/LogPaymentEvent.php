<?php

namespace App\Modules\Payment\Listeners;

use App\Modules\Payment\Events\PaymentSuccessful;
use App\Modules\Payment\Events\PaymentFailed;
use Illuminate\Support\Facades\Log;

class LogPaymentEvent
{
    public function handleSuccessful(PaymentSuccessful $event): void
    {
        Log::channel('payment')->info('Payment successful', [
            'payment_id' => $event->payment->id,
            'payment_code' => $event->payment->code,
            'amount' => $event->payment->amount,
            'user_id' => $event->payment->user_id,
            'order_id' => $event->payment->order_id,
            'reference_id' => $event->payment->reference_id,
        ]);
    }

    public function handleFailed(PaymentFailed $event): void
    {
        Log::channel('payment')->warning('Payment failed', [
            'payment_id' => $event->payment->id,
            'payment_code' => $event->payment->code,
            'amount' => $event->payment->amount,
            'reason' => $event->reason,
        ]);
    }
}
