<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            
            $table->unsignedTinyInteger('day_number')->comment('شماره روز (1, 2, 3...)');
            $table->string('title', 255)->comment('عنوان روز');
            $table->longText('description')->comment('توضیحات برنامه روز');
            
            // === Meals ===
            $table->boolean('has_breakfast')->default(false);
            $table->boolean('has_lunch')->default(false);
            $table->boolean('has_dinner')->default(false);
            
            // === Accommodation ===
            $table->string('accommodation_name', 255)->nullable()->comment('نام هتل/محل اقامت');
            $table->string('accommodation_type', 50)->nullable()->comment('hotel, camp, etc');
            
            // === Activities ===
            $table->json('activities')->nullable()->comment('لیست فعالیت‌ها - JSON array');
            
            // === Transport ===
            $table->string('transport_info', 500)->nullable()->comment('اطلاعات حمل و نقل');
            
            // === Location ===
            $table->string('location_name', 255)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // === Media ===
            $table->string('image_url', 500)->nullable();
            
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['tour_id', 'day_number']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_itineraries');
    }
};
