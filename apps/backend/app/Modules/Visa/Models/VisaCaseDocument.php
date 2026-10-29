<?php

namespace App\Modules\Visa\Models;

use App\Models\User;
use App\Modules\Document\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaCaseDocument extends Model
{
    protected $fillable = [
        'visa_case_id', 'document_id', 'checklist_item_id', 'passenger_id',
        'verification_status', 'verified_by', 'verified_at', 'verification_notes',
        'translation_status', 'needs_translation', 'is_original',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'needs_translation' => 'boolean',
        'is_original' => 'boolean',
    ];

    public function visaCase(): BelongsTo
    {
        return $this->belongsTo(VisaCase::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(VisaDocumentChecklist::class);
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(VisaCasePassenger::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->verification_status === 'approved';
    }

    public function needsCorrection(): bool
    {
        return $this->verification_status === 'needs_correction';
    }
}
