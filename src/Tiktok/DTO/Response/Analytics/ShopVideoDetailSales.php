<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bloco `sales` do intervalo do detalhe de video.
 */
final class ShopVideoDetailSales implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ShopVideoSalesMetrics $overall = null,
        #[ArrayOf(ShopVideoProductSalesBreakdown::class)]
        public readonly ?array $breakdowns = null,
    ) {}
}
