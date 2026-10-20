<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            
            // === شناسه‌ها ===
            $table->uuid('uuid')->unique()->comment('شناسه عمومی (غیرقابل حدس)');
            $table->string('code', 30)->unique()->comment('کد نمایشی (DOC-YYMMDD-XXXX)');
            
            // === اطلاعات فایل ===
            $table->string('original_filename', 255)->comment('نام اصلی فایل');
            $table->string('stored_filename', 255)->comment('نام ذخیره‌شده (hash)');
            $table->string('path', 500)->comment('مسیر در storage');
            $table->string('disk', 50)->default('local')->comment('local, s3, minio');
            $table->string('mime_type', 100);
            $table->string('extension', 20);
            $table->unsignedBigInteger('size')->comment('حجم به بایت');
            $table->string('checksum', 64)->nullable()->comment('SHA-256 برای جلوگیری از تکرار');
            
            // === دسته‌بندی ===
            $table->enum('category', [
                'general',       // عمومی
                'visa',          // مدارک ویزا
                'hotel',         // تصاویر هتل
                'tour',          // تصاویر تور
                'article',       // تصاویر مقاله
                'invoice',       // فاکتور
                'voucher',       // ووچر
                'passport',      // پاسپورت
                'id_card',       // کارت ملی
                'photo',         // عکس شخصی
                'bank_statement',// پرینت حساب
                'insurance',     // بیمه
                'other',         // سایر
            ])->default('general');
            
            // === دید (عمومی/خصوصی) ===
            $table->enum('visibility', ['public', 'private', 'internal'])
                ->default('private')
                ->comment('public = همه، private = فقط مالک، internal = فقط ادمین');
            
            // === اتصال Polymorphic (متصل به هر موجودیتی) ===
            $table->string('documentable_type', 100)->nullable();
            $table->unsignedBigInteger('documentable_id')->nullable();
            
            // === مالک فایل ===
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            
            // === اطلاعات اضافی ===
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable()->comment('داده‌های اضافی (ابعاد تصویر، EXIF و...)');
            
            // === امنیت ===
            $table->boolean('is_verified')->default(false)->comment('اسکن/اعتبارسنجی شده');
            $table->string('scan_status', 30)->default('pending')
                ->comment('pending, clean, infected, invalid');
            $table->timestamp('verified_at')->nullable();
            
            // === آمار ===
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamp('last_downloaded_at')->nullable();
            
            // === Timestamps ===
            $table->timestamps();
            $table->softDeletes();
            
            // === Indexes ===
            $table->index(['documentable_type', 'documentable_id']);
            $table->index(['user_id', 'category']);
            $table->index('visibility');
            $table->index('checksum');
            $table->index('category');
            $table->index('scan_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
