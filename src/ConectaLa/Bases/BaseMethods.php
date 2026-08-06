<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Bases;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Exceptions\ConectaLaRequestException;
use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

abstract class BaseMethods
{
    public function __construct(
        protected PendingRequest $httpClient,
        protected MarketplaceIntegration $integration,
    ) {}

    /**
     * Executa uma request com retry em 429/5xx (backoff exponencial honrando
     * Retry-After). Auth é por header (sem refresh de token), então 401 é erro
     * definitivo. Devolve o JSON decodificado.
     *
     * @return array<mixed>
     */
    protected function makeRequest(
        HttpMethod $method,
        string $path,
        array $query = [],
        array $body = [],
        int $retryAttempt = 0,
    ): array {
        $path = $this->normalizePath($path);
        $response = $this->executeRequest($method, $path, $query, $body);

        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: 2 ** ($retryAttempt + 1));
            sleep($sleep);

            return $this->makeRequest($method, $path, $query, $body, $retryAttempt + 1);
        }

        if ($response->failed()) {
            $this->handleError($response);
        }

        return $response->json() ?? [];
    }

    protected function executeRequest(HttpMethod $method, string $path, array $query, array $body): Response
    {
        $client = $this->httpClient;
        $qs = $query ? '?'.http_build_query($query) : '';

        return match ($method) {
            HttpMethod::GET => $client->get($path, $query),
            HttpMethod::POST => $client->post($path.$qs, $body),
            HttpMethod::PUT => $client->put($path.$qs, $body),
            HttpMethod::PATCH => $client->patch($path.$qs, $body),
            HttpMethod::DELETE => $client->delete($path.$qs, $body),
        };
    }

    protected function normalizePath(string $path): string
    {
        if (! str_starts_with($path, '/')) {
            $path = '/'.$path;
        }

        return preg_replace('#/+#', '/', $path) ?: $path;
    }

    protected function handleError(Response $response): void
    {
        $e = new ConectaLaRequestException($response);
        Log::warning('ConectaLa HTTP Request Error', [
            'status' => $e->status(),
            'message' => $e->getMessage(),
            'url' => $e->url(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);
        throw $e;
    }
}
