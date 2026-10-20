<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_old_slugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->string('slug', 255)->index();
            $table->timestamp('changed_at');
            $table->unsignedInteger('redirect_count')->default(0);
            
            $table->unique(['tour_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_old_slugs');
    }
};
