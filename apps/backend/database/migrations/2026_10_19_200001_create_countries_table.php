<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            
            // === شناسه‌ها ===
            $table->string('name', 150)->comment('نام فارسی');
            $table->string('name_en', 150)->comment('نام لاتین');
            $table->string('code', 3)->unique()->comment('کد ISO Alpha-2 (TR, AE, GE)');
            $table->string('code3', 4)->nullable()->comment('کد ISO Alpha-3 (TUR, ARE)');
            $table->string('phone_code', 10)->nullable()->comment('پیش‌شماره تلفن (+90)');
            
            // === موقعیت ===
            $table->string('continent', 50)->nullable();
            $table->string('capital', 150)->nullable()->comment('پایتخت');
            $table->string('currency', 10)->nullable()->comment('واحد پول');
            $table->string('timezone', 50)->nullable();
            
            // === اطلاعات نمایش ===
            $table->string('flag_emoji', 10)->nullable();
            $table->string('flag_image', 255)->nullable();
            $table->string('slug', 100)->unique();
            
            // === وضعیت ویزا برای ایرانیان ===
            $table->enum('visa_status_for_iranians', [
                'required',          // ویزا لازم است
                'visa_on_arrival',   // ویزا در فرودگاه
                'evisa',             // ویزای الکترونیکی
                'visa_free',         // بدون ویزا
                'restricted',        // ممنوع / محدود
            ])->default('required');
            
            // === SEO ===
            $table->string('seo_title', 255)->nullable();
            $table->text('seo_description')->nullable();
            
            // === وضعیت ===
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false)->comment('کشور پرطرفدار');
            $table->unsignedInteger('sort_order')->default(0);
            
            $table->timestamps();
            
            // === Indexes ===
            $table->index('is_active');
            $table->index('is_popular');
            $table->index('visa_status_for_iranians');
            $table->index('continent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
