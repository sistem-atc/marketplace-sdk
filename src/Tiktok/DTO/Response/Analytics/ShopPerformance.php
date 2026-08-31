<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.performance` do /analytics/{v}/shop/performance — o consolidado da loja.
 * Com granularity=ALL vem 1 intervalo; com 1D, um por dia.
 */
final class ShopPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ShopPerformanceInterval::class)]
        public readonly ?array $intervals = null,
    ) {}
}
