<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            
            // ارائه‌دهنده
            $table->string('provider', 30); // serpapi, searchapi
            $table->string('property_token')->nullable();
            $table->string('provider_property_id')->nullable();
            
            // اطلاعات هتل
            $table->string('hotel_name');
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->unsignedInteger('star_rating')->nullable();
            
            // اتاق
            $table->string('room_type')->nullable();
            $table->unsignedInteger('room_count')->default(1);
            
            // تاریخ‌ها
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedInteger('nights');
            
            // مهمانان
            $table->unsignedInteger('adults')->default(2);
            $table->unsignedInteger('children')->default(0);
            
            // کد تایید
            $table->string('confirmation_code', 50)->nullable();
            
            // داده‌های خام
            $table->jsonb('raw_data')->nullable();
            
            $table->timestamps();
            
            $table->index('booking_id');
            $table->index(['check_in', 'check_out']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
    }
};
