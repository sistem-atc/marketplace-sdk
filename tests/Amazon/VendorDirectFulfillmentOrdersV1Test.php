<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentOrdersV1;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorDfOrdersV1Integration(): FakeIntegration
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

function vendorDfOrdersV1Endpoint(): VendorDirectFulfillmentOrdersV1
{
    return new VendorDirectFulfillmentOrdersV1(new Client(vendorDfOrdersV1Integration()));
}

function vendorDfOrdersV1AssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_DIRECT_FULFILLMENT_ORDERS_V1_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/directFulfillment/orders/v1';

it('getOrders GETs /purchaseOrders', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_ORDERS_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfOrdersV1Endpoint()->getOrders('2026-01-01T00:00:00Z', '2026-01-02T00:00:00Z', ['status' => 'NEW']);

    expect($resp['ok'])->toBeTrue();
    vendorDfOrdersV1AssertSent('GET', VENDOR_DIRECT_FULFILLMENT_ORDERS_V1_BASE.'/purchaseOrders?createdAfter=2026-01-01T00%3A00%3A00Z&createdBefore=2026-01-02T00%3A00%3A00Z&status=NEW');
});

it('getOrder GETs /purchaseOrders/DF%2F1', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_ORDERS_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfOrdersV1Endpoint()->getOrder('DF/1');

    expect($resp['ok'])->toBeTrue();
    vendorDfOrdersV1AssertSent('GET', VENDOR_DIRECT_FULFILLMENT_ORDERS_V1_BASE.'/purchaseOrders/DF%2F1');
});

it('submitAcknowledgement POSTs /acknowledgements with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_ORDERS_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfOrdersV1Endpoint()->submitAcknowledgement($body);

    expect($resp['ok'])->toBeTrue();
    vendorDfOrdersV1AssertSent('POST', VENDOR_DIRECT_FULFILLMENT_ORDERS_V1_BASE.'/acknowledgements', $body);
});
