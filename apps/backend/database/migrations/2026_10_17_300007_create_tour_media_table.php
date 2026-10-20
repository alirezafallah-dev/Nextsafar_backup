<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            
            $table->enum('type', ['image', 'video', 'document', 'brochure'])
                ->default('image');
            $table->string('path', 500)->comment('مسیر یا URL فایل');
            $table->string('title', 255)->nullable();
            $table->text('alt_text')->nullable();
            $table->text('caption')->nullable();
            
            $table->boolean('is_cover')->default(false)->comment('تصویر کاور');
            $table->boolean('is_public')->default(true)->comment('عمومی یا خصوصی');
            
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            
            $table->timestamps();
            
            $table->index(['tour_id', 'is_cover']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_media');
    }
};
