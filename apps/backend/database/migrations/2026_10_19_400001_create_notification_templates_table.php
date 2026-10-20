<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            
            // === شناسه ===
            $table->string('code', 100)->unique()->comment('payment_success_sms, visa_status_email, ...');
            $table->string('name', 255);
            
            // === نوع رویداد ===
            $table->string('event_type', 50);
            $table->enum('channel', ['sms', 'email', 'internal', 'push']);
            
            // === محتوا ===
            $table->string('subject', 255)->nullable();
            $table->text('content');
            $table->json('variables')->nullable()->comment('لیست متغیرهای مجاز');
            
            // === زبان ===
            $table->string('locale', 10)->default('fa');
            
            // === وضعیت ===
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false)->comment('قالب سیستمی (غیرقابل حذف)');
            
            // === متادیتا ===
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // === Indexes ===
            $table->index(['event_type', 'channel', 'is_active']);
            $table->unique(['code', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
