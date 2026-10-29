<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_case_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_case_id')->constrained('visa_cases')->cascadeOnDelete();
            
            // === نوع رویداد ===
            $table->enum('event_type', [
                'status_change',       // تغییر وضعیت
                'document_uploaded',   // آپلود مدرک
                'document_verified',   // تأیید مدرک
                'document_rejected',   // رد مدرک
                'note_added',          // افزودن یادداشت
                'passenger_added',     // افزودن مسافر
                'passenger_updated',   // ویرایش مسافر
                'payment_received',    // دریافت پرداخت
                'appointment_booked',  // رزرو وقت
                'embassy_submitted',   // ارسال به سفارت
                'biometrics_done',     // انجام انگشت‌نگاری
                'result_received',     // دریافت نتیجه
                'assigned',            // اختصاص به کارشناس
                'sla_warning',         // هشدار SLA
                'system',              // رویداد سیستمی
            ]);
            
            // === جزئیات رویداد ===
            $table->string('title', 255);
            $table->text('description')->nullable();
            
            // === وضعیت (برای status_change) ===
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();
            
            // === موجودیت مرتبط (polymorphic) ===
            $table->string('related_type', 100)->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            
            // === کاربر ===
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name', 150)->nullable();
            $table->enum('user_type', ['customer', 'agent', 'system', 'admin'])->default('system');
            
            // === متادیتا ===
            $table->json('metadata')->nullable();
            $table->enum('visibility', ['internal', 'public', 'both'])->default('both')
                ->comment('internal = فقط ادمین، public = کاربر هم می‌بیند، both = هر دو');
            
            $table->timestamp('occurred_at')->useCurrent();
            
            // === Indexes ===
            $table->index(['visa_case_id', 'occurred_at']);
            $table->index('event_type');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_case_timeline');
    }
};
