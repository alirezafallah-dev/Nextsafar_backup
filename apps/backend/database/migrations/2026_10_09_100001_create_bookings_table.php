<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 30)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // نوع رزرو
            $table->string('booking_type', 20); // flight, hotel, tour, visa
            
            // منبع آیتم (وردپرس یا خارجی)
            $table->string('item_source', 20)->default('internal'); // wordpress, external, internal
            $table->string('item_id', 100)->nullable(); // ID پست وردپرس یا ID خارجی
            $table->string('item_title')->nullable(); // عنوان آیتم (برای نمایش سریع)
            
            // وضعیت
            $table->string('status', 30)->default('pending');
            
            // قیمت
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('currency', 3)->default('IRR');
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->string('coupon_code', 50)->nullable();
            
            // مسافران
            $table->unsignedInteger('passenger_count')->default(1);
            
            // داده‌های اضافی (JSON)
            $table->jsonb('booking_data')->nullable();
            $table->jsonb('pricing_breakdown')->nullable();
            $table->jsonb('cancellation_policy')->nullable();
            
            // پرداخت
            $table->string('payment_status', 30)->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // یادداشت‌ها
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // زمان‌های مهم
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_by', 50)->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();
            
            // ایندکس‌ها
            $table->index(['user_id', 'status']);
            $table->index(['booking_type', 'status']);
            $table->index(['item_source', 'item_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
