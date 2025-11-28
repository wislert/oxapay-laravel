<?php

namespace OxaPay\Laravel;

use OxaPay\Laravel\Endpoints\Common;
use OxaPay\Laravel\Endpoints\Payout;
use OxaPay\Laravel\Services\Webhook;
use OxaPay\Laravel\Endpoints\Account;
use OxaPay\Laravel\Endpoints\Payment;
use OxaPay\Laravel\Http\OxaPayClient;
use OxaPay\Laravel\Endpoints\Exchange;
use OxaPay\Laravel\Concerns\ApiKeyTrait;
use OxaPay\Laravel\Exceptions\WebhookNotReceivedException;

/**
 * Central manager for OxaPay endpoints.
 */
final class OxaPayManager
{
    use ApiKeyTrait;

    public const VERSION   = '1.0.0';
    private const BASE_URL = 'https://api.oxapay.com/v1';

    protected OxaPayClient $client;

    public function __construct()
    {
        $this->client = new OxaPayClient(self::BASE_URL, config('oxapay.timeout') ?: 20, self::VERSION);
    }

    /** Payment APIs.
     *
     * @param string|null $merchantsApiKey
     * @param string|null $callbackUrl
     * @param bool|null $sandbox
     * @return Payment
     */
    public function payment(?string $merchantsApiKey = null, ?string $callbackUrl = null, ?bool $sandbox = null): Payment
    {
        return new Payment($this->client, $this->resolveKey('merchants', $merchantsApiKey), $callbackUrl ?: config('oxapay.callback_url.payment'), $sandbox ?? config('oxapay.sandbox'));
    }

    /** Payout APIs.
     *
     * @param string|null $payoutApiKey
     * @param string|null $callbackUrl
     * @return Payout
     */
    public function payout(?string $payoutApiKey = null, ?string $callbackUrl = null): Payout
    {
        return new Payout($this->client, $this->resolveKey('payouts', $payoutApiKey), $callbackUrl ?: config('oxapay.callback_url.payout'));
    }

    /** Exchange APIs.
     *
     * @param string|null $generalApiKey
     * @return Exchange
     */
    public function exchange(?string $generalApiKey = null): Exchange
    {
        return new Exchange($this->client, $this->resolveKey('general', $generalApiKey));
    }

    /**
     * Common APIs.
     *
     * @return Common
     */
    public function common(): Common
    {
        return new Common($this->client);
    }

    /** Account APIs.
     *
     * @param string|null $generalApiKey
     * @return Account
     */
    public function account(?string $generalApiKey = null): Account
    {
        return new Account($this->client, $this->resolveKey('general', $generalApiKey));
    }

    /**
     * Webhook handler.
     *
     * @param string|null $merchantApiKey
     * @param string|null $payoutApiKey
     * @throws WebhookNotReceivedException
     * @return Webhook
     */
    public function webhook(?string $merchantApiKey = null, ?string $payoutApiKey = null): Webhook
    {
        return new Webhook($merchantApiKey, $payoutApiKey);
    }
}
