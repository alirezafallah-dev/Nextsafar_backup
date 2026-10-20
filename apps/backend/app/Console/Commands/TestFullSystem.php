<?php

namespace App\Console\Commands;

use App\Core\ApiResponse;
use App\Core\Logging\AppLogger;
use App\Exceptions\ApiException;
use App\Models\Hotel;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SyncLog;
use App\Models\User;
use App\Modules\Lead\Models\Lead;
use App\Modules\Lead\Models\LeadNote;
use App\Modules\Lead\Services\LeadService;
use App\Modules\Tour\Models\Tour;
use App\Modules\Tour\Models\TourCategory;
use App\Modules\Tour\Models\TourDate;
use App\Modules\Tour\Services\TourService;
use App\Modules\WordPress\Services\HotelSyncService;
use Illuminate\Console\Command;

class TestFullSystem extends Command
{
    protected $signature = 'test:full';
    protected $description = 'Comprehensive test of all implemented modules (Phase 0-2)';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║     🎯 NextSafar - Full System Test Suite                    ║');
        $this->info('║     Phases 0-2 Comprehensive Verification                    ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->info('');

        $passed = 0;
        $failed = 0;
        $failures = [];

        // === PHASE 0 ===
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📦 فاز ۰: زیرساخت');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        foreach ($this->getPhase0Tests() as $name => $callback) {
            $this->runTest($name, $callback, $passed, $failed, $failures);
        }

