<?php

namespace App\Modules\Visa\Events;

use App\Modules\Visa\Models\VisaCase;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisaCaseCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public VisaCase $visaCase,
        public bool $wasSuccessful, // approved/rejected
    ) {}
}
