<?php

namespace FattainNaime\PipraPay\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PipraPayPaymentCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public array $payload)
    {
    }
}
