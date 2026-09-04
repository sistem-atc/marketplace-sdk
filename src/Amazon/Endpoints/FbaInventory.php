<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * FBA Inventory API v1 da SP-API (`/fba/inventory/v1`).
 *
 * Somente `getInventorySummaries` funciona em producao; `createInventoryItem`,
 * `addInventory` e `deleteInventoryItem` sao sandbox-only (integration com `settings.endpoint`
 * apontando pro sandbox).
 */
class FbaInventory
{
    private const BASE = '/fba/inventory/v1';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Resumo de estoque FBA (GET /fba/inventory/v1/summaries). Paginacao por
     * `nextToken` (em `pagination.nextToken`); dado em `payload.inventorySummaries`.
     * Rate limit: 2 req/s + burst 2.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $query  details, startDateTime, sellerSkus, sellerSku, nextToken
     */
    public function getInventorySummaries(string $granularityType, string $granularityId, array $marketplaceIds, array $query = []): array
    {
        return $this->client->get(self::BASE.'/summaries', [
            'granularityType' => $granularityType,
            'granularityId' => $granularityId,
            'marketplaceIds' => implode(',', $marketplaceIds),
        ] + $query);
    }

    /**
     * Cria item no estoque do SANDBOX (POST /fba/inventory/v1/items). Sandbox-only.
     *
     * @param  array<string, mixed>  $body  sellerSku, marketplaceId, productName
     */
    public function createInventoryItem(array $body): array
    {
        return $this->client->post(self::BASE.'/items', $body);
    }

    /**
     * Remove item do estoque do SANDBOX (DELETE /fba/inventory/v1/items/{sellerSku}). Sandbox-only.
     */
    public function deleteInventoryItem(string $sellerSku, string $marketplaceId): array
    {
        return $this->client->delete(
            self::BASE.'/items/'.rawurlencode($sellerSku).'?'.http_build_query(['marketplaceId' => $marketplaceId]),
        );
    }

    /**
     * Adiciona quantidade a itens do estoque do SANDBOX
     * (POST /fba/inventory/v1/items/inventory). Sandbox-only. Exige o header
     * `x-amzn-idempotency-token` (token unico por chamada, ex.: UUID).
     *
     * @param  array<string, mixed>  $body  AddInventoryRequest: inventoryItems[] (sellerSku, marketplaceId, quantity)
     */
    public function addInventory(string $idempotencyToken, array $body): array
    {
        return $this->client->post(self::BASE.'/items/inventory', $body, [
            'x-amzn-idempotency-token' => $idempotencyToken,
        ]);
    }
}
