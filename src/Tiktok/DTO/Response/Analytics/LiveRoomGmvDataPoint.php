<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ponto da serie de `gmv_trend_performances[].data_points[]`.
 *
 * Qual campo vem preenchido depende do `statsType` do performance PAI:
 * TREND_GMV -> `gmv`; TREND_CREATED_ORDER -> `orderCount`. Nao assuma os dois.
 */
final class LiveRoomGmvDataPoint implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $orderCount = null,
        public readonly ?int $timestamp = null,
        public readonly ?MonetaryValue $gmv = null,
    ) {}
}
