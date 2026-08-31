<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Intervalo do Get Shop Performance. `startDate` inclusivo, `endDate` EXCLUSIVO,
 * ambos ISO YYYY-MM-DD no fuso registrado da loja.
 */
final class ShopPerformanceInterval implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
        public readonly ?ShopPerformanceSales $sales = null,
        public readonly ?ShopPerformanceTraffic $traffic = null,
    ) {}
}
