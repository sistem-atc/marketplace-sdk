<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Intervalo do detalhe de produto (fim EXCLUSIVO, ISO YYYY-MM-DD).
 */
final class ShopProductDetailInterval implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
        public readonly ?ShopProductDetailSales $sales = null,
        public readonly ?ShopProductDetailTraffic $traffic = null,
        public readonly ?ShopProductCancelAndRefunds $cancelAndRefunds = null,
    ) {}
}
