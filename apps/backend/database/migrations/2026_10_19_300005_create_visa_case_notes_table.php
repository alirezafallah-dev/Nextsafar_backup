<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_case_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_case_id')->constrained('visa_cases')->cascadeOnDelete();
            
            // === نوع یادداشت ===
            $table->enum('type', [
                'general',         // عمومی
                'document_issue',  // مشکل مدرک
                'embassy_note',    // یادداشت سفارت
                'customer_query',  // سوال مشتری
                'internal',        // داخلی
                'call_log',        // لاگ تماس
                'warning',         // هشدار
                'important',       // مهم
            ])->default('general');
            
            $table->text('content');
            
            // === دید ===
            $table->enum('visibility', ['internal', 'public'])->default('internal')
                ->comment('internal = فقط ادمین، public = کاربر هم می‌بیند');
            
            // === سنجاق کردن ===
            $table->boolean('is_pinned')->default(false);
            
            // === نویسنده ===
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name', 150)->nullable();
            $table->enum('author_type', ['customer', 'agent', 'system', 'admin'])->default('agent');
            
            // === پاسخ‌ها ===
            $table->foreignId('parent_id')->nullable()->constrained('visa_case_notes')->nullOnDelete()
                ->comment('برای reply کردن به یک یادداشت');
            
            // === خوانده شده ===
            $table->boolean('is_read_by_customer')->default(false);
            $table->boolean('is_read_by_agent')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            // === Indexes ===
            $table->index(['visa_case_id', 'created_at']);
            $table->index('is_pinned');
            $table->index('visibility');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_case_notes');
    }
};
