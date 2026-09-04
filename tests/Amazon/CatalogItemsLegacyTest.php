<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\CatalogItemsLegacy;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function catalogItemsLegacyIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );
}

function catalogItemsLegacy(): CatalogItemsLegacy
{
    return new CatalogItemsLegacy(new Client(catalogItemsLegacyIntegration()));
}

it('searchCatalogItems GETs /catalog/2020-12-01/items with CSV keywords', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['numberOfResults' => 0, 'items' => []], 200)]);

    catalogItemsLegacy()->searchCatalogItems(['whey', 'protein'], 'A2Q3Y263D00KWC', ['pageSize' => 5, 'pageToken' => 'tok']);

    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/catalog/2020-12-01/items?keywords=whey%2Cprotein&marketplaceIds=A2Q3Y263D00KWC&pageSize=5&pageToken=tok'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

it('getCatalogItem GETs /catalog/2020-12-01/items/{asin}', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['asin' => 'B0TEST1234'], 200)]);

    $resp = catalogItemsLegacy()->getCatalogItem('B0TEST1234', ['A2Q3Y263D00KWC'], ['includedData' => ['attributes']]);

    expect($resp['asin'])->toBe('B0TEST1234');
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/catalog/2020-12-01/items/B0TEST1234?marketplaceIds=A2Q3Y263D00KWC&includedData=attributes'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

it('listCatalogCategories GETs /catalog/v0/categories with MarketplaceId and ASIN', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['payload' => [['ProductCategoryId' => '123']]], 200)]);

    $resp = catalogItemsLegacy()->listCatalogCategories('A2Q3Y263D00KWC', ['ASIN' => 'B0TEST1234']);

    expect($resp['payload'][0]['ProductCategoryId'])->toBe('123');
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/catalog/v0/categories?MarketplaceId=A2Q3Y263D00KWC&ASIN=B0TEST1234'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});
