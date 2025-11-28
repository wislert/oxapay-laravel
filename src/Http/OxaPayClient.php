<?php

namespace OxaPay\Laravel\Http;

use Illuminate\Support\Facades\Http;
use OxaPay\Laravel\Exceptions\HttpException;
use Illuminate\Http\Client\ConnectionException;
use OxaPay\Laravel\Exceptions\NotFoundException;
use OxaPay\Laravel\Exceptions\RateLimitException;
use OxaPay\Laravel\Contracts\OxaPayClientInterface;
use OxaPay\Laravel\Exceptions\ServerErrorException;
use OxaPay\Laravel\Exceptions\InvalidApiKeyException;
use OxaPay\Laravel\Exceptions\ValidationRequestException;
use OxaPay\Laravel\Exceptions\ServiceUnavailableException;

final class OxaPayClient implements OxaPayClientInterface
{
    public function __construct(protected string $baseUrl, protected int $timeout, protected string $version)
    {
        //
    }

    /**
     * Send POST request.
     *
     * @param string $path
     * @param array $payload
     * @param array $headers
     * @throws HttpException|ValidationRequestException|InvalidApiKeyException|NotFoundException|ServerErrorException|ServiceUnavailableException|RateLimitException
     * @return array
     */
    public function post(string $path, array $payload = [], array $headers = []): array
    {
        return $this->handleRequest('post', $path, $payload, $headers);
    }

    /**
     * Send GET request.
     *
     * @param string $path
     * @param array $query
     * @param array $headers
     * @throws HttpException|ValidationRequestException|InvalidApiKeyException|NotFoundException|ServerErrorException|ServiceUnavailableException|RateLimitException
     * @return array
     */
    public function get(string $path, array $query = [], array $headers = []): array
    {
        return $this->handleRequest('get', $path, $query, $headers);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $data
     * @param array $headers
     * @throws HttpException|ValidationRequestException|InvalidApiKeyException|NotFoundException|ServerErrorException|ServiceUnavailableException|RateLimitException
     * @return mixed
     */
    private function handleRequest(string $method, string $path, array $data, array $headers): mixed
    {
        $method = strtolower($method);

        try {
            $req = Http::withHeaders($this->baseHeaders($headers))
                ->timeout($this->timeout)
                ->connectTimeout(5)
                ->withOptions(['verify' => true]);

            if ($method == 'post') {
                $res = $req->acceptJson()->asJson()->post($this->endpoint($path), $data);
            } else {
                $res = $req->get($this->endpoint($path), $data);
            }
        } catch (ConnectionException $e) {
            throw new HttpException($e->getMessage() ?: 'Network error');
        } catch (\Throwable $e) {
            throw new HttpException($e->getMessage() ?: 'HTTP client error');
        }

        $json = $res->json() ?: [];

        if ($res->failed()) {
            throw $this->getSdkException($res->status(), $json, $res->getBody());
        }

        return $json['data'] ?? [];
    }

    /**
     * Build absolute endpoint URL
     *
     * @param string $path
     * @return string
     */
    private function endpoint(string $path): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Merge default headers
     *
     * @param array $headers
     * @return array
     */
    private function baseHeaders(array $headers = []): array
    {
        return array_merge([
            'Origin' => 'oxa-laravel-package-v-' . $this->version,
        ], $headers);
    }

    /**
     * Map HTTP status to exception.
     *
     * @param int $status
     * @param array $json
     * @param string $body
     * @return HttpException|InvalidApiKeyException|NotFoundException|RateLimitException|ServerErrorException|ServiceUnavailableException|ValidationRequestException
     */
    private function getSdkException(int $status, array $json, string $body): HttpException|ServerErrorException|ValidationRequestException|InvalidApiKeyException|ServiceUnavailableException|NotFoundException|RateLimitException
    {
        $base = (string)(($json['message'] ?? $body) ?: 'HTTP error');
        $errM = (string)($json['error']['message'] ?? '');
        $msg  = rtrim($base) . ($errM !== '' ? ' ' . $errM : '');

        $ex = match ($status) {
            400     => new ValidationRequestException($msg),
            401     => new InvalidApiKeyException($msg),
            404     => new NotFoundException($msg),
            429     => new RateLimitException($msg),
            500     => new ServerErrorException($msg),
            503     => new ServiceUnavailableException($msg),
            default => new HttpException($msg),
        };

        $ex->setContext([
            'status'   => $status,
            'response' => $json,
        ]);

        return $ex;
    }
}
