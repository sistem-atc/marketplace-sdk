<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Venda do produto no intervalo. `orders` sao pedidos; `itemsSold`, unidades.
 */
final class ShopProductDetailSales implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?int $orders = null,
        public readonly ?int $itemsSold = null,
        #[ArrayOf(ShopProductSalesBreakdown::class)]
        public readonly ?array $breakdowns = null,
    ) {}
}
