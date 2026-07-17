<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Estoque por localização (`seller_stock[]` / `shopee_stock[]`). */
final class StockDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $locationId = null,
        public readonly ?int $stock = null,
        public readonly ?bool $ifSaleable = null,
    ) {}
}
