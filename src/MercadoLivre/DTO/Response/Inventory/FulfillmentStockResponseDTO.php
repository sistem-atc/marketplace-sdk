<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Inventory;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * RAIZ de GET /inventories/{inventory_id}/stock/fulfillment — estoque do item
 * no Full.
 *
 * `total` = available + not_available. O detalhe do indisponível vem em
 * `notAvailableDetail[]` (transfer/damage/lost/…).
 *
 * @property list<InventoryStockDetail> $notAvailableDetail
 * @property list<InventoryExternalReference> $externalReferences
 */
final class FulfillmentStockResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<InventoryStockDetail>  $notAvailableDetail
     * @param  list<InventoryExternalReference>  $externalReferences
     */
    public function __construct(
        public readonly ?string $inventoryId = null,
        public readonly ?int $total = null,
        public readonly ?int $availableQuantity = null,
        public readonly ?int $notAvailableQuantity = null,
        #[ArrayOf(InventoryStockDetail::class)]
        public readonly array $notAvailableDetail = [],
        #[ArrayOf(InventoryExternalReference::class)]
        public readonly array $externalReferences = [],
    ) {}
}
