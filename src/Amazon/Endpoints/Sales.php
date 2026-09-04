<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Support\FlattensCsvQuery;

/**
 * Sales API v1 — métricas agregadas de pedidos (contagem, unidades, receita,
 * ticket médio) por intervalo e granularidade.
 */
class Sales
{
    use FlattensCsvQuery;

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Métricas de pedidos (GET /sales/v1/orderMetrics). `interval` no formato
     * ISO8601 `inicio--fim` (ex.: `2026-08-01T00:00:00-03:00--2026-09-01T00:00:00-03:00`);
     * `granularity`: Hour|Day|Week|Month|Year|Total. Query extra:
     * granularityTimeZone (obrigatório se o interval não tiver offset),
     * buyerType (B2B|B2C|All), fulfillmentNetwork (MFN|AFN), firstDayOfWeek
     * (Monday|Sunday), asin, sku, amazonProgram (AmazonHaul). Dado em
     * `payload[]` (um item por bucket: interval, unitCount, orderItemCount,
     * orderCount, averageUnitPrice, totalSales). Rate limit: 0.5 req/s + burst 15.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function getOrderMetrics(array $marketplaceIds, string $interval, string $granularity, array $query = []): array
    {
        return $this->client->get('/sales/v1/orderMetrics', $this->csv([
            'marketplaceIds' => $marketplaceIds,
            'interval' => $interval,
            'granularity' => $granularity,
        ] + $query));
    }
}
