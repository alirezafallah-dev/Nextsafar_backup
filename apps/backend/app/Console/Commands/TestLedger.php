<?php

namespace App\Console\Commands;

use App\Modules\Ledger\Models\LedgerAccount;
use App\Modules\Ledger\Models\LedgerEntry;
use App\Modules\Ledger\Services\LedgerService;
use Illuminate\Console\Command;

class TestLedger extends Command
{
    protected $signature = 'test:ledger';
    protected $description = 'Test Ledger module';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     📒 Ledger Module Test Suite               ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        $tests = [
            'accounts_seeded' => $this->testAccountsSeeded(),
            'entry_code_generation' => $this->testEntryCode(),
            'manual_entry' => $this->testManualEntry(),
            'double_entry_balance' => $this->testDoubleEntryBalance(),
            'trial_balance' => $this->testTrialBalance(),
            'income_statement' => $this->testIncomeStatement(),
            'reversal' => $this->testReversal(),
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

    private function testAccountsSeeded(): bool
    {
        $count = LedgerAccount::count();
        $this->line("   Total accounts: {$count}");
        $this->line("   Assets: " . LedgerAccount::assets()->count());
        $this->line("   Liabilities: " . LedgerAccount::liabilities()->count());
        $this->line("   Revenues: " . LedgerAccount::revenues()->count());
        $this->line("   Expenses: " . LedgerAccount::expenses()->count());
        return $count >= 15;
    }

    private function testEntryCode(): bool
    {
        $code = LedgerEntry::generateUniqueCode();
        $valid = preg_match('/^LED-\d{6}-[A-Z0-9]{4}$/', $code) === 1;
        $this->line("   Generated: {$code}");
        return $valid;
    }

    private function testManualEntry(): bool
    {
        try {
            $service = app(LedgerService::class);
            
            $entry = $service->recordManualEntry([
                'type' => LedgerEntry::TYPE_MANUAL,
                'account_debit' => LedgerAccount::CODE_CASH,
                'account_credit' => LedgerAccount::CODE_TOUR_REVENUE,
                'amount' => 10000000,
                'currency' => 'IRR',
                'description' => 'Test manual entry',
            ]);
            
            $this->line("   Entry code: {$entry->code}");
            $this->line("   Batch ID: {$entry->batch_id}");
            
            // Cleanup
            $service->reverseEntry($entry, 'Test cleanup');
            
            return !empty($entry->code) && !empty($entry->batch_id);
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testDoubleEntryBalance(): bool
    {
        try {
            // تراز باید برقرار باشد: مجموع بدهکار = مجموع بستانکار
            $service = app(LedgerService::class);
            
            $entry1 = $service->recordManualEntry([
                'account_debit' => LedgerAccount::CODE_CASH,
                'account_credit' => LedgerAccount::CODE_TOUR_REVENUE,
                'amount' => 5000000,
                'description' => 'Balance test 1',
            ]);
            
            $entry2 = $service->recordManualEntry([
                'account_debit' => LedgerAccount::CODE_GATEWAY_FEES,
                'account_credit' => LedgerAccount::CODE_CASH,
                'amount' => 100000,
                'description' => 'Balance test 2',
            ]);
            
            $trialBalance = $service->getTrialBalance();
            
            $this->line("   Total Debit: " . number_format($trialBalance['total_debit']));
            $this->line("   Total Credit: " . number_format($trialBalance['total_credit']));
            $this->line("   Balanced: " . ($trialBalance['is_balanced'] ? 'Yes' : 'No'));
            
            // Cleanup
            $service->reverseEntry($entry1, 'cleanup');
            $service->reverseEntry($entry2, 'cleanup');
            
            return $trialBalance['is_balanced'];
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testTrialBalance(): bool
    {
        try {
            $service = app(LedgerService::class);
            $report = $service->getTrialBalance();
            
            $this->line("   Accounts with balance: " . count($report['accounts']));
            $this->line("   Is balanced: " . ($report['is_balanced'] ? 'Yes' : 'No'));
            
            return isset($report['total_debit']) && isset($report['total_credit']);
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testIncomeStatement(): bool
    {
        try {
            $service = app(LedgerService::class);
            $report = $service->getIncomeStatement();
            
            $this->line("   Total Revenue: " . number_format($report['total_revenue']));
            $this->line("   Total Expense: " . number_format($report['total_expense']));
            $this->line("   Net Income: " . number_format($report['net_income']));
            
            return isset($report['net_income']);
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testReversal(): bool
    {
        try {
            $service = app(LedgerService::class);
            
            $entry = $service->recordManualEntry([
                'account_debit' => LedgerAccount::CODE_CASH,
                'account_credit' => LedgerAccount::CODE_TOUR_REVENUE,
                'amount' => 3000000,
                'description' => 'Reversal test',
            ]);
            
            $reversal = $service->reverseEntry($entry, 'Testing reversal');
            
            $entry->refresh();
            
            $this->line("   Original is_reversed: " . ($entry->is_reversed ? 'Yes' : 'No'));
            $this->line("   Reversal code: {$reversal->code}");
            
            return $entry->is_reversed && $reversal->type === LedgerEntry::TYPE_REVERSAL;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }
}
