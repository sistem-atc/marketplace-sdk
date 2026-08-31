<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Uma HORA do dia consultado. `index` e' 0..23 no fuso da loja (0 = 00:00-00:59).
 * Mesmas metricas do `overall`, mais o indice — por isso e' DTO proprio e nao
 * reuso de `ShopHourlyStats`.
 */
final class ShopHourlyInterval implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $index = null,
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?int $itemsSold = null,
        public readonly ?int $visitors = null,
        public readonly ?int $customers = null,
    ) {}
}
