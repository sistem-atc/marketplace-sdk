<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Pricing;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Ofertas de um ASIN — `payload` de `GET /products/pricing/v0/items/{Asin}/offers`.
 *
 * PascalCase (SP-API). `Summary.BuyBoxPrices[0].ListingPrice.Amount` é o preço
 * exibido (Buy Box). DINHEIRO é NUMBER aqui (≠ Order, onde é string) — ver Money.
 *
 * @property list<Offer>|null $offers
 */
final class ItemOffersResponseDTO implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[JsonKey('ASIN')]
        public readonly ?string $asin = null,
        // status/marketplaceId vem lowercase/camel no topo (o resto e Pascal).
        #[JsonKey('status')]
        public readonly ?string $status = null,
        #[JsonKey('marketplaceId')]
        public readonly ?string $marketplaceId = null,
        public readonly ?string $itemCondition = null,
        public readonly ?Identifier $identifier = null,
        public readonly ?Summary $summary = null,
        #[ArrayOf(Offer::class)]
        public readonly ?array $offers = null,
    ) {}
}
