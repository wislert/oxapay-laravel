<?php

namespace OxaPay\Laravel\Concerns;

use OxaPay\Laravel\Exceptions\MissingApiKeyException;

trait ApiKeyTrait
{
    /**
     * Resolve API key for a group.
     *
     * @param string $group merchants|payouts|general
     * @param string|null $apiKey raw key or slot
     * @throws MissingApiKeyException
     * @return string
     */
    private function resolveKey(string $group, ?string $apiKey = null): string
    {
        $map = match ($group) {
            'merchants' => config('oxapay.merchants'),
            'payouts'   => config('oxapay.payouts'),
            'general'   => config('oxapay.general'),
            default     => [],
        };

        if ($apiKey !== null && $apiKey !== '') {
            if ($apiKey !== 'default'
                && isset($map[$apiKey])
                && is_string($map[$apiKey])
                && $map[$apiKey] !== ''
            ) {
                return (string)$map[$apiKey]; // slot → raw key
            }

            return (string)$apiKey; // raw key
        }

        $default = isset($map['default']) ? (string)$map['default'] : '';
        if ($default) {
            return $default;
        }

        throw new MissingApiKeyException("API key for {$group} is not configured.");
    }
}
