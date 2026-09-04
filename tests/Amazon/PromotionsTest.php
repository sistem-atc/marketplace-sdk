<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Promotions;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function promotionsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );
}

function promotions(): Promotions
{
    return new Promotions(new Client(promotionsIntegration()));
}

it('searchPromotions GETs /promotions/2025-12-01/promotions with CSV filters', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['promotions' => [['promotionId' => 'P1']]], 200)]);

    $resp = promotions()->searchPromotions(['A2Q3Y263D00KWC'], ['statuses' => ['ACTIVE', 'SCHEDULED'], 'limit' => 50, 'paginationToken' => 'tok']);

    expect($resp['promotions'][0]['promotionId'])->toBe('P1');
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/promotions/2025-12-01/promotions?marketplaceIds=A2Q3Y263D00KWC&statuses=ACTIVE%2CSCHEDULED&limit=50&paginationToken=tok'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

it('getPromotion GETs /promotions/2025-12-01/promotions/{promotionId}', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['promotionId' => 'P1'], 200)]);

    $resp = promotions()->getPromotion('P1', ['includedData' => ['selections'], 'locale' => 'pt_BR']);

    expect($resp['promotionId'])->toBe('P1');
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/promotions/2025-12-01/promotions/P1?includedData=selections&locale=pt_BR'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

it('getSelection GETs /promotions/2025-12-01/promotions/{promotionId}/selections/{selectionId} with revisionId', function () {
    Http::fake(['https://sellingpartnerapi-na.amazon.com/*' => Http::response(['selectionId' => 'S1', 'items' => []], 200)]);

    $resp = promotions()->getSelection('P1', 'S1', 3, ['limit' => 10]);

    expect($resp['selectionId'])->toBe('S1');
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/promotions/2025-12-01/promotions/P1/selections/S1?revisionId=3&limit=10'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});
