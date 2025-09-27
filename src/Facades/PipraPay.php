<?php

namespace FattainNaime\PipraPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array createCharge(array $data)
 * @method static array verifyPayment(string $pp_id)
 * @method static string getPaymentUrl(array $data)
 * @method static bool validateWebhook(\Illuminate\Http\Request $request)
 *
 * @see \FattainNaime\PipraPay\PipraPayService
 */
class PipraPay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'piprapay';
    }
}
