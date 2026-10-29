<?php

namespace App\Console\Commands;

use App\Modules\Lead\Models\Lead;
use App\Modules\Lead\Models\LeadNote;
use App\Modules\Lead\Services\LeadService;
use Illuminate\Console\Command;

class TestLead extends Command
{
    protected $signature = 'test:lead';
    protected $description = 'Test Lead module';

    public function handle(LeadService $leadService): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     📋 Lead Module Test Suite                 ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        $tests = [
            'create_lead' => $this->testCreate($leadService),
            'change_status' => $this->testChangeStatus($leadService),
            'add_note' => $this->testAddNote($leadService),
            'duplicate_detection' => $this->testDuplicate($leadService),
            'phone_validation' => $this->testPhoneValidation($leadService),
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

    private function testCreate(LeadService $leadService): bool
    {
        try {
            $lead = $leadService->createFromPublicForm([
                'type' => Lead::TYPE_TOUR,
                'full_name' => 'تست کاربر',
                'phone' => '09121111111',
                'message' => 'درخواست تست تور',
                'source' => 'website',
                'destination' => 'ترکیه',
                'adults' => 2,
            ]);

            $this->line("   Created lead: {$lead->code}");
            $this->line("   Status: {$lead->status}");

            $result = $lead->status === Lead::STATUS_NEW && !empty($lead->code);

            $lead->forceDelete();

            return $result;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testChangeStatus(LeadService $leadService): bool
    {
        try {
            $lead = $leadService->createFromPublicForm([
                'type' => Lead::TYPE_HOTEL,
                'full_name' => 'تست وضعیت',
                'phone' => '0912' . rand(2000000, 9999999),
            ]);

            $lead = $leadService->changeStatus($lead, Lead::STATUS_REVIEWING, 'تست تغییر وضعیت');
            $historyCount = $lead->statusHistory()->count();

            $this->line("   Status changed to: {$lead->status}");
            $this->line("   History entries: {$historyCount}");

            $result = $lead->status === Lead::STATUS_REVIEWING && $historyCount >= 1;

            $lead->forceDelete();
            return $result;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testAddNote(LeadService $leadService): bool
    {
        try {
            $lead = $leadService->createFromPublicForm([
                'type' => Lead::TYPE_CONSULTATION,
                'full_name' => 'تست یادداشت',
                'phone' => '0912' . rand(2000000, 9999999),
            ]);

            $note = $leadService->addNote($lead, [
                'type' => LeadNote::TYPE_NOTE,
                'content' => 'این یک یادداشت تست است',
            ]);

            $this->line("   Note ID: {$note->id}");
            $this->line("   Note type: {$note->type}");

            $result = $note->content === 'این یک یادداشت تست است';

            $lead->forceDelete();
            return $result;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testDuplicate(LeadService $leadService): bool
    {
        try {
            $phone = '0912' . rand(2000000, 9999999);

            $lead1 = $leadService->createFromPublicForm([
                'type' => Lead::TYPE_TOUR,
                'full_name' => 'کاربر اصلی',
                'phone' => $phone,
            ]);

            $lead2 = $leadService->createFromPublicForm([
                'type' => Lead::TYPE_TOUR,
                'full_name' => 'کاربر دوم',
                'phone' => $phone,
            ]);

            $this->line("   First lead: {$lead1->code} (duplicate: " . ($lead1->is_duplicate ? 'Yes' : 'No') . ")");
            $this->line("   Second lead: {$lead2->code} (duplicate: " . ($lead2->is_duplicate ? 'Yes' : 'No') . ")");

            $result = !$lead1->is_duplicate && $lead2->is_duplicate && $lead2->duplicate_of === $lead1->id;

            $lead1->forceDelete();
            $lead2->forceDelete();
            return $result;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testPhoneValidation(LeadService $leadService): bool
    {
        try {
            $invalidPhones = ['12345', '02112345678', '0912', 'abc'];
            $allInvalid = true;

            foreach ($invalidPhones as $phone) {
                try {
                    $leadService->createFromPublicForm([
                        'type' => Lead::TYPE_CALL,
                        'full_name' => 'تست',
                        'phone' => $phone,
                    ]);
                    $allInvalid = false;
                    break;
                } catch (\Throwable $e) {
                    // Expected
                }
            }

            $this->line("   Invalid phones rejected: " . ($allInvalid ? 'Yes' : 'No'));

            $validFormats = [
                '09121234567' => '09121234567',
                '+989121234567' => '09121234567',
                '00989121234567' => '09121234567',
            ];

            $allNormalized = true;
            foreach ($validFormats as $input => $expected) {
                $lead = $leadService->createFromPublicForm([
                    'type' => Lead::TYPE_CALL,
                    'full_name' => 'تست',
                    'phone' => $input,
                ]);

                if ($lead->phone !== $expected) {
                    $allNormalized = false;
                }
                $lead->forceDelete();
            }

            $this->line("   Phone formats normalized: " . ($allNormalized ? 'Yes' : 'No'));

            return $allInvalid && $allNormalized;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }
}
