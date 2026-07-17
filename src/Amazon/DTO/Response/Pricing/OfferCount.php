<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Pricing;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Contagem de ofertas por canal (`Summary.NumberOfOffers[]` e
 * `Summary.BuyBoxEligibleOffers[]`). `condition`/`fulfillmentChannel` camelCase.
 */
final class OfferCount implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $offerCount = null,
        #[JsonKey('condition')]
        public readonly ?string $condition = null,
        #[JsonKey('fulfillmentChannel')]
        public readonly ?string $fulfillmentChannel = null,
    ) {}
}
