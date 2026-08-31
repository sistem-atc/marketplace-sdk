<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fatia de venda do produto por `contentType` (VIDEO | LIVE | PRODUCT_CARD).
 */
final class ShopProductSalesBreakdown implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $contentType = null,
        public readonly ?ShopProductBreakdownSales $sales = null,
    ) {}
}
