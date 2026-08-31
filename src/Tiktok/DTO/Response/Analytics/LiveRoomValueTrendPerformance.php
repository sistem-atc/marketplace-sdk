<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Serie temporal escalar de UMA live. O MESMO shape serve dois endpoints — por
 * isso e' um DTO so', com o `statsType` dizendo o que a serie mede:
 *
 * - interactive_trend_performances: WATCH_PV | COMMENT_PV | SHARE_PV
 * - view_trend_performances:        TREND_ONLINE_VIEWER | TREND_ENTER_VIEWER | TREND_LEFT_VIEWER
 */
final class LiveRoomValueTrendPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $statsType = null,
        #[ArrayOf(LiveRoomValueDataPoint::class)]
        public readonly ?array $dataPoints = null,
    ) {}
}
