<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete()
                ->comment('آیتم مرتبط (اگر هر passenger برای آیتم خاصی باشد)');
            
            // === اطلاعات اصلی ===
            $table->enum('type', ['adult', 'child', 'infant'])->default('adult');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('first_name_en', 100)->nullable()->comment('نام لاتین');
            $table->string('last_name_en', 100)->nullable()->comment('نام خانوادگی لاتین');
            
            // === اطلاعات شناسنامه‌ای ===
            $table->string('national_id', 20)->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality', 100)->nullable();
            
            // === اطلاعات تماس ===
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            
            // === اطلاعات اضافه ===
            $table->string('relation_to_owner', 50)->nullable()->comment('نسبت با مالک سفارش');
            $table->text('special_requests')->nullable()->comment('درخواست‌های خاص (غذا، ویلچر و...)');
            
            // === قیمت این مسافر ===
            $table->unsignedBigInteger('price')->default(0);
            
            $table->timestamps();
            
            // === Indexes ===
            $table->index('order_id');
            $table->index('order_item_id');
            $table->index('national_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_passengers');
    }
};
