<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Netshoes\Bases;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Netshoes\Exceptions\NetshoesRequestException;
use SistemAtc\Marketplaces\Netshoes\Support\HttpClientFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

abstract class BaseMethods
{
    protected PendingRequest $httpClient;

    public function __construct(
        PendingRequest $httpClient,
        protected MarketplaceIntegration $integration
    ) {
        $this->httpClient = $httpClient;
    }

    /**
     * O path e' SEMPRE completo, com a versao (`/api/v1/...` ou `/api/v2/...`):
     * a base NAO prefixa versao — cada metodo escolhe a sua. Swagger oficial:
     * pedidos/protocolos/templates em V1, produtos/precos/estoque em V2.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers  headers extras por request (ex idSeller)
     * @return array<int|string, mixed>
     */
    protected function makeRequest(
        HttpMethod $method,
        string $path,
        array $query = [],
        array $body = [],
        int $retryAttempt = 0,
        array $headers = []
    ): array {
        $path = $this->normalizePath($path);
        $response = $this->executeRequest($method, $path, $query, $body, $headers);

        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);

            return $this->makeRequest($method, $path, $query, $body, $retryAttempt + 1, $headers);
        }

        // Auth e' por header estatico (sem exchange). Um 401 reidrata o cliente
        // a partir da Integration uma vez (cobre token rotacionado na DB).
        if ($response->status() === 401 && $retryAttempt === 0) {
            $this->httpClient = HttpClientFactory::make($this->integration);

            return $this->makeRequest($method, $path, $query, $body, $retryAttempt + 1, $headers);
        }

        if ($response->failed()) {
            $this->handleError($response);
        }

        return $response->json() ?? [];
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     */
    protected function executeRequest(
        HttpMethod $method,
        string $path,
        array $query,
        array $body,
        array $headers = [],
    ): Response {
        // Clone: withHeaders muta o PendingRequest e vazaria pra proxima chamada.
        $client = $headers ? (clone $this->httpClient)->withHeaders($headers) : $this->httpClient;

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
        if (! str_starts_with($path, '/')) {
            $path = '/'.$path;
        }

        return preg_replace('#/+#', '/', $path) ?: $path;
    }

    protected function handleError(Response $response): void
    {
        $e = new NetshoesRequestException($response);
        Log::warning('Netshoes HTTP Request Error', [
            'status' => $e->status(),
            'message' => $e->getMessage(),
            'url' => $e->url(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);
        throw $e;
    }
}
