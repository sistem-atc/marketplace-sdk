<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Estoque do SKU por armazem GLOBAL.
 *
 * A chave e' `global_warehouse_id` (nao `warehouse_id`, como no produto
 * local) — sao espacos de id diferentes.
 */
final class GlobalSkuInventory implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $globalWarehouseId = null,
        public readonly ?int $quantity = null,
    ) {}
}
