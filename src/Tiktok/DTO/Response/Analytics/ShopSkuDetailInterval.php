<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Intervalo do detalhe de SKU (fim EXCLUSIVO, ISO YYYY-MM-DD).
 */
final class ShopSkuDetailInterval implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
        public readonly ?MonetaryValue $gmv = null,
        #[ArrayOf(ShopSkuGmvBreakdown::class)]
        public readonly ?array $gmvBreakdown = null,
        public readonly ?int $itemsSold = null,
        #[ArrayOf(ShopSkuItemsSoldBreakdown::class)]
        public readonly ?array $itemsSoldBreakdown = null,
        public readonly ?int $skuOrders = null,
    ) {}
}
