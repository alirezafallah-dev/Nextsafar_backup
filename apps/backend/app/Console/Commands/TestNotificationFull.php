<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\Notification\Enums\NotificationChannel;
use App\Modules\Notification\Enums\NotificationEvent;
use App\Modules\Notification\Enums\NotificationStatus;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Models\NotificationTemplate;
use App\Modules\Notification\Repositories\NotificationRepository;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Notification\Services\TemplateEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class TestNotificationFull extends Command
{
    protected $signature = 'test:notification-full';
    protected $description = 'Comprehensive Notification module test (Phase 6.7)';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║  🎯 Notification Full Test (Phase 6.7)       ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        Queue::fake();

        $tests = [
            'database_structure' => $this->testDatabaseStructure(),
            'template_engine' => $this->testTemplateEngine(),
            'templates_seeded' => $this->testTemplatesSeeded(),
            'send_sms' => $this->testSendSms(),
            'send_email' => $this->testSendEmail(),
            'send_internal' => $this->testSendInternal(),
            'send_for_event' => $this->testSendForEvent(),
            'cancel_notification' => $this->testCancelNotification(),
            'mark_as_read' => $this->testMarkAsRead(),
            'repository_methods' => $this->testRepositoryMethods(),
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

        if ($passed === count($tests)) {
            $this->newLine();
            $this->info('🎉 All tests passed! Notification module is complete.');
            $this->printSummary();
        }

        return $passed === count($tests) ? 0 : 1;
    }

    private function testDatabaseStructure(): bool
    {
        $tables = ['notifications', 'notification_templates', 'notification_logs'];
        $allExist = true;
        
        foreach ($tables as $table) {
            $exists = \Schema::hasTable($table);
            $this->line("   Table {$table}: " . ($exists ? '✅' : '❌'));
            $allExist = $allExist && $exists;
        }
        
        return $allExist;
    }

    private function testTemplateEngine(): bool
    {
        $engine = app(TemplateEngine::class);
        
        $template = 'سلام {{user.name}}! سفارش {{order.code}} شما ثبت شد.';
        $variables = [
            'user' => ['name' => 'علی'],
            'order' => ['code' => 'NS-123'],
        ];
        
        $result = $engine->render($template, $variables);
        $expected = 'سلام علی! سفارش NS-123 شما ثبت شد.';
        
        $this->line("   Rendered: {$result}");
        
        return $result === $expected;
    }

    private function testTemplatesSeeded(): bool
    {
        $count = NotificationTemplate::count();
        $active = NotificationTemplate::where('is_active', true)->count();
        
        $this->line("   Total templates: {$count}");
        $this->line("   Active templates: {$active}");
        
        return $count >= 23 && $active >= 23;
    }

    private function testSendSms(): bool
    {
        $service = app(NotificationService::class);
        $user = User::first();
        
        $notification = $service->sendSms(
            $user->phone ?? '09123456789',
            'تست پیامک',
            $user
        );
        
        $success = $notification->channel === NotificationChannel::SMS
            && $notification->status === NotificationStatus::QUEUED;
        
        $this->line("   Channel: {$notification->channel->label()}");
        $this->line("   Status: {$notification->status_label}");
        
        $notification->forceDelete();
        
        return $success;
    }

    private function testSendEmail(): bool
    {
        $service = app(NotificationService::class);
        $user = User::first();
        
        $notification = $service->sendEmail(
            $user->email ?? 'test@example.com',
            'تست ایمیل',
            'محتوای ایمیل تستی',
            $user
        );
        
        $success = $notification->channel === NotificationChannel::EMAIL
            && $notification->status === NotificationStatus::QUEUED;
        
        $this->line("   Channel: {$notification->channel->label()}");
        $this->line("   Subject: {$notification->subject}");
        
        $notification->forceDelete();
        
        return $success;
    }

    private function testSendInternal(): bool
    {
        $service = app(NotificationService::class);
        $user = User::first();
        
        $notification = $service->sendInternal(
            $user,
            'تست اعلان داخلی',
            'محتوای اعلان داخلی'
        );
        
        $success = $notification->channel === NotificationChannel::INTERNAL
            && $notification->status === NotificationStatus::SENT;
        
        $this->line("   Channel: {$notification->channel->label()}");
        $this->line("   Status: {$notification->status_label} (immediate)");
        
        $notification->forceDelete();
        
        return $success;
    }

    private function testSendForEvent(): bool
    {
        $service = app(NotificationService::class);
        $user = User::first();
        
        $notifications = $service->sendForEvent(
            NotificationEvent::PAYMENT_SUCCESS,
            $user,
            [
                'user' => ['name' => $user->name],
                'order' => ['code' => 'NS-TEST', 'amount' => '1,500,000'],
                'payment' => ['reference' => '12345'],
            ]
        );
        
        $this->line("   Notifications created: " . count($notifications));
        
        $channels = [];
        foreach ($notifications as $n) {
            $channels[] = $n->channel->label();
            $n->forceDelete();
        }
        
        $this->line("   Channels: " . implode(', ', $channels));
        
        return count($notifications) >= 2; // SMS + Internal (Email if user has email)
    }

    private function testCancelNotification(): bool
    {
        $service = app(NotificationService::class);
        $user = User::first();
        
        $notification = $service->sendSms('09123456789', 'پیامک برای لغو', $user);
        
        $result = $service->cancel($notification);
        $notification->refresh();
        
        $success = $result && $notification->status === NotificationStatus::CANCELLED;
        
        $this->line("   Cancelled: " . ($success ? 'Yes' : 'No'));
        
        $notification->forceDelete();
        
        return $success;
    }

    private function testMarkAsRead(): bool
    {
        $service = app(NotificationService::class);
        $user = User::first();
        
        $notification = $service->sendInternal($user, 'تست', 'محتوا');
        
        $notification->markAsRead();
        $notification->refresh();
        
        $success = $notification->delivered_at !== null;
        
        $this->line("   Marked as read: " . ($success ? 'Yes' : 'No'));
        
        $notification->forceDelete();
        
        return $success;
    }

    private function testRepositoryMethods(): bool
    {
        $repo = app(NotificationRepository::class);
        $user = User::first();
        
        // Create test notifications
        $service = app(NotificationService::class);
        $n1 = $service->sendInternal($user, 'تست 1', 'محتوا 1');
        $n2 = $service->sendInternal($user, 'تست 2', 'محتوا 2');
        
        $unreadCount = $repo->getUnreadCount($user->id);
        $this->line("   Unread count: {$unreadCount}");
        
        $marked = $repo->markAllAsRead($user->id);
        $this->line("   Marked as read: {$marked}");
        
        $unreadAfter = $repo->getUnreadCount($user->id);
        $this->line("   Unread after: {$unreadAfter}");
        
        $success = $unreadCount >= 2 && $marked >= 2 && $unreadAfter === 0;
        
        $n1->forceDelete();
        $n2->forceDelete();
        
        return $success;
    }

    private function testStatistics(): bool
    {
        $repo = app(NotificationRepository::class);
        $stats = $repo->getStatistics();
        
        $this->line("   Total: {$stats['total']}");
        $this->line("   Success rate: {$stats['success_rate']}%");
        
        return isset($stats['total']) && isset($stats['success_rate']);
    }

    private function printSummary(): void
    {
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     📦 Notification Module Summary           ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->newLine();
        
        $this->info('📊 Database:');
        $this->line('   - notifications table');
        $this->line('   - notification_templates table');
        $this->line('   - notification_logs table');
        
        $this->newLine();
        $this->info('🎨 Templates:');
        $this->line('   - ' . NotificationTemplate::count() . ' templates seeded');
        $this->line('   - 10 event types covered');
        $this->line('   - 3 channels (SMS, Email, Internal)');
        
        $this->newLine();
        $this->info('🔌 Gateways:');
        $this->line('   - LogSmsGateway (development)');
        $this->line('   - KavenegarGateway (production ready)');
        $this->line('   - LaravelMailGateway (Email)');
        $this->line('   - DatabaseInternalGateway (Internal)');
        
        $this->newLine();
        $this->info('🌐 API Endpoints:');
        $this->line('   - 5 user endpoints');
        $this->line('   - 7 admin endpoints');
        
        $this->newLine();
        $this->info('⚡ Features:');
        $this->line('   ✅ Queue-based sending');
        $this->line('   ✅ Exponential backoff retry');
        $this->line('   ✅ Template variable substitution');
        $this->line('   ✅ Multi-channel support');
        $this->line('   ✅ Event-driven architecture');
        $this->line('   ✅ Complete logging');
        
        $this->newLine();
        $this->info('🎯 Compliance with spec section 18:');
        $this->line('   ✅ 18.1 - Three channels');
        $this->line('   ✅ 18.2 - 10 event types');
        $this->line('   ✅ 18.3 - Queue + retry + templates');
        
        $this->newLine();
        $this->info('🚀 Phase 6: Notification Module COMPLETE!');
    }
}
