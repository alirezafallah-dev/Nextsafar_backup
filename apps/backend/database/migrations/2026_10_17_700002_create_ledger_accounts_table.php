<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('کد حساب (مثلاً 1100, 2100)');
            $table->string('name', 150)->comment('نام حساب');
            $table->string('name_en', 150)->nullable()->comment('نام لاتین');
            
            $table->enum('type', [
                'asset',       // دارایی
                'liability',   // بدهی
                'equity',      // سرمایه
                'revenue',     // درآمد
                'expense',     // هزینه
            ]);
            
            $table->enum('normal_balance', ['debit', 'credit'])
                ->comment('مانده طبیعی حساب');
            
            $table->foreignId('parent_id')->nullable()->constrained('ledger_accounts')->nullOnDelete();
            $table->string('category', 50)->nullable()->comment('دسته‌بندی');
            $table->text('description')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false)->comment('حساب سیستمی (غیرقابل حذف)');
            $table->unsignedInteger('sort_order')->default(0);
            
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_accounts');
    }
};
