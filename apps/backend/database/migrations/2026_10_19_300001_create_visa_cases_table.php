<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_cases', function (Blueprint $table) {
            $table->id();
            
            // === شناسه‌ها ===
            $table->string('code', 30)->unique()->comment('کد یکتا (VIS-YYMMDD-XXXX)');
            $table->uuid('uuid')->unique()->comment('شناسه عمومی');
            
            // === اتصالات ===
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('visa_type_id')->constrained('visa_types');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete()
                ->comment('سفارش مرتبط (اگر از طریق سفارش ایجاد شده)');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete()
                ->comment('لید تبدیل شده (اگر از لید ایجاد شده)');
            
            // === وضعیت (۱۴ وضعیت طبق بخش ۱۶.۲ سند) ===
            $table->enum('status', [
                'registered',              // ۱. ثبت اولیه
                'documents_incomplete',    // ۲. مدارک ناقص
                'documents_complete',      // ۳. مدارک کامل
                'under_review',            // ۴. در حال بررسی
                'needs_appointment',       // ۵. نیازمند وقت سفارت
                'appointment_booked',      // ۶. وقت گرفته شد
                'submitted',               // ۷. ارسال شده
                'in_embassy_process',      // ۸. در پروسه سفارت
                'needs_biometrics',        // ۹. نیازمند انگشت‌نگاری
                'approved',                // ۱۰. تأیید شد
                'rejected',                // ۱۱. رد شد
                'needs_correction',        // ۱۲. نیازمند اصلاح
                'completed',               // ۱۳. تکمیل شد
                'cancelled',               // ۱۴. لغو شد
            ])->default('registered');
            
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // === اطلاعات سفر ===
            $table->date('intended_travel_date')->nullable()->comment('تاریخ مورد نظر سفر');
            $table->date('visa_issue_date')->nullable()->comment('تاریخ صدور ویزا');
            $table->date('visa_expiry_date')->nullable()->comment('تاریخ انقضای ویزا');
            
            // === وقت سفارت ===
            $table->timestamp('appointment_datetime')->nullable();
            $table->string('appointment_location', 255)->nullable();
            $table->string('appointment_reference', 100)->nullable();
            
            // === اطلاعات پاسپورت ===
            $table->string('passport_number', 50)->nullable();
            $table->string('passport_issuer', 100)->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            
            // === ردیابی ===
            $table->string('embassy_tracking_number', 100)->nullable()
                ->comment('شماره پیگیری سفارت');
            $table->string('visa_sticker_number', 100)->nullable()
                ->comment('شماره ویزا');
            
            // === کارشناس مسئول ===
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            
            // === قیمت و پرداخت ===
            $table->unsignedBigInteger('service_fee')->default(0)->comment('هزینه خدمات ما');
            $table->unsignedBigInteger('embassy_fee')->default(0)->comment('هزینه سفارت');
            $table->unsignedBigInteger('total_amount')->default(0)->comment('مبلغ کل');
            $table->unsignedBigInteger('paid_amount')->default(0)->comment('مبلغ پرداخت شده');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            
            // === آمار مدارک ===
            $table->unsignedTinyInteger('total_checklist_items')->default(0);
            $table->unsignedTinyInteger('uploaded_documents_count')->default(0);
            $table->unsignedTinyInteger('approved_documents_count')->default(0);
            $table->unsignedTinyInteger('documents_completion_percentage')->default(0);
            
            // === محاسبه SLA ===
            $table->unsignedSmallInteger('sla_days')->nullable()->comment('روزهای وعده داده شده');
            $table->unsignedSmallInteger('elapsed_days')->default(0)->comment('روزهای گذشته');
            $table->boolean('is_overdue')->default(false);
            
            // === زمان‌های کلیدی ===
            $table->timestamp('documents_completed_at')->nullable();
            $table->timestamp('submitted_to_embassy_at')->nullable();
            $table->timestamp('biometrics_completed_at')->nullable();
            $table->timestamp('result_received_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // === یادداشت‌ها ===
            $table->text('internal_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('correction_notes')->nullable();
            
            // === Timestamps ===
            $table->timestamps();
            $table->softDeletes();
            
            // === Indexes ===
            $table->index(['user_id', 'status']);
            $table->index(['visa_type_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('status');
            $table->index('priority');
            $table->index('is_overdue');
            $table->index('next_follow_up_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_cases');
    }
};
