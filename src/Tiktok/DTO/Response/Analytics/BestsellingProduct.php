<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.products[]` do /analytics/{v}/products/bestselling.
 *
 * Ranking PUBLICO (cross-shop): `shopId`/`shopName` podem ser de OUTRO vendedor,
 * nao necessariamente da loja autorizada. `rating` e' string ("4.5").
 */
final class BestsellingProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $rank = null,
        public readonly ?string $gmvRange = null,
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $rating = null,
        public readonly ?BestsellingProductImage $productImage = null,
        public readonly ?string $shopName = null,
        public readonly ?string $shopId = null,
    ) {}
}
