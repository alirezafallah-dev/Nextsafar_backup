<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('related_tour_id')->constrained('tours')->cascadeOnDelete();
            
            $table->enum('relation_type', [
                'similar',       // مشابه
                'complementary', // مکمل
                'alternative',   // جایگزین
                'upgrade',       // ارتقا
            ])->default('similar');
            
            $table->unsignedTinyInteger('priority')->default(0);
            $table->timestamps();
            
            $table->unique(['tour_id', 'related_tour_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_relations');
    }
};
