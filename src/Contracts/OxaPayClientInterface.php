<?php

namespace OxaPay\Laravel\Contracts;

use OxaPay\Laravel\Exceptions\HttpException;
use OxaPay\Laravel\Exceptions\NotFoundException;
use OxaPay\Laravel\Exceptions\RateLimitException;
use OxaPay\Laravel\Exceptions\ServerErrorException;
use OxaPay\Laravel\Exceptions\InvalidApiKeyException;
use OxaPay\Laravel\Exceptions\ValidationRequestException;
use OxaPay\Laravel\Exceptions\ServiceUnavailableException;

interface OxaPayClientInterface
{
    /**
     * Send POST request.
     *
     * @param string $path
     * @param array $payload
     * @param array $headers
     * @throws HttpException|ValidationRequestException|InvalidApiKeyException|NotFoundException|ServerErrorException|ServiceUnavailableException|RateLimitException
     * @return array
     */
    public function post(string $path, array $payload = [], array $headers = []): array;

    /**
     * Send GET request.
     *
     * @param string $path
     * @param array $query
     * @param array $headers
     * @throws HttpException|ValidationRequestException|InvalidApiKeyException|NotFoundException|ServerErrorException|ServiceUnavailableException|RateLimitException
     * @return array
     */
    public function get(string $path, array $query = [], array $headers = []): array;
}
