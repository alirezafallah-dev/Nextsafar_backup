<?php

namespace App\Modules\Visa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisaCasePassenger extends Model
{
    protected $fillable = [
        'visa_case_id', 'type', 'first_name', 'last_name', 'first_name_en', 'last_name_en',
        'father_name', 'father_name_en', 'national_id', 'passport_number',
        'birth_date', 'birth_place', 'gender', 'nationality', 'marital_status',
        'phone', 'email', 'address',
        'occupation', 'employer_name', 'employer_address', 'employer_phone', 'monthly_income',
        'education_level', 'university_name', 'field_of_study',
        'has_previous_travel', 'previous_travels', 'has_visa_refusal', 'refusal_details',
        'relation_to_owner', 'is_main_applicant',
        'documents_uploaded', 'documents_approved', 'documents_complete',
        'service_fee', 'embassy_fee',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'has_previous_travel' => 'boolean',
        'previous_travels' => 'array',
        'has_visa_refusal' => 'boolean',
        'is_main_applicant' => 'boolean',
        'documents_uploaded' => 'integer',
        'documents_approved' => 'integer',
        'documents_complete' => 'boolean',
        'monthly_income' => 'integer',
        'service_fee' => 'integer',
        'embassy_fee' => 'integer',
    ];

    public function visaCase(): BelongsTo
    {
        return $this->belongsTo(VisaCase::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VisaCaseDocument::class, 'passenger_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getFullNameEnAttribute(): string
    {
        return trim("{$this->first_name_en} {$this->last_name_en}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }
}
