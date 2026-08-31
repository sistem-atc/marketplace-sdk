<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Armazem de um mercado que compartilha estoque com o armazem local
 * da regra (modos SHARED/DYNAMIC).
 */
final class GlobalAssociatedWarehouse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $warehouseId = null,
        public readonly ?string $region = null,
    ) {}
}
