<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.videos[]` do Get Shop Video Performance List.
 *
 * `videoPostTime` NAO e' epoch: e' string "YYYY-MM-DD HH:MM:SS". `duration` e' em
 * segundos. `gpm` e' objeto monetario (GMV por mil views), nao taxa.
 */
final class ShopVideoPerformanceSummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?string $username = null,
        public readonly ?ShopVideoCreator $creator = null,
        public readonly ?string $videoPostTime = null,
        public readonly ?int $duration = null,
        public readonly ?array $hashTags = null,
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?MonetaryValue $gpm = null,
        public readonly ?int $avgCustomers = null,
        public readonly ?int $skuOrders = null,
        public readonly ?int $itemsSold = null,
        public readonly ?int $views = null,
        public readonly ?string $clickThroughRate = null,
        #[ArrayOf(ShopVideoProductRef::class)]
        public readonly ?array $products = null,
    ) {}
}
