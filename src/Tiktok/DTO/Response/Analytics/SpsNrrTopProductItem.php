<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `nrr_top_product_items[]`. `topReviewReasons` e' []string na doc mas
 * string no exemplo oficial -> `mixed`.
 */
final class SpsNrrTopProductItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $deliveredOrderCount = null,
        public readonly ?string $imageUrl = null,
        public readonly ?int $negativeOrderCount = null,
        public readonly ?string $productId = null,
        public readonly ?string $productName = null,
        public mixed $topReviewReasons = null,
    ) {}
}
