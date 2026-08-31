<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Serie temporal de GMV/pedidos da live (`data.gmv_trend_performances[]`).
 *
 * Shape DIFERENTE do `LiveRoomValueTrendPerformance` (view/interactive trends):
 * aqui o data point carrega objeto monetario, la' carrega um escalar `value`.
 */
final class LiveRoomGmvTrendPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // TREND_GMV | TREND_CREATED_ORDER
        public readonly ?string $statsType = null,
        #[ArrayOf(LiveRoomGmvDataPoint::class)]
        public readonly ?array $dataPoints = null,
    ) {}
}
