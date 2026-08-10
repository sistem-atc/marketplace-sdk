<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Bases;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
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
     * @param  array<string, string>  $headers  Headers extras DESTA chamada (não
     *   do client). As rotas de publicidade exigem `Api-Version` (1 ou 2 conforme
     *   o produto) e sem ele devolvem 400/404. Parâmetro opcional e no FIM da
     *   assinatura, de propósito: nenhum caller existente quebra.
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

        // Retry on 429 (Rate Limit) or 5xx (Server Error) - simple backoff
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

    /**
     * @param  array<string, string>  $headers
     */
    protected function executeRequest(
        HttpMethod $method,
        string $path,
        array $query,
        array $body,
        array $headers = [],
    ): Response {
        // CLONE obrigatório: `withHeaders()` MUTA o PendingRequest e faz
        // array_merge_recursive nos headers — sem o clone, o `Api-Version: 2` de
        // Product Ads vazaria pra próxima chamada do mesmo client e viraria
        // `Api-Version: [2, 1]` em Display/Brand.
        $client = $headers !== [] ? (clone $this->httpClient)->withHeaders($headers) : $this->httpClient;
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
        $e = new MercadoLivreRequestException($response);
        Log::warning('Mercado Livre HTTP Request Error', [
            'status' => $e->status(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
        ]);
        throw $e;
    }
}
