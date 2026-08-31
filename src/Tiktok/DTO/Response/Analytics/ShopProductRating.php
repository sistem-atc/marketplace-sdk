<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Balde de avaliacao do produto. `stars` e' ENUM STRING ("1_STAR".."5_STAR"), nao
 * numero. `percentage` e' fracao STRING.
 */
final class ShopProductRating implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $stars = null,
        public readonly ?int $count = null,
        public readonly ?string $percentage = null,
    ) {}
}
