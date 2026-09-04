<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentShippingV1;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorDfShippingV1Integration(): FakeIntegration
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

function vendorDfShippingV1Endpoint(): VendorDirectFulfillmentShippingV1
{
    return new VendorDirectFulfillmentShippingV1(new Client(vendorDfShippingV1Integration()));
}

function vendorDfShippingV1AssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/directFulfillment/shipping/v1';

it('getShippingLabels GETs /shippingLabels', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingV1Endpoint()->getShippingLabels('2026-01-01T00:00:00Z', '2026-01-02T00:00:00Z', ['limit' => 5]);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingV1AssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/shippingLabels?createdAfter=2026-01-01T00%3A00%3A00Z&createdBefore=2026-01-02T00%3A00%3A00Z&limit=5');
});

it('submitShippingLabelRequest POSTs /shippingLabels with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfShippingV1Endpoint()->submitShippingLabelRequest($body);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingV1AssertSent('POST', VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/shippingLabels', $body);
});

it('getShippingLabel GETs /shippingLabels/PO%201', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingV1Endpoint()->getShippingLabel('PO 1');

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingV1AssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/shippingLabels/PO%201');
});

it('submitShipmentConfirmations POSTs /shipmentConfirmations with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfShippingV1Endpoint()->submitShipmentConfirmations($body);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingV1AssertSent('POST', VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/shipmentConfirmations', $body);
});

it('submitShipmentStatusUpdates POSTs /shipmentStatusUpdates with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfShippingV1Endpoint()->submitShipmentStatusUpdates($body);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingV1AssertSent('POST', VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/shipmentStatusUpdates', $body);
});

it('getCustomerInvoices GETs /customerInvoices', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingV1Endpoint()->getCustomerInvoices('2026-01-01T00:00:00Z', '2026-01-02T00:00:00Z');

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingV1AssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/customerInvoices?createdAfter=2026-01-01T00%3A00%3A00Z&createdBefore=2026-01-02T00%3A00%3A00Z');
});

it('getCustomerInvoice GETs /customerInvoices/PO1', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingV1Endpoint()->getCustomerInvoice('PO1');

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingV1AssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/customerInvoices/PO1');
});

it('getPackingSlips GETs /packingSlips', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingV1Endpoint()->getPackingSlips('2026-01-01T00:00:00Z', '2026-01-02T00:00:00Z', ['sortOrder' => 'DESC']);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingV1AssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/packingSlips?createdAfter=2026-01-01T00%3A00%3A00Z&createdBefore=2026-01-02T00%3A00%3A00Z&sortOrder=DESC');
});

it('getPackingSlip GETs /packingSlips/PO1', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingV1Endpoint()->getPackingSlip('PO1');

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingV1AssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_V1_BASE.'/packingSlips/PO1');
});
