<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Codigo identificador regulado do SKU (GTIN/EAN/UPC/ISBN/JAN).
 * E' o campo que carrega o EAN — util pro fiscal.
 */
final class GlobalIdentifierCode implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $type = null,
    ) {}
}
