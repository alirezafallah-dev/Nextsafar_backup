<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('type');
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('national_id', 10)->nullable();
            $table->string('source')->default('website');
            $table->string('status')->default('new');
            $table->string('priority')->default('normal');
            $table->text('message')->nullable();
            $table->string('destination')->nullable();
            $table->unsignedTinyInteger('adults')->default(1);
            $table->unsignedTinyInteger('children')->default(0);
            $table->unsignedTinyInteger('infants')->default(0);
            $table->date('preferred_date')->nullable();
            $table->unsignedBigInteger('budget')->nullable();
            $table->string('preferred_contact_time')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->timestamp('first_contacted_at')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->unsignedInteger('contact_attempts')->default(0);
            $table->boolean('is_duplicate')->default(false);
            $table->foreignId('duplicate_of')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tour_id')->nullable()->constrained('tours')->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('landing_page')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'priority']);
            $table->index(['type', 'status']);
            $table->index('created_at');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
