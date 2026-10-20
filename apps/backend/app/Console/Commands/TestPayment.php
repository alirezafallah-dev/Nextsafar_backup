<?php

namespace App\Console\Commands;

use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;
use Illuminate\Console\Command;

class TestPayment extends Command
{
    protected $signature = 'test:payment';
    protected $description = 'Test Payment module functionality';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     💳 Payment Module Test Suite              ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        $tests = [
            'payment_model' => $this->testPaymentModel(),
            'idempotency_key' => $this->testIdempotencyKey(),
            'payment_service' => $this->testPaymentService(),
            'statistics' => $this->testStatistics(),
            'event_system' => $this->testEventSystem(),
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

    private function testPaymentModel(): bool
    {
        try {
            $code = Payment::generateUniqueCode();
            $valid = preg_match('/^PMT-\d{6}-[A-Z0-9]{4}$/', $code) === 1;
            $this->line("   Generated code: {$code}");
            return $valid;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testIdempotencyKey(): bool
    {
        try {
            $key1 = Payment::generateIdempotencyKey(1, 10, null);
            $key2 = Payment::generateIdempotencyKey(1, 10, null);
            $key3 = Payment::generateIdempotencyKey(1, 11, null);
            
            $this->line("   Key length: " . strlen($key1));
            $this->line("   Same keys match: " . ($key1 === $key2 ? 'Yes' : 'No'));
            $this->line("   Different keys differ: " . ($key1 !== $key3 ? 'Yes' : 'No'));
            
            return $key1 === $key2 && $key1 !== $key3 && strlen($key1) === 64;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testPaymentService(): bool
    {
        try {
            $service = app(PaymentService::class);
            $gateway = $service->getGateway('zarinpal');
            
            $this->line("   Gateway loaded: " . ($gateway ? 'Yes' : 'No'));
            $this->line("   Gateway name: " . $gateway?->getName());
            
            return $gateway !== null && $gateway->getName() === 'zarinpal';
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testStatistics(): bool
    {
        try {
            $service = app(PaymentService::class);
            $stats = $service->getStatistics();
            
            $this->line("   Total payments: " . ($stats['total_payments'] ?? 0));
            $this->line("   Successful: " . ($stats['successful_count'] ?? 0));
            $this->line("   Today: " . ($stats['today_count'] ?? 0));
            
            return isset($stats['total_payments']) && isset($stats['successful_count']);
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testEventSystem(): bool
    {
        try {
            $this->line("   PaymentSuccessful event exists: " . (class_exists(\App\Modules\Payment\Events\PaymentSuccessful::class) ? 'Yes' : 'No'));
            $this->line("   PaymentFailed event exists: " . (class_exists(\App\Modules\Payment\Events\PaymentFailed::class) ? 'Yes' : 'No'));
            $this->line("   Listeners exist: " . (class_exists(\App\Modules\Payment\Listeners\SendPaymentSuccessNotification::class) ? 'Yes' : 'No'));
            
            return class_exists(\App\Modules\Payment\Events\PaymentSuccessful::class)
                && class_exists(\App\Modules\Payment\Events\PaymentFailed::class);
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }
}
