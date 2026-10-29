<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // === شناسه ===
            $table->uuid('uuid')->unique();
            $table->string('code', 30)->unique()->comment('NOT-YYMMDD-XXXX');
            
            // === اطلاعات اصلی ===
            $table->string('event_type', 50)->comment('payment_success, visa_status_changed, ...');
            $table->enum('channel', ['sms', 'email', 'internal', 'push'])->default('sms');
            
            // === گیرنده ===
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient', 255)->comment('phone or email');
            $table->string('recipient_name', 150)->nullable();
            
            // === محتوا ===
            $table->string('subject', 255)->nullable();
            $table->text('content');
            $table->json('variables')->nullable()->comment('متغیرهای قالب');
            $table->foreignId('template_id')->nullable()->constrained('notification_templates')->nullOnDelete();
            
            // === وضعیت (بخش ۱۸.۳) ===
            $table->enum('status', [
                'pending',          // در صف
                'queued',           // به queue رفت
                'processing',       // در حال پردازش
                'sent',             // ارسال شد
                'delivered',        // تحویل داده شد (برای SMS)
                'failed',           // ناموفق
                'retrying',         // در تلاش مجدد
                'cancelled',        // لغو شده
            ])->default('pending');
            
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('max_attempts')->default(3);
            $table->timestamp('next_retry_at')->nullable();
            
            // === نتیجه ارسال ===
            $table->string('provider_message_id', 100)->nullable();
            $table->string('provider_status', 50)->nullable();
            $table->text('error_message')->nullable();
            $table->text('provider_response')->nullable();
            
            // === اولویت ===
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // === متادیتا ===
            $table->json('metadata')->nullable();
            
            // === اتصال به موجودیت‌ها (polymorphic) ===
            $table->string('notifiable_type', 100)->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();
            
            // === Timestamps ===
            $table->timestamp('scheduled_at')->nullable()->comment('زمان‌بندی ارسال');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // === Indexes ===
            $table->index(['user_id', 'status']);
            $table->index(['channel', 'status']);
            $table->index('event_type');
            $table->index('created_at');
            $table->index('scheduled_at');
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
