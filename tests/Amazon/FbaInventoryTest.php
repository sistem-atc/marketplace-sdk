<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\FbaInventory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function fbaInventoryIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );
}

function fbaInventory(): FbaInventory
{
    return new FbaInventory(new Client(fbaInventoryIntegration()));
}

it('getInventorySummaries sends granularity + marketplaceIds + optionals', function () {
    Http::fake(['*' => Http::response([
        'pagination' => ['nextToken' => 'tok'],
        'payload' => ['granularity' => ['granularityType' => 'Marketplace'], 'inventorySummaries' => [['sellerSku' => 'SKU-A']]],
    ])]);

    $resp = fbaInventory()->getInventorySummaries('Marketplace', 'A2Q3Y263D00KWC', ['A2Q3Y263D00KWC'], ['details' => 'true', 'sellerSkus' => 'SKU-A']);

    expect($resp['payload']['inventorySummaries'])->toHaveCount(1);
    expect($resp['pagination']['nextToken'])->toBe('tok');
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/fba/inventory/v1/summaries?granularityType=Marketplace&granularityId=A2Q3Y263D00KWC&marketplaceIds=A2Q3Y263D00KWC&details=true&sellerSkus=SKU-A'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

it('createInventoryItem posts the body to /items', function () {
    Http::fake(['*' => Http::response(['errors' => []])]);

    fbaInventory()->createInventoryItem(['sellerSku' => 'SKU-A', 'marketplaceId' => 'A2Q3Y263D00KWC', 'productName' => 'Whey']);

    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/fba/inventory/v1/items'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && $r['sellerSku'] === 'SKU-A'
        && $r['productName'] === 'Whey');
});

it('deleteInventoryItem sends DELETE with encoded sku + marketplaceId', function () {
    Http::fake(['*' => Http::response(['errors' => []])]);

    fbaInventory()->deleteInventoryItem('SKU A/1', 'A2Q3Y263D00KWC');

    Http::assertSent(fn (Request $r) => $r->method() === 'DELETE'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/fba/inventory/v1/items/SKU%20A%2F1?marketplaceId=A2Q3Y263D00KWC'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

it('addInventory → POST /fba/inventory/v1/items/inventory com x-amzn-idempotency-token', function () {
    Http::fake(['*' => Http::response(['ok' => true])]);

    $body = ['inventoryItems' => [['sellerSku' => 'SKU-A', 'marketplaceId' => 'A2Q3Y263D00KWC', 'quantity' => 5]]];
    $resp = fbaInventory()->addInventory('idem-uuid-1', $body);

    expect($resp)->toBe(['ok' => true]);
    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/fba/inventory/v1/items/inventory'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && $r->hasHeader('x-amzn-idempotency-token', 'idem-uuid-1')
        && $r->data() === $body);
});

it('createInventoryItem não envia x-amzn-idempotency-token', function () {
    Http::fake(['*' => Http::response(['ok' => true])]);

    fbaInventory()->createInventoryItem(['sellerSku' => 'SKU-A']);

    Http::assertSent(fn (Request $r) => $r->url() === 'https://sellingpartnerapi-na.amazon.com/fba/inventory/v1/items'
        && ! $r->hasHeader('x-amzn-idempotency-token'));
});
