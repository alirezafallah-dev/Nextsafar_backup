<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            
            // === Dates ===
            $table->date('departure_date')->comment('تاریخ حرکت');
            $table->date('return_date')->comment('تاریخ بازگشت');
            
            // === Capacity ===
            $table->unsignedSmallInteger('total_capacity')->default(0)->comment('ظرفیت کل');
            $table->unsignedSmallInteger('remaining_capacity')->default(0)->comment('ظرفیت باقی‌مانده');
            $table->unsignedSmallInteger('reserved_count')->default(0)->comment('تعداد رزرو شده');
            $table->unsignedSmallInteger('min_participants')->default(1)->comment('حداقل تعداد برای برگزاری');
            
            // === Pricing (IRR) ===
            $table->unsignedBigInteger('adult_price')->default(0)->comment('قیمت بزرگسال');
            $table->unsignedBigInteger('child_price')->default(0)->comment('قیمت کودک (2-12)');
            $table->unsignedBigInteger('infant_price')->default(0)->comment('قیمت نوزاد (0-2)');
            $table->unsignedBigInteger('single_supplement')->default(0)->comment('هزینه اتاق سینگل');
            
            // === Discount ===
            $table->unsignedBigInteger('discount_amount')->default(0)->comment('مبلغ تخفیف');
            $table->unsignedTinyInteger('discount_percent')->default(0)->comment('درصد تخفیف');
            $table->timestamp('discount_valid_until')->nullable();
            
            // === Status ===
            $table->enum('status', [
                'available',      // موجود
                'limited',        // ظرفیت محدود
                'sold_out',       // تکمیل ظرفیت
                'cancelled',      // لغو شده
                'expired',        // منقضی
            ])->default('available');
            
            $table->boolean('is_guaranteed')->default(false)->comment('تور تضمینی');
            
            // === Notes ===
            $table->text('notes')->nullable()->comment('یادداشت‌های خاص این تاریخ');
            
            $table->timestamps();
            
            // === Indexes ===
            $table->index(['tour_id', 'departure_date']);
            $table->index('departure_date');
            $table->index('status');
            $table->index(['departure_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_dates');
    }
};
