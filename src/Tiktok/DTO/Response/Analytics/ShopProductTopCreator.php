<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Creator que mais vendeu o produto no periodo (top 100 por GMV).
 */
final class ShopProductTopCreator implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $creatorOpenId = null,
        public readonly ?string $creatorName = null,
        public readonly ?string $creatorProfile = null,
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?int $itemsSold = null,
    ) {}
}
