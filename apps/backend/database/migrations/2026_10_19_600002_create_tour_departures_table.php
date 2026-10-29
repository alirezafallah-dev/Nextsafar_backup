<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tour_departures')) {
            return; // جدول از قبل وجود دارد
        }
        
        Schema::create('tour_departures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->unsignedSmallInteger('total_capacity')->default(20);
            $table->unsignedSmallInteger('available_seats')->default(20);
            $table->unsignedBigInteger('price_adult')->default(0);
            $table->unsignedBigInteger('price_child')->default(0);
            $table->unsignedBigInteger('price_infant')->default(0);
            $table->unsignedBigInteger('price_single')->default(0);
            $table->enum('status', ['open', 'guaranteed', 'full', 'cancelled'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('tour_id');
            $table->index('departure_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_departures');
    }
};
