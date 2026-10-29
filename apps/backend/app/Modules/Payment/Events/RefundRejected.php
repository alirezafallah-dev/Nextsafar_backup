<?php

namespace App\Modules\Payment\Events;

use App\Modules\Payment\Models\Refund;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefundRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Refund $refund,
        public string $reason
    ) {}
}
