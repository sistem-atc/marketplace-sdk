<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Um intervalo de minutos da live, com os quatro blocos de metrica separados.
 * `startTime`/`endTime` em epoch de SEGUNDOS.
 */
final class ShopLiveMinuteInterval implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
        public readonly ?ShopLiveIntervalSales $sales = null,
        public readonly ?ShopLiveIntervalTraffic $traffic = null,
        public readonly ?ShopLiveIntervalInteractions $interactions = null,
        public readonly ?ShopLiveIntervalConversion $conversion = null,
    ) {}
}
