<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Peso da embalagem do produto global. `value` e' STRING ("1.32") pelo mesmo
 * motivo das dimensoes. `unit` e' KILOGRAM ou POUND.
 */
final class GlobalPackageWeight implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $value = null,
        public readonly ?string $unit = null,
    ) {}
}
