<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_case_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_case_id')->constrained('visa_cases')->cascadeOnDelete();
            
            // === اطلاعات اصلی ===
            $table->enum('type', ['adult', 'child', 'infant'])->default('adult');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('first_name_en', 100)->nullable()->comment('نام لاتین');
            $table->string('last_name_en', 100)->nullable()->comment('نام خانوادگی لاتین');
            $table->string('father_name', 100)->nullable();
            $table->string('father_name_en', 100)->nullable();
            
            // === اطلاعات شناسنامه‌ای ===
            $table->string('national_id', 20)->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 150)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('marital_status', 30)->nullable()->comment('single, married, divorced, widow');
            
            // === اطلاعات تماس ===
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            
            // === شغلی ===
            $table->string('occupation', 150)->nullable();
            $table->string('employer_name', 200)->nullable();
            $table->string('employer_address', 500)->nullable();
            $table->string('employer_phone', 20)->nullable();
            $table->unsignedBigInteger('monthly_income')->nullable();
            
            // === تحصیلی ===
            $table->string('education_level', 100)->nullable();
            $table->string('university_name', 200)->nullable();
            $table->string('field_of_study', 200)->nullable();
            
            // === سفر قبلی ===
            $table->boolean('has_previous_travel')->default(false);
            $table->json('previous_travels')->nullable()->comment('لیست سفرهای قبلی');
            $table->boolean('has_visa_refusal')->default(false)->comment('قبلاً ویزا رد شده؟');
            $table->text('refusal_details')->nullable();
            
            // === رابطه با مالک پرونده ===
            $table->string('relation_to_owner', 50)->nullable()
                ->comment('self, spouse, child, parent, sibling, other');
            $table->boolean('is_main_applicant')->default(false)
                ->comment('متقاضی اصلی پرونده');
            
            // === وضعیت مدارک این مسافر ===
            $table->unsignedTinyInteger('documents_uploaded')->default(0);
            $table->unsignedTinyInteger('documents_approved')->default(0);
            $table->boolean('documents_complete')->default(false);
            
            // === قیمت ===
            $table->unsignedBigInteger('service_fee')->default(0);
            $table->unsignedBigInteger('embassy_fee')->default(0);
            
            $table->timestamps();
            
            // === Indexes ===
            $table->index('visa_case_id');
            $table->index('national_id');
            $table->index('passport_number');
            $table->index('is_main_applicant');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_case_passengers');
    }
};
