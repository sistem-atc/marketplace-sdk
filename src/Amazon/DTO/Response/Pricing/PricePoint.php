<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Pricing;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Ponto de preço (`Summary.BuyBoxPrices[]` e `Summary.LowestPrices[]`).
 *
 * `ListingPrice` = preço do anúncio; `LandedPrice` = com frete. `condition` e
 * `fulfillmentChannel` vêm camelCase na SP-API (só nas LowestPrices) — #[JsonKey].
 */
final class PricePoint implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[JsonKey('condition')]
        public readonly ?string $condition = null,
        #[JsonKey('fulfillmentChannel')]
        public readonly ?string $fulfillmentChannel = null,
        public readonly ?Money $listingPrice = null,
        public readonly ?Money $landedPrice = null,
        public readonly ?Money $shipping = null,
    ) {}
}
