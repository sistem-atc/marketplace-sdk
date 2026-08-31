<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.live_stream_sessions[]` do Get Shop LIVE Performance List.
 *
 * Quirk: `startTime`/`endTime` aqui vem como STRING ("1623812664") e nao como int,
 * ao contrario de praticamente todo o resto da API. Mantemos string pra nao
 * inventar conversao no roundtrip.
 */
final class ShopLiveSessionPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?string $username = null,
        public readonly ?string $startTime = null,
        public readonly ?string $endTime = null,
        public readonly ?ShopLiveSessionSalesPerformance $salesPerformance = null,
        public readonly ?ShopLiveSessionInteractionPerformance $interactionPerformance = null,
    ) {}
}
