<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Seller;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * Identidade e infra do seller: quem sou (/seller/v1/portfolios/me), meus
 * armazens, score dos produtos e o estoque logistico consolidado
 * (/logistics/v1/inventories, base `services.magalu.com`).
 *
 * O onboarding de webhooks continua em WebhookMethods (signup/signoff).
 */
class SellerMethods extends BaseMethods
{
    /**
     * Tenant/canal/seller do token atual (GET /seller/v1/portfolios/me).
     * Util pra descobrir o `channel.id` exigido em precos/estoque.
     *
     * @return array<string, mixed>
     */
    public function me(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/me');
    }

    /**
     * Armazens (branches) do seller no portfolio (GET /seller/v1/portfolios/me/warehouses).
     *
     * @return array<string, mixed>
     */
    public function myWarehouses(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/me/warehouses');
    }

    /**
     * Score dos produtos no canal (GET /seller/v1/portfolios/products/scores).
     * Exige header `X-Channel-Id`; paginacao `limit`/`offset` SEM underscore.
     *
     * @return array<string, mixed>
     */
    public function productScores(string $channelId, int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/seller/v1/portfolios/products/scores',
            ['limit' => $limit, 'offset' => $offset],
            headers: ['X-Channel-Id' => $channelId],
        );
    }

    /**
     * Estoque logistico consolidado por SKU (GET /logistics/v1/inventories) —
     * total/disponivel/bloqueado por filial. `skus` vira `sku__in` (CSV).
     * Endpoint em piloto na doc.
     *
     * @param list<string> $skus
     * @return array<string, mixed>
     */
    public function listInventories(array $skus = [], int $limit = 50, int $offset = 0): array
    {
        $query = ['_limit' => $limit, '_offset' => $offset];
        if ($skus) $query['sku__in'] = implode(',', $skus);

        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl('/logistics/v1/inventories'), $query);
    }

    /**
     * Solicita relatorio de estoque/kardex (POST /logistics/v1/inventories/reports).
     *
     * `subType`: inventory_kardex | kardex_fulfillment; `fileExtension`:
     * csv|xlsx|json|txt. Resposta traz `id` pra acompanhar em getInventoryReport().
     *
     * @param array<string, mixed> $parameters
     * @return array<string, mixed>
     */
    public function requestInventoryReport(
        string $org,
        string $channel,
        string $subType = 'inventory_kardex',
        string $fileExtension = 'csv',
        array $parameters = [],
    ): array {
        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl('/logistics/v1/inventories/reports'), [], $this->reportBody($org, $channel, $subType, $fileExtension, $parameters));
    }

    /**
     * Varios relatorios de uma vez (POST /logistics/v1/inventories/reports/batch) — 201.
     *
     * @param list<array<string, mixed>> $reports itens no formato de requestInventoryReport() (org, channel, sub_type, file_extension, parameters)
     * @return array<string, mixed>
     */
    public function requestInventoryReportBatch(array $reports): array
    {
        $items = array_map(fn (array $r): array => $this->reportBody(
            (string) $r['org'],
            (string) $r['channel'],
            (string) ($r['sub_type'] ?? 'inventory_kardex'),
            (string) ($r['file_extension'] ?? 'csv'),
            (array) ($r['parameters'] ?? []),
        ), array_values($reports));

        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl('/logistics/v1/inventories/reports/batch'), [], ['reports' => $items]);
    }

    /**
     * Status/URL do relatorio (GET /logistics/v1/inventories/reports/{id}).
     *
     * @return array<string, mixed>
     */
    public function getInventoryReport(string $reportId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl("/logistics/v1/inventories/reports/{$reportId}"));
    }

    /**
     * @param array<string, mixed> $parameters
     * @return array<string, mixed>
     */
    private function reportBody(string $org, string $channel, string $subType, string $fileExtension, array $parameters): array
    {
        $body = [
            'type' => 'stock_logistics',
            'sub_type' => $subType,
            'org' => $org,
            'channel' => $channel,
            'file_extension' => $fileExtension,
        ];
        if ($parameters) $body['parameters'] = $parameters;

        return $body;
    }
}
