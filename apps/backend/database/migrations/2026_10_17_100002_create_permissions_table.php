<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->comment('نام نمایشی دسترسی');
            $table->string('slug', 100)->unique()->comment('شناسه یکتا: module.action');
            $table->string('group', 50)->comment('گروه دسترسی: tours, hotels, users, etc');
            $table->text('description')->nullable()->comment('توضیحات');
            $table->boolean('is_sensitive')->default(false)->comment('دسترسی حساس (نیاز به لاگ)');
            $table->timestamps();
            
            $table->index('group');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
