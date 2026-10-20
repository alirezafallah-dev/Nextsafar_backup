<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->cascadeOnDelete();
            
            // === نوع لاگ ===
            $table->enum('event', [
                'created',      // ایجاد
                'queued',       // رفتن به صف
                'processing',   // شروع پردازش
                'sent',         // ارسال به provider
                'delivered',    // تحویل
                'failed',       // خطا
                'retrying',     // تلاش مجدد
                'cancelled',    // لغو
            ]);
            
            // === جزئیات ===
            $table->text('message')->nullable();
            $table->json('payload')->nullable()->comment('داده ارسال شده به provider');
            $table->json('response')->nullable()->comment('پاسخ از provider');
            $table->string('provider_message_id', 100)->nullable();
            
            // === مدت زمان ===
            $table->unsignedInteger('duration_ms')->nullable();
            
            // === کاربر/سیستم ===
            $table->string('actor', 100)->default('system');
            
            $table->timestamp('logged_at')->useCurrent();
            
            $table->index(['notification_id', 'logged_at']);
            $table->index('event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
