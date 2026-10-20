<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // افزودن order_id (اختیاری - برای سفارش‌های جدید)
            if (!Schema::hasColumn('payments', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('booking_id')
                    ->constrained('orders')->nullOnDelete()
                    ->comment('شناسه سفارش (جدید)');
            }
            
            // کد یکتای تراکنش
            if (!Schema::hasColumn('payments', 'code')) {
                $table->string('code', 30)->unique()->nullable()->after('id')
                    ->comment('کد یکتای تراکنش (PMT-YYMMDD-XXXX)');
            }
            
            // Idempotency Key برای جلوگیری از پرداخت تکراری (قانون ۱۴.۳.۶)
            if (!Schema::hasColumn('payments', 'idempotency_key')) {
                $table->string('idempotency_key', 64)->unique()->nullable()
                    ->comment('کلید جلوگیری از تکرار (user_id + order_id + timestamp)');
            }
            
            // امضای درگاه (قانون ۱۴.۳.۵)
            if (!Schema::hasColumn('payments', 'gateway_signature')) {
                $table->text('gateway_signature')->nullable()
                    ->comment('امضای درگاه برای تأیید callback');
            }
            
            // وضعیت تأیید سمت سرور (قانون ۱۴.۳.۱)
            if (!Schema::hasColumn('payments', 'verification_status')) {
                $table->enum('verification_status', [
                    'not_verified',     // تأیید نشده
                    'verified',         // تأیید شده از سمت سرور
                    'mismatch_amount',  // عدم تطابق مبلغ
                    'verification_failed', // خطا در تأیید
                ])->default('not_verified')
                    ->comment('وضعیت تأیید سمت سرور');
            }
            
            // مبلغ تأیید شده (ممکن است با مبلغ درخواستی متفاوت باشد - قانون ۱۴.۳.۴)
            if (!Schema::hasColumn('payments', 'verified_amount')) {
                $table->unsignedBigInteger('verified_amount')->nullable()
                    ->comment('مبلغ تأیید شده توسط درگاه');
            }
            
            // تعداد تلاش‌های تأیید
            if (!Schema::hasColumn('payments', 'verification_attempts')) {
                $table->unsignedTinyInteger('verification_attempts')->default(0);
            }
            
            // زمان‌های مهم
            if (!Schema::hasColumn('payments', 'initiated_at')) {
                $table->timestamp('initiated_at')->nullable();
            }
            if (!Schema::hasColumn('payments', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            
            // لاگ تأیید (قانون ۱۴.۳.۷)
            if (!Schema::hasColumn('payments', 'verification_log')) {
                $table->json('verification_log')->nullable()
                    ->comment('لاگ کامل استعلام از درگاه');
            }
            
            // Indexes جدید
            $table->index(['order_id', 'status']);
            $table->index('idempotency_key');
            $table->index('verification_status');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $columns = [
                'order_id', 'code', 'idempotency_key', 'gateway_signature',
                'verification_status', 'verified_amount', 'verification_attempts',
                'initiated_at', 'verified_at', 'verification_log',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
