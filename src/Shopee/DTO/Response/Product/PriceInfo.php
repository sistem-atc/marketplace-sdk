<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preço (`price_info[]`).
 *
 * Item COM variação não traz price_info — o preço mora por model
 * (getModelList). Ver o gotcha em ItemResponseDTO::$hasModel.
 */
final class PriceInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $currency = null,
        public readonly ?float $originalPrice = null,
        public readonly ?float $currentPrice = null,
        public readonly ?float $inflatedPriceOfCurrentPrice = null,
        public readonly ?float $inflatedPriceOfOriginalPrice = null,
        public readonly ?float $sipItemPrice = null,
        public readonly ?string $sipItemPriceSource = null,
    ) {}
}
