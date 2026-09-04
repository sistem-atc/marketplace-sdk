<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\ListingsRestrictions;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function listingsRestrictionsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );
}

it('getListingsRestrictions GETs /listings/2021-08-01/restrictions with asin, sellerId and CSV marketplaceIds', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['restrictions' => [['marketplaceId' => 'A2Q3Y263D00KWC', 'reasons' => []]]], 200)]);

    $resp = (new ListingsRestrictions(new Client(listingsRestrictionsIntegration())))
        ->getListingsRestrictions('B0TEST1234', 'SELLER1', ['A2Q3Y263D00KWC'], ['conditionType' => 'new_new', 'reasonLocale' => 'pt_BR']);

    expect($resp['restrictions'])->toHaveCount(1);
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/listings/2021-08-01/restrictions?asin=B0TEST1234&sellerId=SELLER1&marketplaceIds=A2Q3Y263D00KWC&conditionType=new_new&reasonLocale=pt_BR'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});
