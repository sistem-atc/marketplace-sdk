<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Support\FlattensCsvQuery;

/**
 * Orders API 2026-01-01 — a geração nova da Orders (v0 continua em `Orders`).
 *
 * Diferenças pro v0: resposta camelCase sem `payload`; datasets opcionais via
 * `includedData` (BUYER, RECIPIENT, PROCEEDS, EXPENSE, PROMOTION, CANCELLATION,
 * FULFILLMENT, PACKAGES, TAX, PAYMENT, FULFILLMENT_ORDERS) — com PROCEEDS/
 * EXPENSE o pedido já vem com receita/despesa (sem ir na Finances); paginação
 * por `paginationToken`. BUYER/RECIPIENT são PII → passe `restricted: true`
 * (RDT pro path; o Client não envia dataElements no RDT, então se a Amazon
 * exigir dataElements o retorno vem sem esses blocos).
 */
class OrdersV2026
{
    use FlattensCsvQuery;

    private const BASE = '/orders/2026-01-01/orders';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Busca pedidos (GET /orders/2026-01-01/orders). Query: createdAfter/
     * createdBefore, lastUpdatedAfter/lastUpdatedBefore (ISO8601),
     * fulfillmentStatuses[] (PENDING_AVAILABILITY, PENDING, UNSHIPPED,
     * PARTIALLY_SHIPPED, SHIPPED, CANCELLED, UNFULFILLABLE), marketplaceIds[],
     * fulfilledBy[] (MERCHANT|AMAZON), maxResultsPerPage, paginationToken,
     * includedData[]. Arrays viram csv. Retorna `orders[]` + `pagination`.
     * Rate limit: 0.0056 req/s + burst 20 (~1 a cada 3 min! — use o v0 pra
     * varredura frequente).
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function searchOrders(array $query = [], bool $restricted = false): array
    {
        $query = $this->csv($query);

        return $restricted
            ? $this->client->getRestricted(self::BASE, $query)
            : $this->client->get(self::BASE, $query);
    }

    /**
     * Um pedido (GET /orders/2026-01-01/orders/{orderId}) com os datasets de
     * `includedData`. Retorna `order`. Rate limit: 0.5 req/s + burst 30.
     *
     * @param  list<string>  $includedData
     * @return array<string, mixed>
     */
    public function getOrder(string $amazonOrderId, array $includedData = [], bool $restricted = false): array
    {
        $path = self::BASE.'/'.rawurlencode($amazonOrderId);
        $query = $includedData !== [] ? ['includedData' => implode(',', $includedData)] : [];

        return $restricted
            ? $this->client->getRestricted($path, $query)
            : $this->client->get($path, $query);
    }
}
