<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Listings;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function listingsExtrasIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );
}

function listingsExtras(): Listings
{
    return new Listings(new Client(listingsExtrasIntegration()));
}

it('patchListingsItem PATCHes /listings/2021-08-01/items/{sellerId}/{sku} with CSV query and JSON body', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['sku' => 'SKU A', 'status' => 'ACCEPTED'], 200)]);

    $body = ['productType' => 'PRODUCT', 'patches' => [['op' => 'replace', 'path' => '/attributes/list_price', 'value' => [['Amount' => 10]]]]];
    $resp = listingsExtras()->patchListingsItem('SELLER1', 'SKU A', ['A2Q3Y263D00KWC', 'ATVPDKIKX0DER'], $body, ['mode' => 'VALIDATION_PREVIEW', 'includedData' => ['issues']]);

    expect($resp['status'])->toBe('ACCEPTED');
    Http::assertSent(fn (Request $r) => $r->method() === 'PATCH'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/listings/2021-08-01/items/SELLER1/SKU%20A?marketplaceIds=A2Q3Y263D00KWC%2CATVPDKIKX0DER&mode=VALIDATION_PREVIEW&includedData=issues'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && $r->data() === $body);
});

it('searchListingsItems GETs /listings/2021-08-01/items/{sellerId} with CSV filters', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['numberOfResults' => 1, 'items' => [['sku' => 'X']]], 200)]);

    $resp = listingsExtras()->searchListingsItems('SELLER1', 'A2Q3Y263D00KWC', ['withStatus' => ['BUYABLE', 'DISCOVERABLE'], 'pageSize' => 10, 'pageToken' => 'tok']);

    expect($resp['items'])->toHaveCount(1);
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/listings/2021-08-01/items/SELLER1?marketplaceIds=A2Q3Y263D00KWC&withStatus=BUYABLE%2CDISCOVERABLE&pageSize=10&pageToken=tok'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

it('putListingsItemLegacy PUTs /listings/2020-09-01/items/{sellerId}/{sku}', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['status' => 'ACCEPTED'], 200)]);

    $body = ['productType' => 'PRODUCT', 'requirements' => 'LISTING', 'attributes' => []];
    listingsExtras()->putListingsItemLegacy('SELLER1', 'SKU1', ['A2Q3Y263D00KWC'], $body, ['issueLocale' => 'pt_BR']);

    Http::assertSent(fn (Request $r) => $r->method() === 'PUT'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/listings/2020-09-01/items/SELLER1/SKU1?marketplaceIds=A2Q3Y263D00KWC&issueLocale=pt_BR'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && $r->data() === $body);
});

it('patchListingsItemLegacy PATCHes /listings/2020-09-01/items/{sellerId}/{sku}', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['status' => 'ACCEPTED'], 200)]);

    $body = ['productType' => 'PRODUCT', 'patches' => []];
    listingsExtras()->patchListingsItemLegacy('SELLER1', 'SKU1', 'A2Q3Y263D00KWC', $body);

    Http::assertSent(fn (Request $r) => $r->method() === 'PATCH'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/listings/2020-09-01/items/SELLER1/SKU1?marketplaceIds=A2Q3Y263D00KWC'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && $r->data() === $body);
});

it('deleteListingsItemLegacy DELETEs /listings/2020-09-01/items/{sellerId}/{sku}', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['status' => 'ACCEPTED'], 200)]);

    listingsExtras()->deleteListingsItemLegacy('SELLER1', 'SKU1', 'A2Q3Y263D00KWC');

    Http::assertSent(fn (Request $r) => $r->method() === 'DELETE'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/listings/2020-09-01/items/SELLER1/SKU1?marketplaceIds=A2Q3Y263D00KWC'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});
