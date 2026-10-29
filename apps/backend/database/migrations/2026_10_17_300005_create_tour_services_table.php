<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            
            $table->enum('type', ['included', 'excluded'])->comment('شامل یا غیرشامل');
            $table->string('title', 255)->comment('عنوان خدمت');
            $table->string('icon', 50)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            
            $table->timestamps();
            
            $table->index(['tour_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_services');
    }
};
