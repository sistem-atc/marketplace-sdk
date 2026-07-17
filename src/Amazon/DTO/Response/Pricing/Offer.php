<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Pricing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Uma oferta (`Offers[]`). `IsBuyBoxWinner` marca o dono do Buy Box (o preço
 * exibido). `ListingPrice.Amount` é NUMBER (ver Money).
 */
final class Offer implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $sellerId = null,
        public readonly ?string $subCondition = null,
        public readonly ?bool $isBuyBoxWinner = null,
        public readonly ?bool $isFeaturedMerchant = null,
        public readonly ?bool $isFulfilledByAmazon = null,
        public readonly ?Money $listingPrice = null,
        public readonly ?Money $shipping = null,
        public readonly ?PrimeInformation $primeInformation = null,
        public readonly ?SellerFeedbackRating $sellerFeedbackRating = null,
        public readonly ?ShippingTime $shippingTime = null,
        public readonly ?ShipsFrom $shipsFrom = null,
    ) {}
}
