<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.performance` do /analytics/{v}/shop/performance/{date}/performance_per_hour.
 *
 * `latestAvailableTimestamp` (epoch em SEGUNDOS) diz ate' onde o dado ja' fechou —
 * horas depois disso vem zeradas, nao ausentes. Nao leia queda de GMV no fim do
 * dia como queda real.
 */
final class ShopHourlyPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ShopHourlyStats $overall = null,
        #[ArrayOf(ShopHourlyInterval::class)]
        public readonly ?array $intervals = null,
        public readonly ?int $latestAvailableTimestamp = null,
    ) {}
}
