<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\ProductFees;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function productFeesIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );
}

function productFees(): ProductFees
{
    return new ProductFees(new Client(productFeesIntegration()));
}

function productFeesRequest(): array
{
    return [
        'MarketplaceId' => 'A2Q3Y263D00KWC',
        'IsAmazonFulfilled' => true,
        'PriceToEstimateFees' => ['ListingPrice' => ['CurrencyCode' => 'BRL', 'Amount' => 99.9]],
        'Identifier' => 'req-1',
    ];
}

it('getMyFeesEstimateForSKU POSTs /products/fees/v0/listings/{SellerSKU}/feesEstimate wrapping FeesEstimateRequest', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['payload' => ['FeesEstimateResult' => ['Status' => 'Success']]], 200)]);

    $resp = productFees()->getMyFeesEstimateForSKU('SKU A', productFeesRequest());

    expect($resp['payload']['FeesEstimateResult']['Status'])->toBe('Success');
    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/products/fees/v0/listings/SKU%20A/feesEstimate'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && $r->data() === ['FeesEstimateRequest' => productFeesRequest()]);
});

it('getMyFeesEstimateForASIN POSTs /products/fees/v0/items/{Asin}/feesEstimate', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['payload' => ['FeesEstimateResult' => []]], 200)]);

    productFees()->getMyFeesEstimateForASIN('B0TEST1234', productFeesRequest());

    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/products/fees/v0/items/B0TEST1234/feesEstimate'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && $r->data() === ['FeesEstimateRequest' => productFeesRequest()]);
});

it('getMyFeesEstimates POSTs a raw list to /products/fees/v0/feesEstimate', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response([['Status' => 'Success'], ['Status' => 'Success']], 200)]);

    $items = [
        ['FeesEstimateRequest' => productFeesRequest(), 'IdType' => 'ASIN', 'IdValue' => 'B01'],
        ['FeesEstimateRequest' => productFeesRequest(), 'IdType' => 'SellerSKU', 'IdValue' => 'SKU-1'],
    ];
    $resp = productFees()->getMyFeesEstimates($items);

    expect($resp)->toHaveCount(2);
    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/products/fees/v0/feesEstimate'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && $r->data() === $items);
});
