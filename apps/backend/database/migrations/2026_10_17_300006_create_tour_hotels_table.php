<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            
            $table->enum('relation_type', ['main', 'alternative', 'similar'])
                ->default('main')->comment('نوع ارتباط');
            $table->unsignedTinyInteger('priority')->default(1)->comment('اولویت نمایش');
            $table->unsignedSmallInteger('nights')->nullable()->comment('تعداد شب اقامت');
            $table->string('room_type', 100)->nullable()->comment('نوع اتاق');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->unique(['tour_id', 'hotel_id', 'relation_type']);
            $table->index(['tour_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_hotels');
    }
};
