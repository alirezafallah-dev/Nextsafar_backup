<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->comment('نام دسته‌بندی');
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable()->comment('آیکون (emoji یا class)');
            $table->string('color', 20)->nullable()->comment('رنگ برای نمایش');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('tour_categories')->nullOnDelete();
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_categories');
    }
};
