<?php

namespace App\Console\Commands;

use App\Models\Hotel;
use App\Models\SyncLog;
use App\Modules\WordPress\DTOs\HotelSyncDTO;
use App\Modules\WordPress\Services\HotelSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestWebhook extends Command
{
    protected $signature = 'webhook:test {scenario=all : Test scenario (hotel-created|hotel-updated|hotel-deleted|duplicate|all)}';
    protected $description = 'Test WordPress webhook sync scenarios';

    public function handle(HotelSyncService $syncService): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     🔗 Webhook Integration Test Suite        ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        $scenario = $this->argument('scenario');

        return match ($scenario) {
            'hotel-created' => $this->testHotelCreated($syncService),
            'hotel-updated' => $this->testHotelUpdated($syncService),
            'hotel-deleted' => $this->testHotelDeleted($syncService),
            'duplicate' => $this->testDuplicate($syncService),
            'all' => $this->runAllTests($syncService),
            default => $this->error("Unknown scenario: {$scenario}") ?? 1,
        };
    }

    private function runAllTests(HotelSyncService $syncService): int
    {
        $results = [];
        
        $results['hotel-created'] = $this->testHotelCreated($syncService, false);
        $results['hotel-updated'] = $this->testHotelUpdated($syncService, false);
        $results['hotel-deleted'] = $this->testHotelDeleted($syncService, false);
        $results['duplicate'] = $this->testDuplicate($syncService, false);
        
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║            📊 FINAL RESULTS                  ║');
        $this->info('╚══════════════════════════════════════════════╝');
        
        $passed = 0;
        $failed = 0;
        
        foreach ($results as $test => $status) {
            if ($status === 0) {
                $this->info("  ✅ {$test}: PASSED");
                $passed++;
            } else {
                $this->error("  ❌ {$test}: FAILED");
                $failed++;
            }
        }
        
        $this->newLine();
        $this->info("Total: " . count($results) . " | Passed: {$passed} | Failed: {$failed}");
        
        return $failed > 0 ? 1 : 0;
    }

    /**
     * Test 1: Hotel Created
     */
    private function testHotelCreated(HotelSyncService $syncService, bool $showHeader = true): int
    {
        if ($showHeader) {
            $this->info('═══════════════════════════════════════════════');
            $this->info('TEST: Hotel Created Event');
            $this->info('═══════════════════════════════════════════════');
        } else {
            $this->newLine();
            $this->warn('▶ Test 1: Hotel Created');
        }

        $wpPostId = 10000 + rand(1, 9999);
        $uniqueSlug = 'test-hotel-' . Str::random(8);
        
        $payload = [
            'event' => 'created',
            'wp_post_id' => $wpPostId,
            'content_version' => 'v1-' . time(),
            'slug' => $uniqueSlug,
            'name' => 'هتل تست ایجاد ' . $wpPostId,
            'publish_status' => 'published',
            'city' => 'تهران',
            'country' => 'ایران',
            'address' => 'خیابان ولیعصر',
            'short_description' => 'توضیحات کوتاه',
            'full_description' => '<p>توضیحات کامل</p>',
            'latitude' => 35.6892,
            'longitude' => 51.3890,
            'rating' => 4.5,
            'review_count' => 100,
            'seo_title' => 'SEO Title',
            'seo_description' => 'SEO Description',
            'tags' => ['test', 'hotel'],
            'categories' => ['5star'],
            'facilities' => ['wifi', 'pool'],
        ];

        try {
            $syncLog = SyncLog::create([
                'sync_type' => 'hotel',
                'event_type' => 'created',
                'wp_post_id' => $wpPostId,
                'status' => 'pending',
                'received_at' => now(),
                'payload' => $payload,
            ]);

            $dto = HotelSyncDTO::fromWebhookPayload($payload);
            $hotel = $syncService->handleCreated($dto, $syncLog);
            
            // Verify
            $this->line("   Hotel ID: <info>{$hotel->id}</info>");
            $this->line("   Name: <info>{$hotel->name}</info>");
            $this->line("   Slug: <info>{$hotel->slug}</info>");
            $this->line("   WP Post ID: <info>{$hotel->wp_post_id}</info>");
            $this->line("   Sync Status: <info>{$hotel->sync_status}</info>");
            $this->line("   Sync Log Status: <info>{$syncLog->fresh()->status}</info>");
            
            if ($hotel->sync_status === 'synced' && $syncLog->fresh()->status === 'success') {
                $this->info("   ✅ Hotel Created Successfully");
                return 0;
            }
            
            $this->error("   ❌ Sync status not as expected");
            return 1;
            
        } catch (\Throwable $e) {
            $this->error("   ❌ Exception: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Test 2: Hotel Updated
     */
    private function testHotelUpdated(HotelSyncService $syncService, bool $showHeader = true): int
    {
        if ($showHeader) {
            $this->info('═══════════════════════════════════════════════');
            $this->info('TEST: Hotel Updated Event');
            $this->info('═══════════════════════════════════════════════');
        } else {
            $this->newLine();
            $this->warn('▶ Test 2: Hotel Updated');
        }

        // First create a hotel
        $wpPostId = 20000 + rand(1, 9999);
        $uniqueSlug = 'test-hotel-update-' . Str::random(8);
        
        $createPayload = [
            'wp_post_id' => $wpPostId,
            'slug' => $uniqueSlug,
            'name' => 'هتل اولیه ' . $wpPostId,
            'publish_status' => 'published',
            'city' => 'اصفهان',
        ];

        try {
            // Create initial hotel
            $initialSyncLog = SyncLog::create([
                'sync_type' => 'hotel',
                'event_type' => 'created',
                'wp_post_id' => $wpPostId,
                'status' => 'pending',
                'received_at' => now(),
                'payload' => $createPayload,
            ]);
            
            $dto = HotelSyncDTO::fromWebhookPayload($createPayload);
            $hotel = $syncService->handleCreated($dto, $initialSyncLog);
            $initialName = $hotel->name;
            
            // Now update it
            $updatePayload = array_merge($createPayload, [
                'event' => 'updated',
                'content_version' => 'v2-' . time(),
                'name' => 'هتل بروزرسانی شده ' . $wpPostId,
                'city' => 'شیراز',
                'rating' => 4.8,
            ]);
            
            $updateSyncLog = SyncLog::create([
                'sync_type' => 'hotel',
                'event_type' => 'updated',
                'wp_post_id' => $wpPostId,
                'status' => 'pending',
                'received_at' => now(),
                'payload' => $updatePayload,
            ]);
            
            $updateDto = HotelSyncDTO::fromWebhookPayload($updatePayload);
            $updatedHotel = $syncService->handleUpdated($updateDto, $updateSyncLog);
            
            $this->line("   Initial Name: <info>{$initialName}</info>");
            $this->line("   Updated Name: <info>{$updatedHotel->name}</info>");
            $this->line("   Updated City: <info>{$updatedHotel->city}</info>");
            $this->line("   Updated Rating: <info>{$updatedHotel->rating}</info>");
            $this->line("   Hotel ID (same): <info>{$updatedHotel->id}</info>");
            
            if ($updatedHotel->name !== $initialName 
                && $updatedHotel->city === 'شیراز' 
                && $updateSyncLog->fresh()->status === 'success') {
                $this->info("   ✅ Hotel Updated Successfully");
                return 0;
            }
            
            $this->error("   ❌ Update did not apply correctly");
            return 1;
            
        } catch (\Throwable $e) {
            $this->error("   ❌ Exception: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Test 3: Hotel Deleted (soft delete)
     */
    private function testHotelDeleted(HotelSyncService $syncService, bool $showHeader = true): int
    {
        if ($showHeader) {
            $this->info('═══════════════════════════════════════════════');
            $this->info('TEST: Hotel Deleted Event (Soft Delete)');
            $this->info('═══════════════════════════════════════════════');
        } else {
            $this->newLine();
            $this->warn('▶ Test 3: Hotel Deleted (Soft Delete)');
        }

        $wpPostId = 30000 + rand(1, 9999);
        $uniqueSlug = 'test-hotel-delete-' . Str::random(8);
        
        $createPayload = [
            'wp_post_id' => $wpPostId,
            'slug' => $uniqueSlug,
            'name' => 'هتل برای حذف ' . $wpPostId,
            'publish_status' => 'published',
        ];

        try {
            // Create
            $syncLog = SyncLog::create([
                'sync_type' => 'hotel',
                'event_type' => 'created',
                'wp_post_id' => $wpPostId,
                'status' => 'pending',
                'received_at' => now(),
                'payload' => $createPayload,
            ]);
            $dto = HotelSyncDTO::fromWebhookPayload($createPayload);
            $hotel = $syncService->handleCreated($dto, $syncLog);
            $hotelId = $hotel->id;
            
            // Delete
            $deleteSyncLog = SyncLog::create([
                'sync_type' => 'hotel',
                'event_type' => 'deleted',
                'wp_post_id' => $wpPostId,
                'status' => 'pending',
                'received_at' => now(),
                'payload' => ['event' => 'deleted', 'wp_post_id' => $wpPostId],
            ]);
            
            $deletedHotel = $syncService->handleDeleted($wpPostId, $deleteSyncLog);
            
            // Verify soft delete
            $stillExists = Hotel::withTrashed()->find($hotelId);
            $trashed = Hotel::onlyTrashed()->find($hotelId);
            
            $this->line("   Original Hotel ID: <info>{$hotelId}</info>");
            $this->line("   Still exists (with trashed): " . ($stillExists ? '<info>Yes</info>' : '<error>No</error>'));
            $this->line("   Is trashed: " . ($trashed ? '<info>Yes</info>' : '<error>No</error>'));
            $this->line("   Publish Status: <info>{$stillExists->publish_status}</info>");
            $this->line("   Display Status: <info>{$stillExists->display_status}</info>");
            
            if ($stillExists && $trashed && $deleteSyncLog->fresh()->status === 'success') {
                $this->info("   ✅ Hotel Soft Deleted Successfully (data preserved)");
                return 0;
            }
            
            $this->error("   ❌ Soft delete did not work correctly");
            return 1;
            
        } catch (\Throwable $e) {
            $this->error("   ❌ Exception: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Test 4: Duplicate Version Skip
     */
    private function testDuplicate(HotelSyncService $syncService, bool $showHeader = true): int
    {
        if ($showHeader) {
            $this->info('═══════════════════════════════════════════════');
            $this->info('TEST: Duplicate Version Skipped');
            $this->info('═══════════════════════════════════════════════');
        } else {
            $this->newLine();
            $this->warn('▶ Test 4: Duplicate Version Skip');
        }

        $wpPostId = 40000 + rand(1, 9999);
        $uniqueSlug = 'test-hotel-dup-' . Str::random(8);
        $contentVersion = 'v-duplicate-' . time();
        
        $payload = [
            'wp_post_id' => $wpPostId,
            'slug' => $uniqueSlug,
            'name' => 'هتل تست تکرار ' . $wpPostId,
            'publish_status' => 'published',
            'content_version' => $contentVersion,
        ];

        try {
            // First sync
            $syncLog1 = SyncLog::create([
                'sync_type' => 'hotel',
                'event_type' => 'created',
                'wp_post_id' => $wpPostId,
                'status' => 'pending',
                'received_at' => now(),
                'payload' => $payload,
            ]);
            $dto = HotelSyncDTO::fromWebhookPayload($payload);
            $hotel = $syncService->handleCreated($dto, $syncLog1);
            $firstUpdatedAt = $hotel->updated_at;
            
            sleep(1); // wait 1 second
            
            // Second sync with SAME version
            $syncLog2 = SyncLog::create([
                'sync_type' => 'hotel',
                'event_type' => 'updated',
                'wp_post_id' => $wpPostId,
                'status' => 'pending',
                'received_at' => now(),
                'payload' => array_merge($payload, ['event' => 'updated']),
            ]);
            $dto2 = HotelSyncDTO::fromWebhookPayload(array_merge($payload, ['event' => 'updated']));
            $hotel2 = $syncService->handleUpdated($dto2, $syncLog2);
            
            $this->line("   First Updated At: <info>{$firstUpdatedAt}</info>");
            $this->line("   Second Updated At: <info>{$hotel2->updated_at}</info>");
            $this->line("   Sync Log 1 Status: <info>{$syncLog1->fresh()->status}</info>");
            $this->line("   Sync Log 2 Status: <info>{$syncLog2->fresh()->status}</info>");
            $this->line("   Sync Log 2 Response: <info>" . json_encode($syncLog2->fresh()->response) . "</info>");
            
            $skipped = $syncLog2->fresh()->response['action'] ?? null;
            if ($firstUpdatedAt->eq($hotel2->updated_at) && $skipped === 'skipped') {
                $this->info("   ✅ Duplicate Version Skipped Correctly");
                return 0;
            }
            
            $this->error("   ❌ Duplicate was not skipped");
            return 1;
            
        } catch (\Throwable $e) {
            $this->error("   ❌ Exception: " . $e->getMessage());
            return 1;
        }
    }
}
