<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // === کد یکتا (قانون ۱۵.۳.۱) ===
            if (!Schema::hasColumn('refunds', 'code')) {
                $table->string('code', 30)->unique()->nullable()->after('id')
                    ->comment('کد یکتای استرداد (REF-YYMMDD-XXXX)');
            }
            
            // === اتصال به Order جدید ===
            if (!Schema::hasColumn('refunds', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('booking_id')
                    ->constrained('orders')->nullOnDelete()
                    ->comment('سفارش مرتبط (جدید)');
            }
            
            // === نوع استرداد ===
            if (!Schema::hasColumn('refunds', 'type')) {
                $table->enum('type', ['full', 'partial', 'penalty_deduction'])
                    ->default('full')->after('order_id')
                    ->comment('نوع استرداد');
            }
            
            // === مبالغ ===
            if (!Schema::hasColumn('refunds', 'original_amount')) {
                $table->unsignedBigInteger('original_amount')->nullable()->after('amount')
                    ->comment('مبلغ اصلی پرداختی');
            }
            
            if (!Schema::hasColumn('refunds', 'penalty_amount')) {
                $table->unsignedBigInteger('penalty_amount')->default(0)->after('original_amount')
                    ->comment('مبلغ جریمه (قانون ۱۵.۳.۳)');
            }
            
            if (!Schema::hasColumn('refunds', 'refund_amount')) {
                $table->unsignedBigInteger('refund_amount')->nullable()->after('penalty_amount')
                    ->comment('مبلغ نهایی بازگشتی');
            }
            
            // === تبدیل status به enum استاندارد ===
            if (!Schema::hasColumn('refunds', 'status_new')) {
                $table->enum('status_new', [
                    'registered',      // ثبت شده
                    'reviewing',       // در حال بررسی
                    'approved',        // تأیید شده
                    'rejected',        // رد شده
                    'processing',      // در حال بازگشت وجه
                    'completed',       // انجام شد
                    'cancelled',       // لغو شده
                ])->default('registered')->after('refund_amount');
            }
            
            // === جریمه ===
            if (!Schema::hasColumn('refunds', 'penalty_reason')) {
                $table->text('penalty_reason')->nullable()
                    ->comment('دلیل جریمه');
            }
            
            // === تأیید/رد ===
            if (!Schema::hasColumn('refunds', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('refunds', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('refunds', 'rejected_by')) {
                $table->foreignId('rejected_by')->nullable()
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('refunds', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }
            if (!Schema::hasColumn('refunds', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()
                    ->comment('دلیل رد درخواست');
            }
            
            // === زمان‌ها ===
            if (!Schema::hasColumn('refunds', 'requested_at')) {
                $table->timestamp('requested_at')->nullable()
                    ->comment('زمان درخواست');
            }
            
            // === متادیتا ===
            if (!Schema::hasColumn('refunds', 'metadata')) {
                $table->json('metadata')->nullable()
                    ->comment('داده‌های اضافی');
            }
            
            // === Indexes ===
            $table->index(['order_id', 'status_new']);
            $table->index(['payment_id', 'status_new']);
            $table->index('requested_at');
        });
        
        // مهاجرت داده‌های قدیمی
        DB::statement("UPDATE refunds SET status_new = CASE 
            WHEN status = 'pending' THEN 'registered'
            WHEN status = 'approved' THEN 'approved'
            WHEN status = 'processing' THEN 'processing'
            WHEN status = 'completed' THEN 'completed'
            WHEN status = 'rejected' THEN 'rejected'
            ELSE 'registered'
        END WHERE status_new IS NULL");
        
        // حذف status قدیمی و rename status_new
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('refunds', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $columns = [
                'code', 'order_id', 'type', 'original_amount', 'penalty_amount',
                'refund_amount', 'penalty_reason', 'approved_by', 'approved_at',
                'rejected_by', 'rejected_at', 'rejection_reason', 'requested_at',
                'metadata',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('refunds', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
