<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `sales.overall` do detalhe de video: metricas do video INTEIRO no intervalo.
 * `gpm` e' GMV por mil impressoes (objeto monetario). `ctr` e' fracao STRING.
 */
final class ShopVideoSalesMetrics implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?MonetaryValue $gpm = null,
        public readonly ?int $customers = null,
        public readonly ?int $itemsSold = null,
        public readonly ?string $ctr = null,
        public readonly ?int $productImpressions = null,
        public readonly ?int $productClicks = null,
    ) {}
}
