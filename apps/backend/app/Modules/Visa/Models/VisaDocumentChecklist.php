<?php

namespace App\Modules\Visa\Models;

use App\Modules\Document\Enums\DocumentCategory;
use App\Modules\Visa\Enums\ChecklistImportance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaDocumentChecklist extends Model
{
    protected $table = 'visa_document_checklist';

    protected $fillable = [
        'visa_type_id', 'title', 'description', 'sample_url',
        'document_category', 'allowed_mime_types', 'max_file_size',
        'max_files', 'accept_multiple', 'is_required', 'is_per_passenger',
        'importance', 'sort_order',
    ];

    protected $casts = [
        'allowed_mime_types' => 'array',
        'max_file_size' => 'integer',
        'max_files' => 'integer',
        'accept_multiple' => 'boolean',
        'is_required' => 'boolean',
        'is_per_passenger' => 'boolean',
        'importance' => ChecklistImportance::class,
        'sort_order' => 'integer',
    ];

    public function visaType(): BelongsTo
    {
        return $this->belongsTo(VisaType::class);
    }

    /**
     * دریافت DocumentCategory مرتبط
     */
    public function getDocumentCategoryEnumAttribute(): DocumentCategory
    {
        return DocumentCategory::tryFrom($this->document_category) ?? DocumentCategory::GENERAL;
    }

    /**
     * دریافت حداکثر حجم به مگابایت
     */
    public function getMaxSizeMbAttribute(): float
    {
        return $this->max_file_size ? round($this->max_file_size / 1024 / 1024, 2) : 10;
    }
}
