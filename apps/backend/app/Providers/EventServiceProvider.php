<?php

namespace App\Providers;

use App\Modules\Lead\Events\LeadCreated;
use App\Modules\Lead\Events\LeadStatusChanged;
use App\Modules\Order\Events\OrderCreated;
use App\Modules\Order\Events\OrderStatusChanged;
use App\Modules\Payment\Events\PaymentSuccessful;
use App\Modules\Payment\Events\PaymentFailed;
use App\Modules\Payment\Events\RefundCompleted;
use App\Modules\Payment\Listeners\LogPaymentEvent;
use App\Modules\Payment\Listeners\RecordPaymentInLedger;
use App\Modules\Visa\Events\VisaCaseCreated;
use App\Modules\Visa\Events\VisaCaseStatusChanged;
use App\Modules\Visa\Events\VisaDocumentVerified;
use App\Modules\Visa\Events\VisaCaseCompleted;
use App\Modules\Notification\Listeners\SendPaymentSuccessNotification;
use App\Modules\Notification\Listeners\SendRefundNotification;
use App\Modules\Notification\Listeners\SendVisaStatusNotification;
use App\Modules\Notification\Listeners\SendLeadNotification;
use App\Modules\Notification\Listeners\SendOrderNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // === Payment Events ===
        PaymentSuccessful::class => [
            LogPaymentEvent::class . '@handleSuccessful',
            RecordPaymentInLedger::class,
            SendPaymentSuccessNotification::class, // ✅ Notification
        ],
        PaymentFailed::class => [
            LogPaymentEvent::class . '@handleFailed',
        ],
        RefundCompleted::class => [
            SendRefundNotification::class, // ✅ Notification
        ],

        // === Visa Events ===
        VisaCaseCreated::class => [
            // می‌توانیم بعداً welcome notification اضافه کنیم
        ],
        VisaCaseStatusChanged::class => [
            SendVisaStatusNotification::class, // ✅ Notification
        ],
        VisaDocumentVerified::class => [
            // می‌توانیم بعداً document verification notification اضافه کنیم
        ],
        VisaCaseCompleted::class => [
            // می‌توانیم بعداً completion notification اضافه کنیم
        ],

        // === Lead Events ===
        LeadCreated::class => [
            SendLeadNotification::class . '@handleCreated', // ✅ Notification
        ],
        LeadStatusChanged::class => [
            SendLeadNotification::class . '@handleStatusChanged', // ✅ Notification
        ],

        // === Order Events ===
        OrderCreated::class => [
            SendOrderNotification::class . '@handleCreated',
        ],
        OrderStatusChanged::class => [
            SendOrderNotification::class . '@handleStatusChanged', // ✅ Notification
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
