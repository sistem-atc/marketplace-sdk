<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `nbfr_top_product_items[]`. `topReturnReasons` e' []string na doc mas
 * string no exemplo oficial -> `mixed`.
 */
final class SpsNbfrTopProductItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        public readonly ?string $productName = null,
        public readonly ?int $returnOrderCount = null,
        public readonly ?int $topReasonOrderCount = null,
        public mixed $topReturnReasons = null,
        public readonly ?string $imageUrl = null,
    ) {}
}
