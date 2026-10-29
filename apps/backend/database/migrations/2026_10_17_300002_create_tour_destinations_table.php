<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->comment('نام مقصد');
            $table->string('slug', 150)->unique();
            $table->string('country', 100);
            $table->string('city', 100)->nullable();
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->boolean('is_popular')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['country', 'city']);
            $table->index('is_popular');
        });
        
        // Pivot: tours <-> destinations
        Schema::create('tour_destination_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained('tour_destinations')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_number')->nullable()->comment('روز بازدید');
            $table->unsignedTinyInteger('sort_order')->default(0);
            
            $table->unique(['tour_id', 'destination_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_destination_map');
        Schema::dropIfExists('tour_destinations');
    }
};
