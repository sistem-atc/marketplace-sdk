<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Bases;

use Generator;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use InvalidArgumentException;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\MercadoPago\Exceptions\MercadoPagoRequestException;
use SistemAtc\Marketplaces\MercadoPago\Support\HttpClientFactory;

/**
 * Base HTTP dos endpoints Mercado Pago.
 *
 * Espelha o comportamento do SDK oficial (mercadopago/sdk-php) que importa
 * pro ERP, sem trazer o cliente cURL dele:
 *
 *   - `X-Idempotency-Key` automatico em POST/PUT/PATCH. O MP exige a chave
 *     em pagamentos e a recomenda nas demais escritas; sem ela um retry de
 *     rede pode DUPLICAR um pagamento/reembolso. O caller pode passar a
 *     propria chave (ex.: id do pedido) via `$headers` pra que a repeticao
 *     legitima devolva o mesmo recurso.
 *   - Retry com backoff em 429/5xx e re-autenticacao unica em 401
 *     (o token e' renovado pelo TokenRefresher no HttpClientFactory).
 *   - `paginate()` = MPAutoPaginationGenerator do SDK: percorre `results`/
 *     `data`/`elements` ate' `paging.total`, que vem como int em payments e
 *     como STRING em Orders v2.
 */
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
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers  Headers extras DESTA chamada
     *   (ex.: `X-Idempotency-Key` proprio). Nao vazam pro client.
     * @return array<string, mixed>
     */
    protected function makeRequest(
        HttpMethod $method,
        string $path,
        array $query = [],
        array $body = [],
        int $retryAttempt = 0,
        array $headers = []
    ): array {
        if ($retryAttempt === 0) {
            $headers = $this->withIdempotencyKey($method, $headers);
        }

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
            throw new MercadoPagoRequestException($response);
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
        array $headers = []
    ): Response {
        // CLONE: withHeaders() muta o PendingRequest — sem clonar, a chave de
        // idempotencia de uma chamada vazaria pra proxima do mesmo client.
        $client = $headers !== [] ? (clone $this->httpClient)->withHeaders($headers) : $this->httpClient;
        $withQuery = $path.($query !== [] ? '?'.http_build_query($query) : '');

        return match ($method) {
            HttpMethod::GET => $client->get($path, $query),
            HttpMethod::POST => $client->post($withQuery, $body),
            HttpMethod::PUT => $client->put($withQuery, $body),
            HttpMethod::PATCH => $client->patch($withQuery, $body),
            HttpMethod::DELETE => $client->delete($withQuery, $body),
        };
    }

    /**
     * Percorre TODAS as paginas de um endpoint `/search` do MP e devolve
     * cada item. Mesmo criterio de parada do SDK oficial: acabou quando a
     * pagina volta vazia, quando veio menos que `limit` ou quando
     * `offset >= paging.total`.
     *
     * @param  array<string, mixed>  $filters
     * @return Generator<int, array<string, mixed>>
     */
    protected function paginate(string $path, array $filters = [], int $limit = 100, int $offset = 0): Generator
    {
        if ($limit < 1) {
            throw new InvalidArgumentException('limit deve ser >= 1');
        }

        while (true) {
            $page = $this->makeRequest(
                method: HttpMethod::GET,
                path: $path,
                query: $filters + ['limit' => $limit, 'offset' => $offset],
            );

            $items = $page['results'] ?? $page['data'] ?? $page['elements'] ?? [];

            if ($items === []) {
                return;
            }

            foreach ($items as $item) {
                yield $item;
            }

            $offset += count($items);
            $total = isset($page['paging']['total']) ? (int) $page['paging']['total'] : null;

            if (count($items) < $limit || ($total !== null && $offset >= $total)) {
                return;
            }
        }
    }

    /**
     * @param  array<string, string>  $headers
     * @return array<string, string>
     */
    private function withIdempotencyKey(HttpMethod $method, array $headers): array
    {
        if (! in_array($method, [HttpMethod::POST, HttpMethod::PUT, HttpMethod::PATCH], true)) {
            return $headers;
        }

        foreach (array_keys($headers) as $name) {
            if (strtolower($name) === 'x-idempotency-key') {
                return $headers;
            }
        }

        $headers['X-Idempotency-Key'] = (string) Str::uuid();

        return $headers;
    }
}
