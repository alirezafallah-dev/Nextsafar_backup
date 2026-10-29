<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            
            // === WordPress Sync Fields ===
            $table->unsignedBigInteger('wp_post_id')->unique()->nullable()->comment('شناسه پست وردپرس');
            $table->string('wp_post_type', 50)->default('hotel')->comment('نوع پست وردپرس');
            $table->timestamp('wp_last_synced_at')->nullable()->comment('زمان آخرین همگام‌سازی');
            $table->string('wp_content_version', 100)->nullable()->comment('نسخه محتوای وردپرس');
            
            // === Basic Info ===
            $table->string('slug', 255)->unique()->comment('اسلاگ یکتا');
            $table->string('name', 255)->comment('نام هتل');
            $table->string('city', 100)->nullable()->comment('شهر');
            $table->string('country', 100)->nullable()->comment('کشور');
            $table->string('address', 500)->nullable()->comment('آدرس');
            
            // === Status Fields ===
            $table->enum('publish_status', ['draft', 'published', 'private', 'trash', 'deleted'])
                ->default('draft')->comment('وضعیت انتشار وردپرس');
            $table->enum('display_status', ['active', 'inactive', 'pending_review', 'needs_update'])
                ->default('pending_review')->comment('وضعیت نمایش در سایت');
            $table->enum('sync_status', ['synced', 'pending', 'failed', 'out_of_sync'])
                ->default('pending')->comment('وضعیت همگام‌سازی');
            
            // === Content Source ===
            $table->enum('content_source', ['wordpress', 'manual', 'provider', 'mixed'])
                ->default('wordpress')->comment('منبع محتوا');
            
            // === Content Fields ===
            $table->text('short_description')->nullable()->comment('توضیحات کوتاه');
            $table->longText('full_description')->nullable()->comment('محتوای کامل');
            $table->json('faq')->nullable()->comment('سوالات متداول - JSON');
            $table->json('tags')->nullable()->comment('برچسب‌ها - JSON array');
            $table->json('categories')->nullable()->comment('دسته‌بندی‌ها - JSON array');
            
            // === Media ===
            $table->string('featured_image_url', 500)->nullable()->comment('تصویر شاخص');
            $table->json('gallery_images')->nullable()->comment('گالری تصاویر - JSON array');
            
            // === Location ===
            $table->decimal('latitude', 10, 8)->nullable()->comment('عرض جغرافیایی');
            $table->decimal('longitude', 11, 8)->nullable()->comment('طول جغرافیایی');
            
            // === Facilities & Rating ===
            $table->json('facilities')->nullable()->comment('امکانات - JSON structured');
            $table->decimal('rating', 3, 2)->nullable()->comment('امتیاز (0-5)');
            $table->unsignedInteger('review_count')->default(0)->comment('تعداد نظرات');
            
            // === SEO Fields ===
            $table->string('seo_title', 255)->nullable()->comment('عنوان سئو');
            $table->text('seo_description')->nullable()->comment('توضیحات سئو');
            $table->string('seo_keyword', 255)->nullable()->comment('کلمه کلیدی سئو');
            $table->string('seo_image_url', 500)->nullable()->comment('تصویر شبکه اجتماعی');
            $table->string('canonical_url', 500)->nullable()->comment('لینک کانونیکال');
            
            // === Metadata ===
            $table->json('metadata')->nullable()->comment('متادیتای اضافی - JSON');
            
            // === Timestamps ===
            $table->timestamp('published_at')->nullable()->comment('تاریخ انتشار');
            $table->timestamps();
            $table->softDeletes()->comment('حذف نرم');
            
            // === Indexes ===
            $table->index(['city', 'country']);
            $table->index(['publish_status', 'display_status']);
            $table->index('sync_status');
            $table->index('content_source');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
