<?php

namespace App\Modules\Payment\Listeners;

use App\Modules\Payment\Events\PaymentSuccessful;
use App\Modules\Ledger\Services\LedgerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Listener برای ثبت خودکار پرداخت در Ledger
 */
class RecordPaymentInLedger implements ShouldQueue
{
    public $queue = 'finance';
    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(
        protected LedgerService $ledgerService
    ) {}

    public function handle(PaymentSuccessful $event): void
    {
        try {
            $entry = $this->ledgerService->recordPaymentReceived($event->payment);

            Log::channel('payment')->info('Payment recorded in ledger', [
                'payment_code' => $event->payment->code,
                'ledger_entry_code' => $entry->code,
                'amount' => $entry->amount,
            ]);

        } catch (Throwable $e) {
            Log::channel('payment')->error('Failed to record payment in ledger', [
                'payment_code' => $event->payment->code,
                'error' => $e->getMessage(),
            ]);

            throw $e; // برای retry توسط queue
        }
    }

    public function failed(PaymentSuccessful $event, Throwable $exception): void
    {
        Log::channel('payment')->error('Payment ledger recording failed after all retries', [
            'payment_code' => $event->payment->code,
            'error' => $exception->getMessage(),
        ]);

        // TODO: در فاز بعدی، یک notification برای ادمین ارسال شود
    }
}
