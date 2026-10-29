<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\Notification\Enums\NotificationChannel;
use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Enums\NotificationStatus;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Models\NotificationLog;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Notification\Services\TemplateEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class TestNotificationService extends Command
{
    protected $signature = 'test:notification-service';
    protected $description = 'Test Notification Service (Phase 6.3)';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║  📬 Notification Service Test (Phase 6.3)    ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        // Fake queue برای جلوگیری از ارسال واقعی
        Queue::fake();

        $tests = [
            'template_engine' => $this->testTemplateEngine(),
            'send_sms' => $this->testSendSms(),
            'send_email' => $this->testSendEmail(),
            'send_internal' => $this->testSendInternal(),
            'send_for_event' => $this->testSendForEvent(),
            'cancel_notification' => $this->testCancelNotification(),
            'statistics' => $this->testStatistics(),
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

        return $passed === count($tests) ? 0 : 1;
    }

    private function testTemplateEngine(): bool
    {
        try {
            $engine = app(TemplateEngine::class);
            
            $template = 'سلام {{user.name}}! سفارش شما با کد {{order.code}} ثبت شد.';
            $variables = [
                'user' => ['name' => 'علی'],
                'order' => ['code' => 'NS-261019-ABC1'],
            ];
            
            $result = $engine->render($template, $variables);
            
            $this->line("   Template: {$template}");
            $this->line("   Result: {$result}");
            
            $expected = 'سلام علی! سفارش شما با کد NS-261019-ABC1 ثبت شد.';
            $success = $result === $expected;
            
            // تست extract variables
            $vars = $engine->extractVariables($template);
            $this->line("   Extracted variables: " . implode(', ', $vars));
            
            return $success && in_array('user.name', $vars);
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testSendSms(): bool
    {
        try {
            $service = app(NotificationService::class);
            $user = User::first();
            
            $notification = $service->sendSms(
                $user->phone ?? '09123456789',
                'تست پیامک از NextSafar',
                $user
            );
            
            $this->line("   Code: {$notification->code}");
            $this->line("   Channel: {$notification->channel->label()}");
            $this->line("   Status: {$notification->status_label}");
            $this->line("   Recipient: {$notification->recipient}");
            
            $success = $notification->channel === NotificationChannel::SMS
                && $notification->status === NotificationStatus::QUEUED;
            
            // Cleanup
            $notification->forceDelete();
            
            return $success;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testSendEmail(): bool
    {
        try {
            $service = app(NotificationService::class);
            $user = User::first();
            
            $notification = $service->sendEmail(
                $user->email ?? 'test@example.com',
                'تست ایمیل',
                'این یک ایمیل تستی است',
                $user
            );
            
            $this->line("   Code: {$notification->code}");
            $this->line("   Channel: {$notification->channel->label()}");
            $this->line("   Subject: {$notification->subject}");
            
            $success = $notification->channel === NotificationChannel::EMAIL
                && $notification->status === NotificationStatus::QUEUED;
            
            $notification->forceDelete();
            
            return $success;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testSendInternal(): bool
    {
        try {
            $service = app(NotificationService::class);
            $user = User::first();
            
            $notification = $service->sendInternal(
                $user,
                'تست اعلان داخلی',
                'این یک اعلان داخلی است',
                ['type' => 'info', 'icon' => 'bell']
            );
            
            $this->line("   Code: {$notification->code}");
            $this->line("   Channel: {$notification->channel->label()}");
            $this->line("   Status: {$notification->status_label}");
            
            // Internal notification should be immediately sent
            $success = $notification->channel === NotificationChannel::INTERNAL
                && $notification->status === NotificationStatus::SENT;
            
            $notification->forceDelete();
            
            return $success;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testSendForEvent(): bool
    {
        try {
            $service = app(NotificationService::class);
            $user = User::first();
            
            // ارسال برای event PAYMENT_SUCCESS
            $notifications = $service->sendForEvent(
                NotificationEvent::PAYMENT_SUCCESS,
                $user,
                [
                    'user' => ['name' => $user->name],
                    'order' => ['code' => 'NS-261019-TEST', 'amount' => '1,500,000'],
                    'payment' => ['reference' => '12345'],
                ]
            );
            
            $this->line("   Notifications sent: " . count($notifications));
            foreach ($notifications as $n) {
                $this->line("     - {$n->channel->label()}: {$n->code}");
            }
            
            // PAYMENT_SUCCESS باید به 3 کانال برود: SMS, Email, Internal
            $success = count($notifications) >= 2; // حداقل 2 تا (اگر email تنظیم نشده باشد)
            
            // Cleanup
            foreach ($notifications as $n) {
                $n->forceDelete();
            }
            
            return $success;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testCancelNotification(): bool
    {
        try {
            $service = app(NotificationService::class);
            $user = User::first();
            
            $notification = $service->sendSms(
                $user->phone ?? '09123456789',
                'پیامک برای لغو',
                $user
            );
            
            $this->line("   Before cancel: {$notification->status_label}");
            
            $result = $service->cancel($notification);
            $notification->refresh();
            
            $this->line("   After cancel: {$notification->status_label}");
            
            $success = $result && $notification->status === NotificationStatus::CANCELLED;
            
            $notification->forceDelete();
            
            return $success;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testStatistics(): bool
    {
        try {
            $repo = app(\App\Modules\Notification\Repositories\NotificationRepository::class);
            $stats = $repo->getStatistics();
            
            $this->line("   Total: {$stats['total']}");
            $this->line("   Sent: {$stats['sent']}");
            $this->line("   Failed: {$stats['failed']}");
            $this->line("   Success rate: {$stats['success_rate']}%");
            
            return isset($stats['total']) && isset($stats['success_rate']);
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }
}
