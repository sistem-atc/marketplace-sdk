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

    /**
     * Requisicao JSON autenticada (Bearer + X-Tenant-Id ja' vem da factory).
     *
     * `$path` relativo cai na base `api.magalu.com`; URL absoluta (ver
     * `servicesUrl()`) e' usada como esta'. `$headers` sao extras por chamada
     * (ex.: `X-Channel-Id`) sem contaminar o client compartilhado.
     *
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    protected function makeRequest(
        HttpMethod $method,
        string $path,
        array $query = [],
        array $body = [],
        int $retryAttempt = 0,
        array $headers = [],
    ): array {
        $path = $this->normalizePath($path);
        $response = $this->executeRequest($method, $path, $query, $body, $headers);

        if (($response->status() === 429 || $response->status() >= 500) && $retryAttempt < 3) {
            $sleep = (int) ($response->header('Retry-After') ?: pow(2, $retryAttempt + 1));
            sleep($sleep);
            return $this->makeRequest($method, $path, $query, $body, $retryAttempt + 1, $headers);
        }

        if ($response->status() === 401 && $retryAttempt === 0) {
            $this->httpClient = HttpClientFactory::make($this->integration);
            return $this->makeRequest($method, $path, $query, $body, $retryAttempt + 1, $headers);
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
        array $headers = [],
    ): Response {
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
        // URL absoluta (services.magalu.com etc.) passa intacta — o PendingRequest
        // do Laravel so' prefixa a baseUrl quando o path nao comeca com http(s)://.
        if (preg_match('#^https?://#i', $path)) return $path;
        if (! str_starts_with($path, '/')) $path = '/'.$path;
        return preg_replace('#/+#', '/', $path) ?: $path;
    }

    /**
     * URL absoluta na base `services.magalu.com` (Magalu Entregas/Magalog,
     * fiscal-management, fiscal-documents, inventories, conversations,
     * questions, transportadora, smart-label). Mesmo Bearer + X-Tenant-Id
     * do client — so' muda o host. Config: marketplaces.magalu.services_base.
     */
    protected function servicesUrl(string $path): string
    {
        $base = rtrim((string) config('marketplaces.magalu.services_base', 'https://services.magalu.com'), '/');

        return $base.$this->normalizePath($path);
    }

    /**
     * Requisicao cujo corpo de resposta NAO e' JSON (PDF/ZPL/anexo binario).
     * Devolve o body cru; erro HTTP vira MagaluRequestException.
     *
     * @param array<string, mixed> $query
     */
    protected function rawGet(string $path, array $query = []): string
    {
        $response = $this->httpClient->get($this->normalizePath($path), $query);
        if ($response->failed()) $this->handleError($response);

        return $response->body();
    }

    protected function handleError(Response $response): void
    {
        $e = new MagaluRequestException($response);
        Log::warning('Magalu HTTP Request Error', [
            'status' => $e->status(),
            'magalu_error' => $e->magaluError(),
            'magalu_message' => $e->magaluMessage(),
            'url' => $e->url(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);
        throw $e;
    }
}
