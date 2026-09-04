<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentOrders;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorDfOrdersIntegration(): FakeIntegration
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

function vendorDfOrdersEndpoint(): VendorDirectFulfillmentOrders
{
    return new VendorDirectFulfillmentOrders(new Client(vendorDfOrdersIntegration()));
}

function vendorDfOrdersAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_DIRECT_FULFILLMENT_ORDERS_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/directFulfillment/orders/2021-12-28';

it('getOrders GETs /purchaseOrders', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_ORDERS_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfOrdersEndpoint()->getOrders('2026-01-01T00:00:00Z', '2026-01-02T00:00:00Z', ['limit' => 20]);

    expect($resp['ok'])->toBeTrue();
    vendorDfOrdersAssertSent('GET', VENDOR_DIRECT_FULFILLMENT_ORDERS_BASE.'/purchaseOrders?createdAfter=2026-01-01T00%3A00%3A00Z&createdBefore=2026-01-02T00%3A00%3A00Z&limit=20');
});

it('getOrder GETs /purchaseOrders/DF-1', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_ORDERS_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfOrdersEndpoint()->getOrder('DF-1');

    expect($resp['ok'])->toBeTrue();
    vendorDfOrdersAssertSent('GET', VENDOR_DIRECT_FULFILLMENT_ORDERS_BASE.'/purchaseOrders/DF-1');
});

it('submitAcknowledgement POSTs /acknowledgements with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_ORDERS_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfOrdersEndpoint()->submitAcknowledgement($body);

    expect($resp['ok'])->toBeTrue();
    vendorDfOrdersAssertSent('POST', VENDOR_DIRECT_FULFILLMENT_ORDERS_BASE.'/acknowledgements', $body);
});
