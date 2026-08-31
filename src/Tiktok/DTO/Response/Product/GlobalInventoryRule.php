<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Regra de alocacao de estoque de um armazem da loja.
 *
 * `allocationMode`:
 *   - SHARED:  estoque unico entre os mercados — baixa em um baixa em todos
 *   - DYNAMIC: dividido proporcionalmente; baixa afeta so' o mercado
 *   - MANUAL:  seller configura por mercado; `associatedWarehouses` nao vem
 *
 * @property list<GlobalAssociatedWarehouse>|null $associatedWarehouses
 */
final class GlobalInventoryRule implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $localWarehouseId = null,
        public readonly ?string $allocationMode = null,
        #[ArrayOf(GlobalAssociatedWarehouse::class)]
        public readonly ?array $associatedWarehouses = null,
    ) {}
}
