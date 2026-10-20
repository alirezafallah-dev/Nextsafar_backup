<?php

namespace App\Modules\Visa\Events;

use App\Modules\Visa\Models\VisaCaseDocument;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisaDocumentVerified
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public VisaCaseDocument $caseDocument,
        public string $verificationStatus, // approved, needs_correction, rejected
        public ?string $notes = null,
    ) {}
}
