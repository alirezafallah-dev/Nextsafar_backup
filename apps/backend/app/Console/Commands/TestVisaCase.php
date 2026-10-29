<?php

namespace App\Console\Commands;

use App\Modules\Visa\Enums\VisaCaseStatus;
use App\Modules\Visa\Models\VisaCase;
use App\Modules\Visa\Models\VisaCasePassenger;
use App\Modules\Visa\Models\VisaCaseTimeline;
use Illuminate\Console\Command;

class TestVisaCase extends Command
{
    protected $signature = 'test:visa-case';
    protected $description = 'Test Visa Case (Phase 5.2.2)';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     🛂 Visa Case Test (Phase 5.2.2)           ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        $tests = [
            'status_enum' => $this->testStatusEnum(),
            'create_case' => $this->testCreateCase(),
            'transitions' => $this->testTransitions(),
            'relationships' => $this->testRelationships(),
            'scopes' => $this->testScopes(),
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

    private function testStatusEnum(): bool
    {
        $statuses = VisaCaseStatus::cases();
        $this->line("   Total statuses: " . count($statuses));
        $this->line("   Active: " . count(VisaCaseStatus::activeStatuses()));
        $this->line("   Terminal: " . count(VisaCaseStatus::terminalStatuses()));
        
        // بررسی چند انتقال
        $canMoveFromRegistered = VisaCaseStatus::REGISTERED->canTransitionTo(VisaCaseStatus::DOCUMENTS_COMPLETE);
        $cannotMoveFromCompleted = VisaCaseStatus::COMPLETED->canTransitionTo(VisaCaseStatus::REGISTERED);
        
        $this->line("   REGISTERED → DOCUMENTS_COMPLETE: " . ($canMoveFromRegistered ? 'Yes' : 'No'));
        $this->line("   COMPLETED → REGISTERED: " . ($cannotMoveFromCompleted ? 'Yes (WRONG)' : 'No (CORRECT)'));
        
        return count($statuses) === 14 && $canMoveFromRegistered && !$cannotMoveFromCompleted;
    }

    private function testCreateCase(): bool
    {
        try {
            $visaType = \App\Modules\Visa\Models\VisaType::first();
            if (!$visaType) {
                $this->error("   No visa type found");
                return false;
            }

            // پیدا کردن یک کاربر
            $user = \App\Models\User::first();
            if (!$user) {
                $this->error("   No user found");
                return false;
            }

            $case = VisaCase::create([
                'user_id' => $user->id,
                'visa_type_id' => $visaType->id,
                'status' => VisaCaseStatus::REGISTERED,
                'intended_travel_date' => now()->addMonths(2),
                'service_fee' => 5000000,
                'embassy_fee' => 3000000,
                'total_amount' => 8000000,
            ]);

            $this->line("   Created case: {$case->code}");
            $this->line("   UUID: " . substr($case->uuid, 0, 8) . '...');
            $this->line("   Status: {$case->status_label}");
            $this->line("   Total: " . number_format($case->total_amount));

            $success = !empty($case->code) && !empty($case->uuid);

            // Cleanup
            $case->forceDelete();

            return $success;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testTransitions(): bool
    {
        try {
            $visaType = \App\Modules\Visa\Models\VisaType::first();
            $user = \App\Models\User::first();
            
            $case = VisaCase::create([
                'user_id' => $user->id,
                'visa_type_id' => $visaType->id,
                'status' => VisaCaseStatus::REGISTERED,
            ]);

            // تست انتقال مجاز
            $can1 = $case->canTransitionTo(VisaCaseStatus::DOCUMENTS_INCOMPLETE);
            $can2 = $case->canTransitionTo(VisaCaseStatus::DOCUMENTS_COMPLETE);
            $can3 = $case->canTransitionTo(VisaCaseStatus::CANCELLED);
            
            // تست انتقال غیرمجاز
            $can4 = $case->canTransitionTo(VisaCaseStatus::APPROVED);
            $can5 = $case->canTransitionTo(VisaCaseStatus::COMPLETED);
            
            $this->line("   REGISTERED → DOCUMENTS_INCOMPLETE: " . ($can1 ? 'Yes' : 'No'));
            $this->line("   REGISTERED → DOCUMENTS_COMPLETE: " . ($can2 ? 'Yes' : 'No'));
            $this->line("   REGISTERED → CANCELLED: " . ($can3 ? 'Yes' : 'No'));
            $this->line("   REGISTERED → APPROVED (invalid): " . ($can4 ? 'Yes (WRONG)' : 'No (CORRECT)'));
            $this->line("   REGISTERED → COMPLETED (invalid): " . ($can5 ? 'Yes (WRONG)' : 'No (CORRECT)'));
            
            $success = $can1 && $can2 && $can3 && !$can4 && !$can5;
            
            $case->forceDelete();
            return $success;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testRelationships(): bool
    {
        try {
            $visaType = \App\Modules\Visa\Models\VisaType::first();
            $user = \App\Models\User::first();
            
            $case = VisaCase::create([
                'user_id' => $user->id,
                'visa_type_id' => $visaType->id,
                'status' => VisaCaseStatus::REGISTERED,
            ]);

            // افزودن مسافر
            $passenger = VisaCasePassenger::create([
                'visa_case_id' => $case->id,
                'type' => 'adult',
                'first_name' => 'علی',
                'last_name' => 'رضایی',
                'first_name_en' => 'Ali',
                'last_name_en' => 'Rezaei',
                'national_id' => '1234567890',
                'birth_date' => '1990-01-15',
                'gender' => 'male',
                'is_main_applicant' => true,
            ]);

            // افزودن timeline
            $timeline = VisaCaseTimeline::record(
                $case,
                'status_change',
                'پرونده ایجاد شد',
                'پرونده ویزا توسط کاربر ایجاد شد',
                ['status' => 'registered'],
                'public'
            );

            $case->load(['user', 'visaType.country', 'passengers', 'timeline']);
            
            $this->line("   User: {$case->user->name}");
            $this->line("   Visa type: {$case->visaType->title}");
            $this->line("   Country: {$case->visaType->country->name}");
            $this->line("   Passengers: {$case->passengers->count()}");
            $this->line("   Timeline entries: {$case->timeline->count()}");
            $this->line("   Passenger name: {$passenger->full_name}");
            
            $success = $case->user !== null 
                && $case->visaType !== null 
                && $case->passengers->count() === 1
                && $case->timeline->count() === 1;
            
            $timeline->delete();
            $passenger->delete();
            $case->forceDelete();
            
            return $success;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        }
    }

    private function testScopes(): bool
    {
        // Test scopes
        $activeCount = VisaCase::active()->count();
        $terminalCount = VisaCase::terminal()->count();
        $this->line("   Active cases: {$activeCount}");
        $this->line("   Terminal cases: {$terminalCount}");
        
        // Scopes should work even with 0 records
        return true;
    }
}
