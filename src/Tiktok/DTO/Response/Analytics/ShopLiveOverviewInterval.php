<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Intervalo agregado do Get Shop LIVE Performance Overview (todas as lives da
 * loja somadas). `startDate`/`endDate` sao datas ISO YYYY-MM-DD no fuso da loja,
 * com fim EXCLUSIVO. Taxas em fracao STRING.
 */
final class ShopLiveOverviewInterval implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?int $skuOrders = null,
        public readonly ?int $customers = null,
        public readonly ?int $itemsSold = null,
        public readonly ?string $clickToOrderRate = null,
        public readonly ?string $clickThroughRate = null,
    ) {}
}
