<?php

namespace OxaPay\Laravel\Endpoints;

use OxaPay\Laravel\Http\OxaPayClient;

final class Account
{
    public function __construct(protected OxaPayClient $client, protected ?string $apiKey)
    {
        //
    }

    /**
     * @return array
     */
    protected function headers(): array
    {
        return ['general_api_key' => $this->apiKey];
    }

    /**
     * Get account balance.
     *
     * @param string $currency
     * @return array
     */
    public function balance(string $currency = ''): array
    {
        return $this->client->get('general/account/balance', ['currency' => $currency], $this->headers());
    }
}
