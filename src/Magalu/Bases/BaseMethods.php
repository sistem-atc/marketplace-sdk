<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Bases;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Exceptions\MagaluRequestException;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

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
        $path = $this->normalizePath($path);
        $response = $this->executeRequest($method, $path, $query, $body);

        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->makeRequest($method, $path, $query, $body, $retryAttempt + 1);
        }

        if ($response->status() === 401 && $retryAttempt === 0) {
            $this->httpClient = HttpClientFactory::make($this->integration);
            return $this->makeRequest($method, $path, $query, $body, $retryAttempt + 1);
        }

        if ($response->failed()) {
            $this->handleError($response);
        }

        return $response->json() ?? [];
    }

    protected function executeRequest(
        HttpMethod $method,
        string $path,
        array $query,
        array $body,
    ): Response {
        $client = $this->httpClient;
        return match ($method) {
            HttpMethod::GET => $client->get($path, $query),
            HttpMethod::POST => $client->post($path.($query ? '?'.http_build_query($query) : ''), $body),
            HttpMethod::PUT => $client->put($path.($query ? '?'.http_build_query($query) : ''), $body),
            HttpMethod::PATCH => $client->patch($path.($query ? '?'.http_build_query($query) : ''), $body),
            HttpMethod::DELETE => $client->delete($path.($query ? '?'.http_build_query($query) : ''), $body),
        };
    }

    protected function normalizePath(string $path): string
    {
        if (! str_starts_with($path, '/')) $path = '/'.$path;
        return preg_replace('#/+#', '/', $path) ?: $path;
    }

    protected function handleError(Response $response): void
    {
        $e = new MagaluRequestException($response);
        Log::warning('Magalu HTTP Request Error', [
            'status' => $e->status(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);
        throw $e;
    }
}
