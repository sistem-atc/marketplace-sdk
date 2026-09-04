<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorOrders;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorOrdersIntegration(): FakeIntegration
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

function vendorOrdersEndpoint(): VendorOrders
{
    return new VendorOrders(new Client(vendorOrdersIntegration()));
}

function vendorOrdersAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_ORDERS_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/orders/v1';

it('getPurchaseOrders GETs /purchaseOrders', function () {
    Http::fake([VENDOR_ORDERS_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorOrdersEndpoint()->getPurchaseOrders(['limit' => 10, 'createdAfter' => '2026-01-01T00:00:00Z']);

    expect($resp['ok'])->toBeTrue();
    vendorOrdersAssertSent('GET', VENDOR_ORDERS_BASE.'/purchaseOrders?limit=10&createdAfter=2026-01-01T00%3A00%3A00Z');
});

it('getPurchaseOrder GETs /purchaseOrders/PO%2012345', function () {
    Http::fake([VENDOR_ORDERS_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorOrdersEndpoint()->getPurchaseOrder('PO 12345');

    expect($resp['ok'])->toBeTrue();
    vendorOrdersAssertSent('GET', VENDOR_ORDERS_BASE.'/purchaseOrders/PO%2012345');
});

it('submitAcknowledgement POSTs /acknowledgements with the body', function () {
    Http::fake([VENDOR_ORDERS_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorOrdersEndpoint()->submitAcknowledgement($body);

    expect($resp['ok'])->toBeTrue();
    vendorOrdersAssertSent('POST', VENDOR_ORDERS_BASE.'/acknowledgements', $body);
});

it('getPurchaseOrdersStatus GETs /purchaseOrdersStatus', function () {
    Http::fake([VENDOR_ORDERS_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorOrdersEndpoint()->getPurchaseOrdersStatus(['purchaseOrderNumber' => 'PO1']);

    expect($resp['ok'])->toBeTrue();
    vendorOrdersAssertSent('GET', VENDOR_ORDERS_BASE.'/purchaseOrdersStatus?purchaseOrderNumber=PO1');
});
