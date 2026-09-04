<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentInventory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorDfInventoryIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: [
            'client_id' => 'test-client',
            'client_secret' => 'test-secret',
            'marketplace_id' => 'A2Q3Y263D00KWC',
        ],
        active: true,
        expired: false,
    );
}

function vendorDfInventoryEndpoint(): VendorDirectFulfillmentInventory
{
    return new VendorDirectFulfillmentInventory(new Client(vendorDfInventoryIntegration()));
}

function vendorDfInventoryAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_DIRECT_FULFILLMENT_INVENTORY_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/directFulfillment/inventory/v1';

it('submitInventoryUpdate POSTs /warehouses/WH%201/items with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_INVENTORY_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfInventoryEndpoint()->submitInventoryUpdate('WH 1', $body);

    expect($resp['ok'])->toBeTrue();
    vendorDfInventoryAssertSent('POST', VENDOR_DIRECT_FULFILLMENT_INVENTORY_BASE.'/warehouses/WH%201/items', $body);
});