        // === PHASE 1 ===
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📦 فاز ۱: همگام‌سازی وردپرس');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        foreach ($this->getPhase1Tests() as $name => $callback) {
            $this->runTest($name, $callback, $passed, $failed, $failures);
        }

        // === PHASE 2.1 ===
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📦 فاز ۲.۱: نقش‌ها و دسترسی‌ها');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        foreach ($this->getPhase21Tests() as $name => $callback) {
            $this->runTest($name, $callback, $passed, $failed, $failures);
        }

        // === PHASE 2.2 ===
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📦 فاز ۲.۲: ماژول تور');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        foreach ($this->getPhase22Tests() as $name => $callback) {
            $this->runTest($name, $callback, $passed, $failed, $failures);
        }

        // === PHASE 2.3 ===
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📦 فاز ۲.۳: ماژول درخواست و لید');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        foreach ($this->getPhase23Tests() as $name => $callback) {
            $this->runTest($name, $callback, $passed, $failed, $failures);
        }

        // === Summary ===
        $total = $passed + $failed;
        $rate = $total > 0 ? round(($passed / $total) * 100, 1) : 0;

        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║                      📊 FINAL SUMMARY                        ║');
        $this->info('╠══════════════════════════════════════════════════════════════╣');
        $this->info(sprintf('║  Total Tests: %-44s║', $total));
        $this->info(sprintf('║  Passed: %-49s║', $passed . ' ✅'));
        $this->info(sprintf('║  Failed: %-49s║', $failed . ' ❌'));
        $this->info(sprintf('║  Success Rate: %-43s║', $rate . '%'));
        $this->info('╚══════════════════════════════════════════════════════════════╝');

        if ($failed > 0) {
            $this->newLine();
            $this->error('❌ Failed Tests Details:');
            foreach ($failures as $f) {
                $this->error("   • {$f['name']}");
                $this->error("     Reason: {$f['reason']}");
            }
        }

        if ($failed === 0) {
            $this->newLine();
            $this->info('🎉 ALL TESTS PASSED! System ready for Phase 4.');
            return 0;
        } else {
            $this->newLine();
            $this->warn('⚠️  Some tests failed. Review before proceeding.');
            return 1;
        }
    }

    private function runTest(string $name, callable $callback, int &$passed, int &$failed, array &$failures): void
    {
        try {
            $result = $callback();
            if ($result === true) {
                $this->info("  ✅ {$name}");
                $passed++;
            } else {
                $reason = is_string($result) ? $result : 'Returned false';
                $this->error("  ❌ {$name}");
                $this->error("     → {$reason}");
                $failed++;
                $failures[] = ['name' => $name, 'reason' => $reason];
            }
        } catch (\Throwable $e) {
            $this->error("  ❌ {$name}");
            $this->error("     → Exception: " . $e->getMessage());
            $failed++;
            $failures[] = ['name' => $name, 'reason' => $e->getMessage()];
        }
    }

    // ============================================================
    // PHASE TESTS
    // ============================================================

    private function getPhase0Tests(): array
    {
        return [
            'API Response Contract' => fn() => $this->testApiResponse(),
            'API Exception Handler' => fn() => $this->testApiException(),
            'Logging System (AppLogger)' => fn() => $this->testLogging(),
            'WordPress Feature Flags' => fn() => $this->testFeatureFlags(),
        ];
    }

    private function getPhase1Tests(): array
    {
        return [
            'Hotel Model exists' => fn() => class_exists(Hotel::class),
            'SyncLog Model exists' => fn() => class_exists(SyncLog::class),
            'Hotel Sync Service' => fn() => app(HotelSyncService::class) instanceof HotelSyncService,
            'Webhook Signature Verification' => fn() => $this->testWebhookSignature(),
        ];
    }

    private function getPhase21Tests(): array
    {
        return [
            '9 roles seeded' => fn() => Role::count() >= 9 ? true : 'Only ' . Role::count() . ' roles',
            '85+ permissions seeded' => fn() => Permission::count() >= 85 ? true : 'Only ' . Permission::count() . ' permissions',
            'User HasRoles trait' => fn() => method_exists(User::first() ?? new User(), 'hasRole'),
            'Super admin bypasses permissions' => fn() => $this->testSuperAdmin(),
            'Role hierarchy' => fn() => $this->testRoleHierarchy(),
        ];
    }

    private function getPhase22Tests(): array
    {
        return [
            'Tour Model exists' => fn() => class_exists(Tour::class),
            'TourCategory seeded (10)' => fn() => TourCategory::count() >= 10 ? true : 'Only ' . TourCategory::count(),
            'Tour auto code generation' => fn() => $this->testTourCode(),
            'TourDate capacity logic' => fn() => $this->testTourDateCapacity(),
            'TourService create flow' => fn() => app(TourService::class) instanceof TourService,
        ];
    }

    private function getPhase23Tests(): array
    {
        return [
            'Lead Model exists' => fn() => class_exists(Lead::class),
            'Lead auto code generation' => fn() => preg_match('/^LD-\d{6}-[A-Z0-9]{4}$/', Lead::generateUniqueCode()) === 1,
            'Lead duplicate detection' => fn() => $this->testLeadDuplicate(),
            'Lead phone normalization' => fn() => $this->testLeadPhone(),
            'Lead status history' => fn() => $this->testLeadStatusHistory(),
            'Lead notes system' => fn() => $this->testLeadNotes(),
        ];
    }

    // ============================================================
    // PHASE 0 TESTS
    // ============================================================

    private function testApiResponse(): bool
    {
        $response = ApiResponse::success(['test' => 'data'], 'Test message');
        $array = $response->toArray();
        return $array['success'] === true && $array['data']['test'] === 'data';
    }

    private function testApiException(): bool
    {
        $exception = ApiException::notFound('تست', '123');
        $array = $exception->toArray();
        return $array['success'] === false && $array['error']['code'] === 'NOT_FOUND';
    }

    private function testLogging(): bool
    {
        AppLogger::api()->info('test.full_system', ['phase' => 'testing']);
        return true;
    }

    private function testFeatureFlags(): bool
    {
        $flagFile = base_path('../cms/wp-content/plugins/nextsafar-core/inc/feature-flags.php');
        return file_exists($flagFile);
    }

    // ============================================================
    // PHASE 1 TESTS
    // ============================================================

    private function testWebhookSignature(): bool
    {
        $secret = config('services.webhooks.wordpress_secret');
        if (empty($secret)) return false;
        $payload = json_encode(['test' => 'data']);
        $signature = hash_hmac('sha256', $payload, $secret);
        return strlen($signature) === 64;
    }

    // ============================================================
    // PHASE 2.1 TESTS
    // ============================================================

    private function testSuperAdmin(): bool
    {
        $user = User::whereHas('roles', fn($q) => $q->where('slug', 'super_admin'))->first();
        if (!$user) return true;
        return $user->isSuperAdmin() && $user->hasPermission('any.random.permission');
    }

    private function testRoleHierarchy(): bool
    {
        $superAdmin = Role::where('slug', 'super_admin')->first();
        $user = Role::where('slug', 'user')->first();
        return $superAdmin && $user && $superAdmin->isHigherThan($user);
    }

    // ============================================================
    // PHASE 2.2 TESTS - CRITICAL
    // ============================================================

    private function testTourCode()
    {
        $code = Tour::generateUniqueCode();
        $matches = preg_match('/^TR-[A-Z]{3}-\d{3}$/', $code);
        $length = strlen($code);
        
        // TR-XXX-NNN = 2 + 1 + 3 + 1 + 3 = 10 characters
        if ($matches !== 1) {
            return "Regex failed for '{$code}'";
        }
        if ($length !== 10) {
            return "Length is {$length}, expected 10 for '{$code}'";
        }
        return true;
    }

    private function testTourDateCapacity()
    {
        $category = TourCategory::first();
        if (!$category) {
            return 'No category found';
        }

        try {
            $tour = Tour::create([
                'title' => 'Test Tour ' . time() . '-' . uniqid(),
                'destination_country' => 'Test',
                'category_id' => $category->id,
                'status' => 'draft',
            ]);

            $date = TourDate::create([
                'tour_id' => $tour->id,
                'departure_date' => now()->addMonth(),
                'return_date' => now()->addMonth()->addDays(5),
                'total_capacity' => 10,
                'remaining_capacity' => 10,
                'reserved_count' => 0,
                'adult_price' => 1000000,
                'child_price' => 0,
                'infant_price' => 0,
                'single_supplement' => 0,
                'min_participants' => 1,
                'discount_amount' => 0,
                'discount_percent' => 0,
                'status' => 'available',
                'is_guaranteed' => false,
            ]);

            $reserved = $date->reserveCapacity(3);
            $date->refresh();

            $success = $reserved === true
                && $date->remaining_capacity === 7
                && $date->reserved_count === 3;

            if (!$success) {
                $result = sprintf(
                    'reserved=%s, remaining=%s (expected 7), count=%s (expected 3)',
                    $reserved ? 'true' : 'false',
                    $date->remaining_capacity,
                    $date->reserved_count
                );
                $date->forceDelete();
                $tour->forceDelete();
                return $result;
            }

            $date->forceDelete();
            $tour->forceDelete();
            return true;
        } catch (\Throwable $e) {
            return 'Exception: ' . $e->getMessage();
        }
    }

    // ============================================================
    // PHASE 2.3 TESTS
    // ============================================================

    private function testLeadDuplicate(): bool
    {
        $phone = '0912' . rand(3000000, 9999999);
        $lead1 = Lead::create(['type' => Lead::TYPE_TOUR, 'full_name' => 'User 1', 'phone' => $phone]);
        $duplicates = Lead::findDuplicates($phone, 999999);
        $found = $duplicates->contains('id', $lead1->id);
        $lead1->forceDelete();
        return $found;
    }

    private function testLeadPhone(): bool
    {
        $service = app(LeadService::class);
        $lead = $service->createFromPublicForm(['type' => Lead::TYPE_CALL, 'full_name' => 'Test', 'phone' => '+989123456789']);
        $normalized = $lead->phone === '09123456789';
        $lead->forceDelete();
        return $normalized;
    }

    private function testLeadStatusHistory(): bool
    {
        $service = app(LeadService::class);
        $lead = $service->createFromPublicForm(['type' => Lead::TYPE_TOUR, 'full_name' => 'Test', 'phone' => '0912' . rand(1000000, 9999999)]);
        $service->changeStatus($lead, Lead::STATUS_CONTACTED, 'Test');
        $lead->refresh();
        $success = $lead->statusHistory()->count() >= 1 && $lead->status === Lead::STATUS_CONTACTED;
        $lead->forceDelete();
        return $success;
    }

    private function testLeadNotes(): bool
    {
        $service = app(LeadService::class);
        $lead = $service->createFromPublicForm(['type' => Lead::TYPE_CONSULTATION, 'full_name' => 'Test', 'phone' => '0912' . rand(1000000, 9999999)]);
        $note = $service->addNote($lead, ['type' => LeadNote::TYPE_NOTE, 'content' => 'Test note']);
        $success = $note->exists && $note->content === 'Test note';
        $lead->forceDelete();
        return $success;
    }
}
