<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Inventory;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

class InventoryMethods extends BaseMethods
{
    /**
     * Estoque de um item no Fulfillment (Full).
     *
     * GET /inventories/{inventory_id}/stock/fulfillment
     *
     * O inventory_id e' o codigo que identifica o item no Full — obtido em
     * /items (campo `inventory_id`, ou `variations[].inventory_id` quando ha
     * variacoes). Retorna:
     *   - inventory_id
     *   - total                 (available + not_available)
     *   - available_quantity    (disponivel pra venda)
     *   - not_available_quantity
     *   - not_available_detail[] {status, quantity} — transfer, damage, lost,
     *                            noFiscalCoverage, withdrawal, internal_process
     */
    public function fulfillmentStock(string $inventoryId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/inventories/{$inventoryId}/stock/fulfillment");
    }
}
