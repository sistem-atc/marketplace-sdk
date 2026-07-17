<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Opção padronizada (`standardise_tier_variation[].variation_option_list[]`). */
final class StandardVariationOption implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $variationOptionId = null,
        public readonly ?string $variationOptionName = null,
        public readonly ?string $imageId = null,
        public readonly ?string $imageUrl = null,
    ) {}
}
