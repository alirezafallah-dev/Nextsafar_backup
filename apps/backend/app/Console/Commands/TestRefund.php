<?php

namespace App\Console\Commands;

use App\Modules\Payment\Models\Refund;
use App\Modules\Payment\Services\RefundService;
use Illuminate\Console\Command;

class TestRefund extends Command
{
    protected $signature = 'test:refund';
    protected $description = 'Test Refund module';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     💸 Refund Module Test Suite               ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        $tests = [
            'refund_model' => $this->testRefundModel(),
            'status_transitions' => $this->testStatusTransitions(),
            'refund_service' => $this->testRefundService(),
            'penalty_calculation' => $this->testPenaltyCalculation(),
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

    private function testRefundModel(): bool
    {
        try {
            $code = Refund::generateUniqueCode();
            $valid = preg_match('/^REF-\d{6}-[A-Z0-9]{4}$/', $code) === 1;
            $this->line("   Generated code: {$code}");
            $this->line("   Statuses: " . count(Refund::ALL_STATUSES));
            return $valid && count(Refund::ALL_STATUSES) === 7;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testStatusTransitions(): bool
    {
        try {
            $refund = Refund::create([
                'payment_id' => null,
                'user_id' => null,
                'type' => Refund::TYPE_FULL,
                'amount' => 1000000,
                'original_amount' => 1000000,
                'currency' => 'IRR',
                'reason' => 'Test',
            ]);

            // registered -> reviewing (OK)
            $canReview = $refund->canTransitionTo(Refund::STATUS_REVIEWING);
            // registered -> completed (NOT OK)
            $canComplete = $refund->canTransitionTo(Refund::STATUS_COMPLETED);

            $this->line("   Can go to reviewing: " . ($canReview ? 'Yes' : 'No'));
            $this->line("   Can go to completed: " . ($canComplete ? 'Yes' : 'No'));

            $refund->forceDelete();

            return $canReview && !$canComplete;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testRefundService(): bool
    {
        try {
            $service = app(RefundService::class);
            $this->line("   Service loaded: Yes");
            $this->line("   Has LedgerService: " . (property_exists($service, 'ledgerService') ? 'Yes' : 'No'));
            return true;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testPenaltyCalculation(): bool
    {
        try {
            $service = app(RefundService::class);

            $refund = Refund::create([
                'payment_id' => null,
                'user_id' => null,
                'type' => Refund::TYPE_PENALTY,
                'amount' => 1000000,
                'original_amount' => 1000000,
                'currency' => 'IRR',
                'reason' => 'Test',
            ]);

            $calc = $service->calculatePenalty($refund, 20); // 20% penalty

            $this->line("   Original: " . number_format($calc['original_amount']));
            $this->line("   Penalty %: {$calc['penalty_percentage']}");
            $this->line("   Penalty: " . number_format($calc['penalty_amount']));
            $this->line("   Refund: " . number_format($calc['refund_amount']));

            $correct = $calc['penalty_amount'] === 200000 && $calc['refund_amount'] === 800000;

            $refund->forceDelete();

            return $correct;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testStatistics(): bool
    {
        try {
            $service = app(RefundService::class);
            $stats = $service->getStatistics();

            $this->line("   Total refunds: " . ($stats['total_refunds'] ?? 0));
            $this->line("   Pending: " . ($stats['pending_count'] ?? 0));
            $this->line("   Completed: " . ($stats['completed_count'] ?? 0));

            return isset($stats['total_refunds']) && isset($stats['by_status']);
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }
}
