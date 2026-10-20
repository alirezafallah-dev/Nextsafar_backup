<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // === شناسه و کد ===
            $table->string('code', 30)->unique()->comment('کد یکتای سفارش (NS-261017-XXXX)');
            
            // === نوع سفارش ===
            $table->enum('type', [
                'tour',      // سفارش تور
                'hotel',     // سفارش هتل
                'visa',      // سفارش ویزا
                'mixed',     // ترکیبی (مثلاً تور + ویزا)
            ])->default('tour');
            
            // === کاربر مالک ===
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // === منبع سفارش ===
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete()
                ->comment('لید تبدیل شده به سفارش');
            $table->string('source', 50)->default('direct')
                ->comment('direct, lead, admin, api, whatsapp');
            
            // === وضعیت ===
            $table->enum('status', [
                'draft',                 // پیش‌نویس
                'pending_payment',       // در انتظار پرداخت
                'paid',                  // پرداخت شده
                'processing',            // در حال صدور
                'confirmed',             // صادر شده / تأیید شده
                'failed',                // ناموفق
                'pending_review',        // نیازمند بررسی
                'cancelled',             // لغو شده
                'refund_requested',      // درخواست استرداد
                'refunded',              // استرداد شده
            ])->default('draft');
            
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // === قیمت‌ها (به ریال) ===
            $table->unsignedBigInteger('subtotal')->default(0)->comment('جمع کل آیتم‌ها');
            $table->unsignedBigInteger('discount_amount')->default(0)->comment('مبلغ تخفیف');
            $table->unsignedBigInteger('tax_amount')->default(0)->comment('مالیات');
            $table->unsignedBigInteger('total_amount')->default(0)->comment('مبلغ نهایی');
            $table->string('currency', 10)->default('IRR');
            
            // === کد تخفیف ===
            $table->string('coupon_code', 50)->nullable();
            $table->foreignId('coupon_id')->nullable(); // در فاز بعدی
            
            // === اطلاعات مسافران ===
            $table->unsignedTinyInteger('adults_count')->default(0);
            $table->unsignedTinyInteger('children_count')->default(0);
            $table->unsignedTinyInteger('infants_count')->default(0);
            
            // === تاریخ‌های مهم ===
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // === کارشناسان ===
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete()
                ->comment('کارشناس مسئول');
            
            // === اطلاعات اضافی ===
            $table->json('metadata')->nullable()->comment('داده‌های اضافی (JSON)');
            $table->text('admin_notes')->nullable();
            $table->text('customer_notes')->nullable();
            
            // === Tracking ===
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            
            // === Timestamps ===
            $table->timestamps();
            $table->softDeletes();
            
            // === Indexes ===
            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index(['type', 'status']);
            $table->index('assigned_to');
            $table->index('created_at');
            $table->index('paid_at');
            $table->index('lead_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
