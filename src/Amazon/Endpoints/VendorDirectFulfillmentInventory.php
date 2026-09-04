<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Direct Fulfillment Inventory API v1 — o fornecedor informa à Amazon o
 * estoque disponível por armazém pra venda em Direct Fulfillment.
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P, programa Direct
 * Fulfillment); NÃO se aplica a conta de seller (3P).
 *
 * Path base: /vendor/directFulfillment/inventory/v1. Rate limit do modelo:
 * 10 req/s, burst 10.
 */
class VendorDirectFulfillmentInventory
{
    private const BASE = '/vendor/directFulfillment/inventory/v1';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Atualização de estoque (full ou parcial) de um armazém.
     * POST /warehouses/{warehouseId}/items. Resposta: `payload.transactionId`.
     *
     * @param  array<string, mixed>  $body  SubmitInventoryUpdateRequest {inventory: {sellingParty, isFullUpdate, items}}
     * @return array<string, mixed>
     */
    public function submitInventoryUpdate(string $warehouseId, array $body): array
    {
        return $this->client->post(self::BASE.'/warehouses/'.rawurlencode($warehouseId).'/items', $body);
    }
}
