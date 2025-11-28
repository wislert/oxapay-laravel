<?php

namespace OxaPay\Laravel\Endpoints;

use OxaPay\Laravel\Http\OxaPayClient;
use OxaPay\Laravel\Concerns\CallbackUrlTrait;
use OxaPay\Laravel\Exceptions\MissingAddressException;
use OxaPay\Laravel\Exceptions\MissingTrackIdException;

final class Payout
{
    use CallbackUrlTrait;

    public function __construct(protected OxaPayClient $client, protected ?string $apiKey, ?string $callbackUrl = null)
    {
        $this->callbackUrl = $callbackUrl ?: '';
    }

    /**
     * @return array
     */
    protected function headers(): array
    {
        return ['payout_api_key' => $this->apiKey];
    }

    /**
     * Generate a payout request.
     *
     * @param array $data
     * @param string|null $callbackUrl
     * @return array
     */
    public function generate(array $data, ?string $callbackUrl = null): array
    {
        if (!($data['address'] ?? '')) {
            throw new MissingAddressException('address must be provided!');
        }

        return $this->client->post('payout', $this->setCallbackUrl($data, $callbackUrl), $this->headers());
    }

    /**
     * Get payout information by track id.
     *
     * @param int|string $trackId
     * @return array
     */
    public function information(int|string $trackId): array
    {
        if (!$trackId) {
            throw new MissingTrackIdException('Track id must be provided');
        }

        return $this->client->get('payout/' . $trackId, [], $this->headers());
    }

    /**
     * Get payout history.
     *
     * @param array $filters
     * @return array
     */
    public function history(array $filters = []): array
    {
        return $this->client->get('payout', $filters, $this->headers());
    }
}
