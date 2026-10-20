<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            
            // ارتباط با وردپرس
            $table->unsignedBigInteger('wordpress_post_id');
            $table->string('tour_title');
            $table->string('tour_slug')->nullable();
            
            // جزئیات تور
            $table->string('destination')->nullable();
            $table->date('departure_date');
            $table->unsignedInteger('duration_days')->nullable();
            $table->unsignedInteger('duration_nights')->nullable();
            
            // ظرفیت
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);
            
            // کد تایید
            $table->string('confirmation_code', 50)->nullable();
            
            // داده‌های اضافی
            $table->jsonb('tour_data')->nullable();
            
            $table->timestamps();
            
            $table->index('booking_id');
            $table->index('wordpress_post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_bookings');
    }
};
