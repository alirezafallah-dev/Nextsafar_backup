<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            
            // === نوع دسترسی ===
            $table->enum('action', [
                'upload',      // آپلود
                'view',        // مشاهده
                'download',    // دانلود
                'delete',      // حذف
                'share',       // اشتراک‌گذاری
            ]);
            
            // === کاربر و IP ===
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            
            // === لینک زمان‌دار استفاده شده ===
            $table->string('token_used', 100)->nullable()
                ->comment('اگر با لینک زمان‌دار دسترسی گرفته شده');
            
            // === نتیجه ===
            $table->enum('result', ['success', 'denied', 'error'])->default('success');
            $table->string('reason', 255)->nullable();
            
            $table->timestamp('accessed_at')->useCurrent();
            
            $table->index(['document_id', 'accessed_at']);
            $table->index('user_id');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_access_logs');
    }
};
