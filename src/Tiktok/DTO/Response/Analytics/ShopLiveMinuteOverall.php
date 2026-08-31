<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `performance.overall` do Shop LIVE Minute Performance — o consolidado da
 * transmissao inteira, contra o qual os `intervals` (minuto a minuto) somam.
 * Datas em epoch de SEGUNDOS; `duration` em segundos.
 */
final class ShopLiveMinuteOverall implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $liveTitle = null,
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
        public readonly ?int $duration = null,
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?int $itemsSold = null,
        public readonly ?int $uniqueViewers = null,
        public readonly ?int $impressions = null,
    ) {}
}
