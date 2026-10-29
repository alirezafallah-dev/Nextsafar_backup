<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            
            // === نوع آیتم (Polymorphic) ===
            $table->string('item_type', 50)->comment('tour, hotel, visa, service');
            $table->unsignedBigInteger('item_id')->nullable();
            
            // === Snapshot از اطلاعات در زمان سفارش ===
            $table->string('title', 255)->comment('عنوان آیتم در زمان سفارش');
            $table->string('description', 1000)->nullable();
            $table->string('code', 50)->nullable()->comment('کد تور/هتل/ویزا');
            $table->string('slug', 255)->nullable();
            
            // === تاریخ‌های سرویس ===
            $table->date('service_date')->nullable()->comment('تاریخ شروع سرویس');
            $table->date('service_end_date')->nullable()->comment('تاریخ پایان سرویس');
            
            // === مقصد ===
            $table->string('destination', 200)->nullable();
            
            // === تعداد ===
            $table->unsignedSmallInteger('quantity')->default(1);
            
            // === قیمت‌ها (به ریال) ===
            $table->unsignedBigInteger('unit_price')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('total_price')->default(0);
            $table->string('currency', 10)->default('IRR');
            
            // === اطلاعات خاص ===
            $table->json('extra_data')->nullable()->comment('داده‌های خاص (JSON)');
            // برای تور: tour_date_id، برای هتل: check-in/out، room type
            // برای ویزا: visa_type، country
            
            // === وضعیت آیتم ===
            $table->enum('status', [
                'pending',      // در انتظار
                'confirmed',    // تأیید شده
                'cancelled',    // لغو شده
                'refunded',     // استرداد شده
            ])->default('pending');
            
            $table->timestamps();
            
            // === Indexes ===
            $table->index(['item_type', 'item_id']);
            $table->index('order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
