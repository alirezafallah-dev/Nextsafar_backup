<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            
            // === Sync Info ===
            $table->string('sync_type', 50)->comment('نوع همگام‌سازی: hotel, article, etc');
            $table->string('event_type', 50)->comment('نوع رویداد: created, updated, deleted, etc');
            $table->unsignedBigInteger('wp_post_id')->nullable()->comment('شناسه پست وردپرس');
            $table->unsignedBigInteger('laravel_record_id')->nullable()->comment('شناسه رکورد لاراول');
            
            // === Status ===
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'retrying'])
                ->default('pending');
            $table->unsignedTinyInteger('attempt_count')->default(0)->comment('تعداد تلاش‌ها');
            $table->unsignedTinyInteger('max_attempts')->default(3)->comment('حداکثر تلاش‌ها');
            
            // === Timing ===
            $table->timestamp('received_at')->nullable()->comment('زمان دریافت وب‌هوک');
            $table->timestamp('processed_at')->nullable()->comment('زمان پردازش');
            $table->timestamp('next_retry_at')->nullable()->comment('زمان تلاش بعدی');
            
            // === Data ===
            $table->string('content_version', 100)->nullable()->comment('نسخه داده');
            $table->json('payload')->nullable()->comment('داده ارسالی');
            $table->json('response')->nullable()->comment('پاسخ پردازش');
            
            // === Error Info ===
            $table->text('error_message')->nullable()->comment('پیام خطا');
            $table->text('error_trace')->nullable()->comment('Trace خطا');
            
            // === Metadata ===
            $table->string('webhook_signature', 255)->nullable()->comment('امضای وب‌هوک');
            $table->string('ip_address', 45)->nullable()->comment('آی‌پی ارسال‌کننده');
            $table->json('metadata')->nullable()->comment('متادیتای اضافی');
            
            $table->timestamps();
            
            // === Indexes ===
            $table->index(['sync_type', 'status']);
            $table->index('wp_post_id');
            $table->index('laravel_record_id');
            $table->index('next_retry_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
