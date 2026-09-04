<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\CatalogItems;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function catalogItemsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );
}

function catalogItems(): CatalogItems
{
    return new CatalogItems(new Client(catalogItemsIntegration()));
}

it('searchCatalogItems GETs /catalog/2022-04-01/items with CSV identifiers and includedData', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['numberOfResults' => 1, 'items' => [['asin' => 'B01']]], 200)]);

    $resp = catalogItems()->searchCatalogItems(['A2Q3Y263D00KWC'], [
        'identifiers' => ['7898000000001', '7898000000002'],
        'identifiersType' => 'EAN',
        'includedData' => ['summaries', 'identifiers'],
        'pageSize' => 20,
    ]);

    expect($resp['items'][0]['asin'])->toBe('B01');
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/catalog/2022-04-01/items?marketplaceIds=A2Q3Y263D00KWC&identifiers=7898000000001%2C7898000000002&identifiersType=EAN&includedData=summaries%2Cidentifiers&pageSize=20'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

it('getCatalogItem GETs /catalog/2022-04-01/items/{asin}', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['asin' => 'B0TEST1234', 'summaries' => []], 200)]);

    $resp = catalogItems()->getCatalogItem('B0TEST1234', 'A2Q3Y263D00KWC', ['includedData' => ['summaries', 'images'], 'locale' => 'pt_BR']);

    expect($resp['asin'])->toBe('B0TEST1234');
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/catalog/2022-04-01/items/B0TEST1234?marketplaceIds=A2Q3Y263D00KWC&includedData=summaries%2Cimages&locale=pt_BR'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});
