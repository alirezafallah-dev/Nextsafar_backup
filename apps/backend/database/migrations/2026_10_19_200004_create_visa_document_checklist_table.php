<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_document_checklist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_type_id')->constrained('visa_types')->cascadeOnDelete();
            
            // === اطلاعات آیتم ===
            $table->string('title', 255)->comment('عنوان مدرک (پاسپورت، عکس، ...)');
            $table->text('description')->nullable()->comment('توضیحات و نکات');
            $table->string('sample_url', 500)->nullable()->comment('لینک نمونه');
            
            // === نوع Document (مرتبط با DocumentCategory) ===
            $table->string('document_category', 50)->default('general')
                ->comment('دسته‌بندی فایل (از DocumentCategory enum)');
            
            // === مشخصات فایل ===
            $table->json('allowed_mime_types')->nullable()
                ->comment('انواع MIME مجاز');
            $table->unsignedBigInteger('max_file_size')->nullable()
                ->comment('حداکثر حجم (بایت)');
            $table->unsignedTinyInteger('max_files')->default(1)
                ->comment('حداکثر تعداد فایل');
            $table->boolean('accept_multiple')->default(false)
                ->comment('چند فایل قبول کند؟');
            
            // === اهمیت ===
            $table->boolean('is_required')->default(true);
            $table->boolean('is_per_passenger')->default(true)
                ->comment('به ازای هر مسافر لازم است');
            $table->enum('importance', ['required', 'recommended', 'optional'])->default('required');
            
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('visa_type_id');
            $table->index('is_required');
            $table->index('importance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_document_checklist');
    }
};
