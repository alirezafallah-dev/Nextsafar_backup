<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            
            // ارتباط با وردپرس
            $table->unsignedBigInteger('wordpress_post_id');
            $table->string('visa_title');
            $table->string('visa_slug')->nullable();
            
            // نوع ویزا
            $table->string('visa_type', 50); // tourist, business, student, ...
            $table->string('country', 100);
            $table->string('country_code', 5)->nullable();
            
            // وضعیت خاص ویزا
            $table->string('visa_status', 30)->default('draft');
            
            // اطلاعات متقاضی
            $table->jsonb('applicant_data')->nullable();
            
            // مدارک
            $table->jsonb('documents')->nullable();
            
            // زمان‌های پردازش
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            
            // یادداشت ادمین
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // داده‌های اضافی
            $table->jsonb('visa_data')->nullable();
            
            $table->timestamps();
            
            $table->index('booking_id');
            $table->index('wordpress_post_id');
            $table->index('visa_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_bookings');
    }
};
