<?php

namespace App\Modules\Visa\Events;

use App\Modules\Visa\Enums\VisaCaseStatus;
use App\Modules\Visa\Models\VisaCase;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisaCaseStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public VisaCase $visaCase,
        public VisaCaseStatus $oldStatus,
        public VisaCaseStatus $newStatus,
        public ?string $reason = null,
    ) {}
}
