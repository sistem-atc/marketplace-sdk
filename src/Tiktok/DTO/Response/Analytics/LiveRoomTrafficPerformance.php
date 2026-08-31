<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.traffic_performances[]` — uma fonte de trafego e seu detalhamento.
 */
final class LiveRoomTrafficPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?LiveRoomTrafficSource $source = null,
        #[ArrayOf(LiveRoomTrafficSource::class)]
        public readonly ?array $subSources = null,
    ) {}
}
