<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_case_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_case_id')->constrained('visa_cases')->cascadeOnDelete();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete()
                ->comment('اشاره به جدول documents');
            $table->foreignId('checklist_item_id')->nullable()
                ->constrained('visa_document_checklist')->nullOnDelete()
                ->comment('اشاره به آیتم چک‌لیست');
            $table->foreignId('passenger_id')->nullable()
                ->constrained('visa_case_passengers')->nullOnDelete()
                ->comment('اگر این مدرک برای یک مسافر خاص است');
            
            // === وضعیت تأیید ===
            $table->enum('verification_status', [
                'pending',          // در انتظار بررسی
                'approved',         // تأیید شد
                'needs_correction', // نیاز به اصلاح
                'rejected',         // رد شد
            ])->default('pending');
            
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            
            // === اطلاعات اضافی ===
            $table->string('translation_status', 30)->nullable()
                ->comment('none, pending, completed');
            $table->boolean('needs_translation')->default(false);
            $table->boolean('is_original')->default(true)
                ->comment('اصل مدرک یا ترجمه');
            
            $table->timestamps();
            
            // === Indexes ===
            $table->index(['visa_case_id', 'verification_status']);
            $table->index(['checklist_item_id', 'passenger_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_case_documents');
    }
};
