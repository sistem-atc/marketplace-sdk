<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL;

use Closure;
use Generator;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Shopify\Exceptions\ShopifyGraphQLException;
use SistemAtc\Marketplaces\Shopify\Exceptions\ShopifyRequestException;

/**
 * Cliente da Shopify Admin API GraphQL (`POST /admin/api/{versao}/graphql.json`).
 *
 * Versao vem de `marketplaces.shopify.graphql_api_version` (default 2026-07) —
 * independente da `api_version` REST, porque o schema usado pelas classes
 * geradas em Queries/ e Mutations/ e' o 2026-07 (exige versao >= 2025-10).
 *
 * - `errors` (top-level) → ShopifyGraphQLException.
 * - `userErrors` de mutation NAO lanca: e' regra de negocio, volta no array
 *   do payload pra quem chamou tratar.
 * - Throttle: HTTP 429 (Retry-After) ou erro `THROTTLED` (calcula espera por
 *   `extensions.cost.throttleStatus`) → retry ate $maxRetries.
 * - 401 → recria o PendingRequest a partir da integration (token novo) 1x.
 */
class GraphQLClient
{
    protected PendingRequest $httpClient;

    /** Funcao de espera (segundos) — trocavel nos testes. */
    protected Closure $sleeper;

    public function __construct(
        PendingRequest $httpClient,
        protected MarketplaceIntegration $integration,
        protected int $maxRetries = 3,
    ) {
        $this->httpClient = $httpClient;
        $this->sleeper = static fn (int $seconds) => sleep($seconds);
    }

    public static function make(MarketplaceIntegration $integration): PendingRequest
    {
        $settings = $integration->getMarketplaceSettings();
        $shop = $settings['shop_domain'] ?? '';
        $version = config('marketplaces.shopify.graphql_api_version', '2026-07');

        return Http::withHeaders([
            'X-Shopify-Access-Token' => $integration->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->baseUrl("https://{$shop}/admin/api/{$version}")->timeout(60);
    }

    public static function forIntegration(MarketplaceIntegration $integration, int $maxRetries = 3): static
    {
        return new static(static::make($integration), $integration, $maxRetries);
    }

    public function withSleeper(Closure $sleeper): static
    {
        $this->sleeper = $sleeper;

        return $this;
    }

    /**
     * Executa um documento e devolve `data`. `userErrors` ficam dentro de
     * `data.<mutation>.userErrors` — nao sao tratados aqui.
     *
     * @param array<string,mixed> $variables
     * @return array<string,mixed>
     */
    public function query(string $document, array $variables = []): array
    {
        $attempt = 0;
        $reauthed = false;

        while (true) {
            $response = $this->httpClient->post('/graphql.json', [
                'query' => $document,
                'variables' => $variables === [] ? new \stdClass : $variables,
            ]);

            if ($response->status() === 401 && ! $reauthed) {
                $reauthed = true;
                $this->httpClient = static::make($this->integration);
                continue;
            }

            if (($response->status() === 429 || $response->status() >= 500) && $attempt < $this->maxRetries) {
                $attempt++;
                ($this->sleeper)((int) ($response->header('Retry-After') ?: 2 ** $attempt));
                continue;
            }

            if ($response->failed()) {
                $this->fail($response);
            }

            $json = $response->json() ?? [];
            $errors = $json['errors'] ?? [];

            if ($errors !== []) {
                $exception = new ShopifyGraphQLException($errors, $json['extensions'] ?? [], $response->status());

                if ($exception->isThrottled() && $attempt < $this->maxRetries) {
                    $attempt++;
                    ($this->sleeper)($this->throttleWait($json['extensions'] ?? [], $attempt));
                    continue;
                }

                Log::warning('Shopify GraphQL errors', [
                    'integration_id' => $this->integration->getIntegrationIdentifier(),
                    'errors' => $errors,
                ]);
                throw $exception;
            }

            return $json['data'] ?? [];
        }
    }

    /**
     * Itera os `node`s de uma connection seguindo `pageInfo.hasNextPage/endCursor`
     * (injeta a variavel `after` a cada pagina). `$connectionPath` e' o caminho
     * em dot-notation dentro de `data` (ex.: `orders` ou `product.variants`).
     *
     * @param array<string,mixed> $variables
     * @return Generator<int,array<string,mixed>>
     */
    public function paginate(string $document, array $variables, string $connectionPath, string $cursorVariable = 'after'): Generator
    {
        do {
            $data = $this->query($document, $variables);
            $connection = data_get($data, $connectionPath) ?? [];

            foreach ($connection['edges'] ?? [] as $edge) {
                if (isset($edge['node'])) {
                    yield $edge['node'];
                }
            }

            foreach ($connection['nodes'] ?? [] as $node) {
                yield $node;
            }

            $pageInfo = $connection['pageInfo'] ?? [];
            $hasNext = (bool) ($pageInfo['hasNextPage'] ?? false);
            $variables[$cursorVariable] = $pageInfo['endCursor'] ?? null;
        } while ($hasNext && $variables[$cursorVariable] !== null);
    }

    /** @param array<string,mixed> $extensions */
    protected function throttleWait(array $extensions, int $attempt): int
    {
        $status = $extensions['cost']['throttleStatus'] ?? [];
        $requested = (float) ($extensions['cost']['requestedQueryCost'] ?? 0);
        $available = (float) ($status['currentlyAvailable'] ?? 0);
        $rate = (float) ($status['restoreRate'] ?? 0);

        if ($rate > 0 && $requested > $available) {
            return max(1, (int) ceil(($requested - $available) / $rate));
        }

        return 2 ** $attempt;
    }

    protected function fail(Response $response): never
    {
        $e = new ShopifyRequestException($response);
        Log::warning('Shopify GraphQL HTTP Error', [
            'status' => $e->status(),
            'integration_id' => $this->integration->getIntegrationIdentifier(),
            'body' => $response->json(),
        ]);
        throw $e;
    }
}
