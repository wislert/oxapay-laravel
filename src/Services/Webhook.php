<?php

namespace OxaPay\Laravel\Services;

use OxaPay\Laravel\Concerns\ApiKeyTrait;
use OxaPay\Laravel\Exceptions\WebhookSignatureException;
use OxaPay\Laravel\Exceptions\WebhookNotReceivedException;

final class Webhook
{
    use ApiKeyTrait;

    private array $data;
    private array $apiKeys;

    /**
     * @param string|null $merchantApiKey
     * @param string|null $payoutApiKey
     */
    public function __construct(?string $merchantApiKey = null, ?string $payoutApiKey = null)
    {
        if (!$this->data = request()->all()) {
            throw new WebhookNotReceivedException('Webhook is not received!');
        }

        $this->apiKeys = [
            'merchants' => $merchantApiKey,
            'payouts'   => $payoutApiKey,
        ];
    }

    /**
     * Set merchant api key
     *
     * @param string $merchantApiKey
     * @return $this
     */
    public function setMerchantApiKey(string $merchantApiKey): Webhook
    {
        $this->apiKeys['merchants'] = $merchantApiKey;

        return $this;
    }

    /**
     * Set payout api key
     *
     * @param string $payoutApiKey
     * @return $this
     */
    public function setPayoutApiKey(string $payoutApiKey): Webhook
    {
        $this->apiKeys['payouts'] = $payoutApiKey;

        return $this;
    }

    /**
     * Get webhook payload.
     *
     * @param bool $verify Validate HMAC if true
     * @throws WebhookSignatureException
     * @return array
     */
    public function getData(bool $verify = true): array
    {
        if ($verify) {
            $this->verify();
        }

        return $this->data;
    }

    /**
     * Validate HMAC signature (sha512 over raw body).
     *
     * @return void
     */
    public function verify(): void
    {
        $hmac = request()->header('hmac');
        if (!$hmac) {
            throw new WebhookSignatureException('Missing HMAC header.');
        }

        $content = request()->getContent();

        $calc = hash_hmac('sha512', $content, $this->resolveApiKey($this->data['type'] ?? ''));

        if (!hash_equals($calc, (string)$hmac)) {
            $exception = new WebhookSignatureException('Invalid HMAC signature.');
            $exception->setContext(['content' => $content, 'hmac' => $hmac, 'new_hmac' => $calc]);

            throw $exception;
        }
    }

    /**
     * Resolve API key from payload type.
     *
     * @param string $type
     * @return string
     */
    private function resolveApiKey(string $type): string
    {
        $group = match (true) {
            in_array($type, ['invoice', 'white_label', 'static_address', 'payment_link', 'donation'], true) => 'merchants',
            $type === 'payout'                                                                              => 'payouts',

            default => 'merchants',
        };

        $key = $this->apiKeys[$group] ?? null;

        return $this->resolveKey($group, $key);
    }

}
