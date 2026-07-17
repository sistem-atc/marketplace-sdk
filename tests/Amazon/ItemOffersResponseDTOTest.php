<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Amazon\DTO\Response\Pricing\ItemOffersResponseDTO;

/** Payload SINTETICO na shape da Product Pricing API (PascalCase, Amount NUMBER). */
function fakeItemOffers(): array
{
    return [
        'ASIN' => 'B0BL5CSRBM',
        'status' => 'Success',                      // lowercase no topo
        'marketplaceId' => 'A2Q3Y263D00KWC',        // camelCase no topo
        'ItemCondition' => 'New',
        'Identifier' => ['ASIN' => 'B0BL5CSRBM', 'MarketplaceId' => 'A2Q3Y263D00KWC', 'ItemCondition' => 'New'],
        'Offers' => [[
            'SellerId' => 'A2WV4O7W9ECPST',
            'SubCondition' => 'new',
            'IsBuyBoxWinner' => true,
            'IsFulfilledByAmazon' => true,
            'ListingPrice' => ['Amount' => 35.63, 'CurrencyCode' => 'BRL'],   // NUMBER (double)
            'SellerFeedbackRating' => ['FeedbackCount' => 100, 'SellerPositiveFeedbackRating' => 92],  // int
            'ShippingTime' => ['availabilityType' => 'NOW', 'minimumHours' => 0, 'maximumHours' => 24],
            'ShipsFrom' => ['Country' => 'BR'],
        ]],
        'Summary' => [
            'TotalOfferCount' => 1,
            'BuyBoxPrices' => [['condition' => 'New',
                'ListingPrice' => ['Amount' => 35.63, 'CurrencyCode' => 'BRL'],
                'LandedPrice' => ['Amount' => 35.63, 'CurrencyCode' => 'BRL']]],
            'LowestPrices' => [['condition' => 'New', 'fulfillmentChannel' => 'Amazon',
                'ListingPrice' => ['Amount' => 35.63, 'CurrencyCode' => 'BRL']]],
        ],
    ];
}

it('hidrata as ofertas PascalCase', function () {
    $dto = ItemOffersResponseDTO::fromArray(fakeItemOffers());

    expect($dto->asin)->toBe('B0BL5CSRBM')
        ->and($dto->status)->toBe('Success')            // via JsonKey (lowercase)
        ->and($dto->marketplaceId)->toBe('A2Q3Y263D00KWC') // via JsonKey (camel)
        ->and($dto->offers)->toHaveCount(1)
        ->and($dto->offers[0]->isBuyBoxWinner)->toBeTrue()
        ->and($dto->offers[0]->sellerId)->toBe('A2WV4O7W9ECPST');
});

it('preço exibido = BuyBoxPrices[0].ListingPrice.Amount (NUMBER, nao string)', function () {
    $dto = ItemOffersResponseDTO::fromArray(fakeItemOffers());
    $buyBox = $dto->summary?->buyBoxPrices[0] ?? null;

    // no Pricing o Amount e NUMBER (double), diferente do Order onde e string
    expect($buyBox?->listingPrice?->amount)->toBe(35.63)
        ->and($buyBox?->condition)->toBe('New')
        // e o preferido pelo consumidor pra preco exibido:
        ->and((float) $dto->summary->buyBoxPrices[0]->listingPrice->amount)->toBe(35.63);
});

it('trata os campos camelCase (condition/fulfillmentChannel/availabilityType) via JsonKey', function () {
    $dto = ItemOffersResponseDTO::fromArray(fakeItemOffers());

    expect($dto->summary?->lowestPrices[0]->fulfillmentChannel)->toBe('Amazon')
        ->and($dto->offers[0]->shippingTime?->availabilityType)->toBe('NOW')
        ->and($dto->offers[0]->shippingTime?->minimumHours)->toBe(0);
});

it('faz roundtrip lossless (PascalCase + camelCase misturados + Amount NUMBER)', function () {
    $payload = fakeItemOffers();

    expect(ItemOffersResponseDTO::fromArray($payload)->toArray())->toEqual($payload);
});
