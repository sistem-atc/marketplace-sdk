<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Mirakl\Bases;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Mirakl\Exceptions\MiraklRequestException;
use SistemAtc\Marketplaces\Mirakl\Support\HttpClientFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

abstract class BaseMethods
{
    protected PendingRequest $httpClient;

    public function __construct(
        PendingRequest $httpClient,
        protected MarketplaceIntegration $integration
    ) {
        $this->httpClient = $httpClient;
    }

    protected function makeRequest(
        HttpMethod $method,
        string $path,
        array $query = [],
        array $body = [],
        int $retryAttempt = 0
    ): array {
        $response = $this->executeRequest($method, $path, $query, $body);

        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->makeRequest($method, $path, $query, $body, $retryAttempt + 1);
        }

        if ($response->failed()) {
            throw new MiraklRequestException($response);
        }

        return $response->json() ?? [];
    }

    protected function executeRequest(HttpMethod $method, string $path, array $query, array $body): Response
    {
        return match ($method) {
            HttpMethod::GET => $this->httpClient->get($path, $query),
            HttpMethod::POST => $this->httpClient->post($path, $body),
            HttpMethod::PUT => $this->httpClient->put($path, $body),
            HttpMethod::DELETE => $this->httpClient->delete($path, $body),
            default => throw new \InvalidArgumentException("Method not supported"),
        };
    }
}
