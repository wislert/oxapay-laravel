<?php

namespace OxaPay\Laravel\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \OxaPay\Laravel\Endpoints\Payment payment(?string $merchantsApiKey = null, ?string $callbackUrl = null, ?bool $sandbox = null)
 * @method static \OxaPay\Laravel\Endpoints\Payout payout(?string $payoutApiKey = null, ?string $callbackUrl = null)
 * @method static \OxaPay\Laravel\Endpoints\Exchange exchange(?string $generalApiKey = null)
 * @method static \OxaPay\Laravel\Endpoints\Common common()
 * @method static \OxaPay\Laravel\Endpoints\Account account(?string $generalApiKey = null)
 * @method static \OxaPay\Laravel\Services\Webhook webhook(?string $merchantApiKey = null, ?string $payoutApiKey = null)
 */
class OxaPay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'oxapay.manager';
    }
}
