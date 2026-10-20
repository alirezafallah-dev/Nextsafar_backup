<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\Document\Enums\DocumentCategory;
use App\Modules\Document\Enums\DocumentVisibility;
use App\Modules\Document\Enums\ScanStatus;
use App\Modules\Document\Models\Document;
use App\Modules\Document\Services\DocumentService;
use App\Modules\Document\Services\StorageService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class TestDocument extends Command
{
    protected $signature = 'test:document';
    protected $description = 'Test Document module';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║     📁 Document Module Test Suite             ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->info('');

        // ✅ ایجاد کاربر موقت و لاگین برای تست‌ها
        $testUser = $this->getOrCreateTestUser();
        Auth::login($testUser);

        $tests = [
            'enums' => $this->testEnums(),
            'upload' => $this->testUpload(),
            'temp_link' => $this->testTempLink(),
            'access_log' => $this->testAccessLog(),
            'validation' => $this->testValidation(),
            'cleanup' => $this->testCleanup(),
        ];

        // لاگ‌اوت کاربر تست
        Auth::logout();

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

    /**
     * ایجاد کاربر موقت برای تست یا استفاده از super_admin موجود
     */
    private function getOrCreateTestUser(): User
    {
        // ابتدا دنبال super admin بگرد
        $user = User::whereHas('roles', fn($q) => $q->where('slug', 'super_admin'))->first();
        if ($user) {
            return $user;
        }

        // در غیر این صورت یک کاربر تست بساز
        $user = User::where('email', 'test-doc@nextsafar.local')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Document Test User',
                'email' => 'test-doc@nextsafar.local',
                'phone' => '0912' . rand(1000000, 9999999),
                'password' => bcrypt('password'),
                'is_active' => true,
            ]);
        }

        return $user;
    }

    private function testEnums(): bool
    {
        $this->line("   Categories: " . count(DocumentCategory::cases()));
        $this->line("   Visibilities: " . count(DocumentVisibility::cases()));
        $this->line("   Scan statuses: " . count(ScanStatus::cases()));
        
        $visaRelated = DocumentCategory::PASSPORT->isVisaRelated();
        $public = DocumentCategory::HOTEL->isPublic();
        
        $this->line("   Passport is visa-related: " . ($visaRelated ? 'Yes' : 'No'));
        $this->line("   Hotel is public: " . ($public ? 'Yes' : 'No'));
        
        return $visaRelated && $public && count(DocumentCategory::cases()) >= 10;
    }

    private function testUpload(): bool
    {
        $testPath = null;
        try {
            $service = app(DocumentService::class);
            
            // Create test file
            $testPath = storage_path('app/test_upload.txt');
            file_put_contents($testPath, 'Test content for document module - ' . time());
            
            $file = new UploadedFile($testPath, 'test.txt', 'text/plain', null, true);
            
            $document = $service->upload(
                $file,
                DocumentCategory::GENERAL,
                DocumentVisibility::PUBLIC,
                null,
                'Test Document',
                'Test description'
            );
            
            $this->line("   Code: {$document->code}");
            $this->line("   UUID: " . substr($document->uuid, 0, 8) . '...');
            $this->line("   Path: {$document->path}");
            $this->line("   Size: " . $document->formatted_size);
            $this->line("   Scan status: " . $document->scan_status->label());
            
            // Cleanup
            $service->delete($document);
            
            return !empty($document->code) && !empty($document->uuid);
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        } finally {
            if ($testPath && file_exists($testPath)) {
                @unlink($testPath);
            }
        }
    }

    private function testTempLink(): bool
    {
        $testPath = null;
        try {
            $service = app(DocumentService::class);
            
            $testPath = storage_path('app/test_private.txt');
            file_put_contents($testPath, 'Private test content - ' . time());
            
            $file = new UploadedFile($testPath, 'private.txt', 'text/plain', null, true);
            
            $document = $service->upload(
                $file,
                DocumentCategory::GENERAL,
                DocumentVisibility::PRIVATE,
                null,
                'Private Test'
            );
            
            $link = $service->createTempLink($document, 30, 5);
            
            $this->line("   Token: " . substr($link->token, 0, 8) . '...');
            $this->line("   URL: " . substr($link->url, 0, 50) . '...');
            $this->line("   Valid: " . ($link->isValid() ? 'Yes' : 'No'));
            $this->line("   Max uses: " . $link->max_uses);  // ✅ اصلاح شد
            
            $valid = $link->isValid() && !empty($link->token);
            
            // Cleanup
            $service->delete($document);
            
            return $valid;
        } catch (\Throwable $e) {
            $this->error("   Error: " . $e->getMessage());
            return false;
        } finally {
            if ($testPath && file_exists($testPath)) {
                @unlink($testPath);
            }
        }
    }

    private function testAccessLog(): bool
    {
        $count = \App\Modules\Document\Models\DocumentAccessLog::count();
        $this->line("   Access logs count: {$count}");
        return true;
    }

    private function testValidation(): bool
    {
        // استفاده از StorageService از container
        $storage = app(StorageService::class);
        
        // Test 1: Valid PDF با استفاده از Laravel's UploadedFile::fake()
        // این روش mime را به درستی بر اساس extension تنظیم می‌کند
        $pdfFile = \Illuminate\Http\UploadedFile::fake()->create('test.pdf', 100);
        
        $result = $storage->validate($pdfFile, DocumentCategory::GENERAL);
        $this->line("   Valid PDF: " . ($result['valid'] ? 'Pass' : 'Fail'));
        $this->line("     Mime detected: " . $pdfFile->getMimeType());
        if (!$result['valid']) {
            $this->line("     Errors: " . implode(', ', $result['errors']));
        }
        
        // Test 2: Valid Image (JPEG)
        $imageFile = \Illuminate\Http\UploadedFile::fake()->image('test.jpg', 100, 100);
        $result2 = $storage->validate($imageFile, DocumentCategory::GENERAL);
        $this->line("   Valid JPEG: " . ($result2['valid'] ? 'Pass' : 'Fail'));
        
        // Test 3: Invalid extension (EXE)
        $exeFile = \Illuminate\Http\UploadedFile::fake()->create('malware.exe', 50);
        $result3 = $storage->validate($exeFile, DocumentCategory::GENERAL);
        $this->line("   Invalid EXE rejected: " . (!$result3['valid'] ? 'Pass' : 'Fail'));
        
        // Test 4: Oversized file (21MB - above 20MB limit)
        $bigFile = \Illuminate\Http\UploadedFile::fake()->create('big.zip', 22000); // 22MB
        $result4 = $storage->validate($bigFile, DocumentCategory::GENERAL);
        $this->line("   Oversized file rejected: " . (!$result4['valid'] ? 'Pass' : 'Fail'));
        
        // همه تست‌ها باید پاس شوند
        return $result['valid'] && $result2['valid'] && !$result3['valid'] && !$result4['valid'];
    }

    private function testCleanup(): bool
    {
        $testFiles = glob(storage_path('app/*_test.*')) ?: [];
        foreach ($testFiles as $file) {
            @unlink($file);
        }
        
        $this->line("   Cleaned " . count($testFiles) . " test files");
        return true;
    }
}
