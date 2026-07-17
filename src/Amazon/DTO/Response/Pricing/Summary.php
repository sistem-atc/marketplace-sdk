<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Pricing;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Resumo de preços (`Summary`). `BuyBoxPrices[0].ListingPrice.Amount` é o preço
 * EXIBIDO (o que o consumidor lê); `LowestPrices` é fallback.
 *
 * @property list<PricePoint>|null $buyBoxPrices
 * @property list<PricePoint>|null $lowestPrices
 * @property list<OfferCount>|null $numberOfOffers
 * @property list<OfferCount>|null $buyBoxEligibleOffers
 * @property list<SalesRanking>|null $salesRankings
 */
final class Summary implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $totalOfferCount = null,
        public readonly ?Money $competitivePriceThreshold = null,
        #[ArrayOf(PricePoint::class)]
        public readonly ?array $buyBoxPrices = null,
        #[ArrayOf(PricePoint::class)]
        public readonly ?array $lowestPrices = null,
        #[ArrayOf(OfferCount::class)]
        public readonly ?array $numberOfOffers = null,
        #[ArrayOf(OfferCount::class)]
        public readonly ?array $buyBoxEligibleOffers = null,
        #[ArrayOf(SalesRanking::class)]
        public readonly ?array $salesRankings = null,
    ) {}
}
