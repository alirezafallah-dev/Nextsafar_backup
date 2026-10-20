<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            
            // نوع مسافر
            $table->string('passenger_type', 20)->default('adult'); // adult, child, infant
            $table->string('title', 10)->nullable(); // MR, MRS, MS, MISS, MASTER
            
            // نام (انگلیسی)
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            
            // نام (فارسی)
            $table->string('first_name_fa', 100)->nullable();
            $table->string('last_name_fa', 100)->nullable();
            
            // مدارک شناسایی
            $table->string('national_id', 20)->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('passport_country', 5)->nullable();
            
            // اطلاعات شخصی
            $table->date('birth_date');
            $table->string('gender', 10); // male, female
            $table->string('nationality', 50)->default('Iran');
            
            // تماس
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            
            // ترجیحات
            $table->string('seat_preference', 20)->nullable();
            $table->string('meal_preference', 50)->nullable();
            $table->text('special_requests')->nullable();
            
            // قیمت پرداختی این مسافر
            $table->decimal('price_paid', 14, 2)->default(0);
            
            $table->timestamps();
            
            // ایندکس‌ها
            $table->index('booking_id');
            $table->index('passport_number');
            $table->index('national_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_passengers');
    }
};
