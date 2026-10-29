<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            
            // === شناسه یکتا ===
            $table->string('code', 30)->unique()->comment('کد یکتای تراکنش (LED-YYMMDD-XXXX)');
            $table->string('batch_id', 64)->nullable()->index()
                ->comment('شناسه دسته‌ای (برای تراکنش‌های دوطرفه)');
            
            // === نوع تراکنش ===
            $table->enum('type', [
                'payment_received',       // دریافت پرداخت
                'refund_issued',          // استرداد
                'commission_earned',      // کارمزد
                'tax_collected',          // مالیات
                'provider_payment',       // پرداخت به پروایدر
                'adjustment',             // تعدیل
                'reversal',               // برگشت
                'manual',                 // دستی
            ]);
            
            // === ارتباط با سفارش/پرداخت ===
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('refund_id')->nullable()->constrained('refunds')->nullOnDelete();
            
            // === حساب‌ها (Double-entry) ===
            $table->string('account_debit', 100)->comment('حساب بدهکار');
            $table->string('account_credit', 100)->comment('حساب بستانکار');
            
            // === مبالغ ===
            $table->unsignedBigInteger('amount')->comment('مبلغ تراکنش');
            $table->string('currency', 10)->default('IRR');
            $table->decimal('exchange_rate', 12, 6)->default(1.000000)
                ->comment('نرخ ارز (برای ارزهای غیر ریالی)');
            $table->unsignedBigInteger('amount_in_base_currency')->nullable()
                ->comment('مبلغ به ارز پایه');
            
            // === توضیحات ===
            $table->string('description', 500);
            $table->text('notes')->nullable();
            
            // === Reference ===
            $table->string('reference_type', 50)->nullable()
                ->comment('payment, refund, manual_adjustment, ...');
            $table->string('reference_code', 100)->nullable()
                ->comment('کد مرجع (کد پرداخت، کد استرداد و...)');
            
            // === کاربر و تاریخ ===
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('transaction_date')->useCurrent();
            
            // === متادیتا ===
            $table->json('metadata')->nullable();
            $table->boolean('is_reversed')->default(false);
            $table->foreignId('reversed_by_entry')->nullable()
                ->comment('اگر این تراکنش توسط تراکنش دیگری برگشت شده');
            
            // === Timestamps ===
            $table->timestamps();
            
            // === Indexes ===
            $table->index(['order_id', 'transaction_date']);
            $table->index(['payment_id', 'transaction_date']);
            $table->index(['account_debit', 'transaction_date']);
            $table->index(['account_credit', 'transaction_date']);
            $table->index('transaction_date');
            $table->index(['reference_type', 'reference_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
