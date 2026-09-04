<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Pricing;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function pricingExtrasIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );
}

function pricingExtras(): Pricing
{
    return new Pricing(new Client(pricingExtrasIntegration()));
}

it('getPricing GETs /products/pricing/v0/price with Asins CSV', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['payload' => [['ASIN' => 'B01', 'status' => 'Success']]], 200)]);

    $resp = pricingExtras()->getPricing('A2Q3Y263D00KWC', 'Asin', ['B01', 'B02'], ['ItemCondition' => 'New']);

    expect($resp['payload'][0]['ASIN'])->toBe('B01');
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/products/pricing/v0/price?MarketplaceId=A2Q3Y263D00KWC&ItemType=Asin&Asins=B01%2CB02&ItemCondition=New'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

it('getPricing uses Skus when ItemType is Sku', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['payload' => []], 200)]);

    pricingExtras()->getPricing('A2Q3Y263D00KWC', 'Sku', ['SKU-1']);

    Http::assertSent(fn (Request $r) => $r->url() === 'https://sellingpartnerapi-na.amazon.com/products/pricing/v0/price?MarketplaceId=A2Q3Y263D00KWC&ItemType=Sku&Skus=SKU-1');
});

it('getCompetitivePricing GETs /products/pricing/v0/competitivePrice', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['payload' => []], 200)]);

    pricingExtras()->getCompetitivePricing('A2Q3Y263D00KWC', 'Asin', ['B01'], ['CustomerType' => 'Consumer']);

    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/products/pricing/v0/competitivePrice?MarketplaceId=A2Q3Y263D00KWC&ItemType=Asin&Asins=B01&CustomerType=Consumer'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

it('getListingOffers GETs /products/pricing/v0/listings/{SellerSKU}/offers', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['payload' => ['Summary' => []]], 200)]);

    pricingExtras()->getListingOffers('SKU A', 'A2Q3Y263D00KWC', 'New', ['CustomerType' => 'Business']);

    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/products/pricing/v0/listings/SKU%20A/offers?MarketplaceId=A2Q3Y263D00KWC&ItemCondition=New&CustomerType=Business'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

it('getItemOffersBatch POSTs /batches/products/pricing/v0/itemOffers with requests[]', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['responses' => [['status' => ['statusCode' => 200]]]], 200)]);

    $requests = [['uri' => '/products/pricing/v0/items/B01/offers', 'method' => 'GET', 'MarketplaceId' => 'A2Q3Y263D00KWC', 'ItemCondition' => 'New']];
    $resp = pricingExtras()->getItemOffersBatch($requests);

    expect($resp['responses'])->toHaveCount(1);
    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/batches/products/pricing/v0/itemOffers'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && $r->data() === ['requests' => $requests]);
});

it('getListingOffersBatch POSTs /batches/products/pricing/v0/listingOffers with requests[]', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['responses' => []], 200)]);

    $requests = [['uri' => '/products/pricing/v0/listings/SKU-1/offers', 'method' => 'GET', 'MarketplaceId' => 'A2Q3Y263D00KWC', 'ItemCondition' => 'New']];
    pricingExtras()->getListingOffersBatch($requests);

    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/batches/products/pricing/v0/listingOffers'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && $r->data() === ['requests' => $requests]);
});

it('getFeaturedOfferExpectedPriceBatch POSTs /batches/products/pricing/2022-05-01/offer/featuredOfferExpectedPrice', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['responses' => []], 200)]);

    $requests = [['uri' => '/products/pricing/2022-05-01/offer/featuredOfferExpectedPrice', 'method' => 'GET', 'marketplaceId' => 'A2Q3Y263D00KWC', 'sku' => 'SKU-1']];
    pricingExtras()->getFeaturedOfferExpectedPriceBatch($requests);

    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/batches/products/pricing/2022-05-01/offer/featuredOfferExpectedPrice'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && $r->data() === ['requests' => $requests]);
});
