<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            
            // ارائه‌دهنده
            $table->string('provider', 30); // serpapi, searchapi
            $table->string('provider_flight_id')->nullable();
            
            // اطلاعات پرواز
            $table->string('flight_number', 20)->nullable();
            $table->string('airline', 100)->nullable();
            $table->string('airline_code', 10)->nullable();
            
            // فرودگاه‌ها
            $table->string('departure_airport', 10); // IATA code
            $table->string('departure_city')->nullable();
            $table->string('arrival_airport', 10);
            $table->string('arrival_city')->nullable();
            
            // زمان‌ها
            $table->timestamp('departure_time');
            $table->timestamp('arrival_time')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            
            // جزئیات
            $table->string('cabin_class', 20)->default('economy'); // economy, business, first
            $table->unsignedInteger('stops')->default(0);
            $table->string('pnr_code', 20)->nullable();
            $table->unsignedInteger('baggage_kg')->nullable();
            
            // نوع سفر
            $table->string('trip_type', 20)->default('one_way'); // one_way, round_trip
            
            // داده‌های خام API
            $table->jsonb('raw_data')->nullable();
            
            $table->timestamps();
            
            $table->index('booking_id');
            $table->index(['departure_airport', 'arrival_airport']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_bookings');
    }
};
