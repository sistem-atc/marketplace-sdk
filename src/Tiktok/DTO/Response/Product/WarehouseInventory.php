<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Estoque do SKU num armazem especifico (`warehouse_inventory[]`).
 *
 * `availableQuantity` aqui equivale ao `inventory.quantity` do Get Product;
 * `committedQuantity` (reservado por pedido em aberto) NAO aparece la'.
 */
final class WarehouseInventory implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $warehouseId = null,
        public readonly ?int $availableQuantity = null,
        public readonly ?int $committedQuantity = null,
    ) {}
}
