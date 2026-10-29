<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_types', function (Blueprint $table) {
            $table->id();
            
            // === اتصال به WordPress (spec 7.3) ===
            $table->unsignedBigInteger('wp_post_id')->nullable()->unique()
                ->comment('شناسه پست وردپرس (post_type = visa)');
            $table->string('wp_slug', 255)->nullable();
            
            // === اطلاعات اصلی ===
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('title', 255)->comment('عنوان نوع ویزا');
            $table->string('title_en', 255)->nullable();
            $table->string('slug', 255)->unique();
            
            // === دسته‌بندی ===
            $table->enum('category', [
                'tourist',        // توریستی
                'business',       // تجاری
                'student',        // تحصیلی
                'work',           // کاری
                'medical',        // درمانی
                'transit',        // ترانزیت
                'family',         // خانوادگی
                'investment',     // سرمایه‌گذاری
                'digital_nomad',  // دیجیتال نومد
                'other',          // سایر
            ])->default('tourist');
            
            // === زمان و اعتبار ===
            $table->unsignedSmallInteger('processing_days_min')->nullable()
                ->comment('حداقل روز پردازش');
            $table->unsignedSmallInteger('processing_days_max')->nullable()
                ->comment('حداکثر روز پردازش');
            $table->unsignedSmallInteger('validity_days')->nullable()
                ->comment('اعتبار ویزا (روز)');
            $table->unsignedSmallInteger('max_stay_days')->nullable()
                ->comment('حداکثر اقامت (روز)');
            $table->enum('entry_type', ['single', 'double', 'multiple'])->default('single');
            
            // === قیمت‌ها (به ریال) ===
            $table->unsignedBigInteger('base_price')->default(0)
                ->comment('قیمت پایه خدمات ما');
            $table->unsignedBigInteger('embassy_fee')->default(0)
                ->comment('هزینه سفارت');
            $table->unsignedBigInteger('total_price')->default(0)
                ->comment('قیمت کل (پایه + سفارت)');
            $table->string('currency', 10)->default('IRR');
            
            // === محتوا (سینک شده از WP) ===
            $table->text('short_description')->nullable();
            $table->longText('full_description')->nullable();
            $table->json('requirements')->nullable()->comment('شرایط و مدارک (JSON)');
            $table->json('faqs')->nullable()->comment('سوالات متداول (JSON)');
            
            // === SEO ===
            $table->string('seo_title', 255)->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_image', 500)->nullable();
            
            // === وضعیت ===
            $table->enum('status', [
                'draft',      // پیش‌نویس
                'published',  // منتشر شده
                'inactive',   // غیرفعال
                'coming_soon',// به زودی
            ])->default('draft');
            
            $table->boolean('is_featured')->default(false)->comment('ویژه');
            $table->boolean('accepts_online_application')->default(true)
                ->comment('امکان ثبت درخواست آنلاین');
            
            // === سینک ===
            $table->string('sync_version', 50)->nullable();
            $table->timestamp('last_synced_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // === Indexes ===
            $table->index('country_id');
            $table->index('category');
            $table->index('status');
            $table->index('is_featured');
            $table->index('wp_post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_types');
    }
};
