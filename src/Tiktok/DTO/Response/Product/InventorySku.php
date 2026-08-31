<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** SKU dentro de `inventory[].skus[]` do Inventory Search. */
final class InventorySku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $sellerSku = null,
        public readonly ?int $totalAvailableQuantity = null,
        public readonly ?int $totalCommittedQuantity = null,
        #[ArrayOf(WarehouseInventory::class)]
        public readonly ?array $warehouseInventory = null,
        public readonly ?InventoryDistribution $totalAvailableInventoryDistribution = null,
    ) {}
}
