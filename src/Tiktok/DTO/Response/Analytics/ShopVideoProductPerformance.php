<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.products[]` do Get Shop Video Product Performance List — produto
 * promovido em UM video.
 *
 * `dailyAvgBuyers` vem como STRING mesmo sendo contagem media.
 */
final class ShopVideoProductPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?int $unitsSold = null,
        public readonly ?string $dailyAvgBuyers = null,
    ) {}
}
