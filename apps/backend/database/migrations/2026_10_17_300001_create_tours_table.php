<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            
            // === Basic Info ===
            $table->string('code', 50)->unique()->comment('کد یکتای تور (مثال: TR-IST-001)');
            $table->string('slug', 255)->unique()->comment('اسلاگ URL');
            $table->string('title', 255)->comment('عنوان تور');
            $table->string('subtitle', 500)->nullable()->comment('زیرعنوان یا شعار');
            $table->text('summary')->nullable()->comment('توضیحات کوتاه (برای لیست)');
            $table->longText('description')->nullable()->comment('توضیحات کامل');
            
            // === Destination ===
            $table->string('destination_country', 100)->nullable()->comment('کشور مقصد');
            $table->string('destination_city', 100)->nullable()->comment('شهر مقصد');
            $table->string('origin_city', 100)->nullable()->comment('شهر مبدأ');
            
            // === Duration ===
            $table->unsignedSmallInteger('duration_nights')->nullable()->comment('تعداد شب');
            $table->unsignedSmallInteger('duration_days')->nullable()->comment('تعداد روز');
            
            // === Transport ===
            $table->string('transport_type', 50)->nullable()->comment('airplane, bus, train, car');
            $table->string('airline', 100)->nullable()->comment('نام ایرلاین در صورت وجود');
            
            // === Status ===
            $table->enum('status', [
                'draft',             // پیش‌نویس
                'ready_to_publish',  // آماده انتشار
                'published',         // منتشر شده
                'on_sale',           // در حال فروش
                'sold_out',          // تکمیل ظرفیت
                'expired',           // منقضی شده
                'cancelled',         // لغو شده
                'inactive',          // غیرفعال
            ])->default('draft')->comment('وضعیت تور');
            
            $table->boolean('is_featured')->default(false)->comment('تور ویژه');
            $table->boolean('is_active')->default(true)->comment('فعال بودن');
            
            // === Pricing Overview (از tour_dates محاسبه می‌شود) ===
            $table->unsignedBigInteger('min_price')->nullable()->comment('حداقل قیمت (محاسبه شده)');
            $table->string('currency', 10)->default('IRR')->comment('واحد پول');
            
            // === Category ===
            $table->foreignId('category_id')->nullable()->constrained('tour_categories')->nullOnDelete();
            
            // === SEO ===
            $table->string('seo_title', 255)->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords', 255)->nullable();
            $table->json('meta_tags')->nullable();
            
            // === Cancellation Policy ===
            $table->text('cancellation_policy')->nullable()->comment('قوانین لغو');
            $table->text('important_notes')->nullable()->comment('نکات مهم');
            
            // === Requirements ===
            $table->json('required_documents')->nullable()->comment('مدارک مورد نیاز - JSON array');
            $table->unsignedTinyInteger('min_age')->nullable()->comment('حداقل سن');
            $table->unsignedTinyInteger('max_age')->nullable()->comment('حداکثر سن');
            
            // === Admin ===
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            
            // === Timestamps ===
            $table->timestamps();
            $table->softDeletes();
            
            // === Indexes ===
            $table->index(['status', 'is_active']);
            $table->index('destination_country');
            $table->index('destination_city');
            $table->index('is_featured');
            $table->index('min_price');
            $table->index('category_id');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
