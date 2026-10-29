<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\Lead\Models\Lead;
use App\Modules\Notification\Enums\NotificationChannel;
use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Models\Notification;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\Payment;
use App\Modules\Visa\Enums\VisaCaseStatus;
use App\Modules\Visa\Models\VisaCase;
use App\Modules\Visa\Models\VisaType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;

class TestNotificationIntegration extends Command
{
    protected $signature = 'test:notification-integration';
    protected $description = 'Test notification integration with existing modules (Phase 6.5)';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║  🔗 Notification Integration (Phase 6.5)     ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        // پاک کردن notifications قبلی برای تست
        Notification::query()->delete();

        $tests = [
            'payment_success_event' => $this->testPaymentSuccess(),
            'visa_status_change_event' => $this->testVisaStatusChange(),
            'lead_created_event' => $this->testLeadCreated(),
            'order_status_change_event' => $this->testOrderStatusChange(),
        ];

        $this->newLine();
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║            📊 RESULTS                         ║');
        $this->info('╚══════════════════════════════════════════════╝');

        $passed = 0;
        foreach ($tests as $test => $result) {
            if ($result) {
                $this->info("  ✅ {$test}");
                $passed++;
            } else {
                $this->error("  ❌ {$test}");
            }
        }

        $this->newLine();
        $this->info("Passed: {$passed}/" . count($tests));

        // نمایش خلاصه notifications ایجاد شده
        $this->newLine();
        $total = Notification::count();
        $this->info("📬 Total notifications created: {$total}");
        
        if ($total > 0) {
            $this->newLine();
            $this->info('Breakdown by event:');
            $grouped = Notification::selectRaw('event_type, count(*) as count')
                ->groupBy('event_type')
                ->get();
            foreach ($grouped as $g) {
                $this->line("  • {$g->event_type}: {$g->count}");
            }
        }

        // Cleanup
        Notification::query()->delete();

        return $passed === count($tests) ? 0 : 1;
    }

    private function testPaymentSuccess(): bool
    {
        try {
            $user = User::first();
            
            $payment = Payment::create([
                'code' => Payment::generateUniqueCode(),
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'gateway' => 'zarinpal',
                'amount' => 15000000,
                'currency' => 'IRR',
                'status' => 'successful',
                'reference_id' => 'TEST-REF-123',
            ]);

            // Dispatch event
            event(new \App\Modules\Payment\Events\PaymentSuccessful($payment));

            // بررسی اینکه notification ساخته شده
            $count = Notification::where('event_type', NotificationEvent::PAYMENT_SUCCESS->value)
                ->where('user_id', $user->id)
                ->count();

            $this->line("   Payment event dispatched");
            $this->line("   Notifications created: {$count}");

            $payment->forceDelete();

            return $count >= 2; // SMS + Internal (Email اگر email داشته باشد)
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testVisaStatusChange(): bool
    {
        try {
            $user = User::first();
            $visaType = VisaType::first();
            
            $case = VisaCase::create([
                'code' => VisaCase::generateUniqueCode(),
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'visa_type_id' => $visaType->id,
                'status' => VisaCaseStatus::DOCUMENTS_INCOMPLETE,
                'service_fee' => 5000000,
                'embassy_fee' => 3000000,
                'total_amount' => 8000000,
            ]);

            // Dispatch status change event
            event(new \App\Modules\Visa\Events\VisaCaseStatusChanged(
                $case,
                VisaCaseStatus::DOCUMENTS_INCOMPLETE,
                VisaCaseStatus::DOCUMENTS_COMPLETE,
                'مدارک کامل شد'
            ));

            $count = Notification::where('event_type', NotificationEvent::VISA_STATUS_CHANGED->value)
                ->where('user_id', $user->id)
                ->count();

            $this->line("   Visa status change event dispatched");
            $this->line("   Notifications created: {$count}");

            $case->forceDelete();

            return $count >= 2; // SMS + Internal
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testLeadCreated(): bool
    {
        try {
            $lead = Lead::create([
                'type' => Lead::TYPE_TOUR,
                'full_name' => 'تست کاربر',
                'phone' => '0912' . rand(1000000, 9999999),
                'destination' => 'ترکیه',
                'message' => 'درخواست تست',
            ]);

            // Dispatch event
            event(new \App\Modules\Lead\Events\LeadCreated($lead));

            $count = Notification::where('event_type', NotificationEvent::LEAD_CREATED->value)
                ->count();

            $this->line("   Lead created event dispatched");
            $this->line("   Notifications created: {$count}");

            $lead->forceDelete();

            return $count >= 1; // حداقل SMS (چون user نداشتیم)
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testOrderStatusChange(): bool
    {
        try {
            $user = User::first();
            
            $order = Order::create([
                'code' => Order::generateUniqueCode(),
                'type' => Order::TYPE_TOUR,
                'user_id' => $user->id,
                'status' => Order::STATUS_PENDING_PAYMENT,
                'total_amount' => 20000000,
            ]);

            // Dispatch status change to confirmed
            event(new \App\Modules\Order\Events\OrderStatusChanged(
                $order,
                Order::STATUS_PENDING_PAYMENT,
                Order::STATUS_CONFIRMED,
                'سفارش صادر شد'
            ));

            $count = Notification::where('event_type', NotificationEvent::ORDER_ISSUED->value)
                ->where('user_id', $user->id)
                ->count();

            $this->line("   Order status change event dispatched");
            $this->line("   Notifications created: {$count}");

            $order->forceDelete();

            return $count >= 2; // SMS + Email + Internal
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }
}
