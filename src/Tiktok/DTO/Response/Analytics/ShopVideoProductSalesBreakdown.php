<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fatia de `sales.breakdowns[]` do detalhe de video: as MESMAS metricas do
 * `overall`, porem de UM produto ancorado no video (`productId`).
 */
final class ShopVideoProductSalesBreakdown implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?MonetaryValue $gpm = null,
        public readonly ?int $customers = null,
        public readonly ?int $itemsSold = null,
        public readonly ?string $ctr = null,
        public readonly ?int $productImpressions = null,
        public readonly ?int $productClicks = null,
    ) {}
}
