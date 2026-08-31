<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fatia de trafego do produto por `contentType`.
 */
final class ShopProductTrafficBreakdown implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ShopProductBreakdownTraffic $traffic = null,
        public readonly ?string $contentType = null,
    ) {}
}
