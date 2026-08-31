<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.performance` do /analytics/{v}/shop_lives/{live_id}/performance_per_minutes.
 *
 * Os `intervals` sao PAGINADOS (next_page_token / total_count ficam fora deste
 * objeto, na raiz de `data`) — `overall` NAO e' a soma da pagina, e' da live toda.
 */
final class ShopLiveMinutePerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ShopLiveMinuteOverall $overall = null,
        #[ArrayOf(ShopLiveMinuteInterval::class)]
        public readonly ?array $intervals = null,
    ) {}
}
