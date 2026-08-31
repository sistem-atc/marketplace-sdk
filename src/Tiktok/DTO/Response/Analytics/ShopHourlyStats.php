<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Metricas do recorte horario da loja (usado tanto no `overall` do dia quanto,
 * com o campo `index` a mais, em cada hora — veja `ShopHourlyInterval`).
 */
final class ShopHourlyStats implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?int $itemsSold = null,
        public readonly ?int $visitors = null,
        public readonly ?int $customers = null,
    ) {}
}
