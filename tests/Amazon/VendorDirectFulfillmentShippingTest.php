<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentShipping;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorDfShippingIntegration(): FakeIntegration
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

function vendorDfShippingEndpoint(): VendorDirectFulfillmentShipping
{
    return new VendorDirectFulfillmentShipping(new Client(vendorDfShippingIntegration()));
}

function vendorDfShippingAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/directFulfillment/shipping/2021-12-28';

it('getShippingLabels GETs /shippingLabels', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingEndpoint()->getShippingLabels('2026-01-01T00:00:00Z', '2026-01-02T00:00:00Z', ['shipFromPartyId' => 'WH1']);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingAssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/shippingLabels?createdAfter=2026-01-01T00%3A00%3A00Z&createdBefore=2026-01-02T00%3A00%3A00Z&shipFromPartyId=WH1');
});

it('submitShippingLabelRequest POSTs /shippingLabels with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfShippingEndpoint()->submitShippingLabelRequest($body);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingAssertSent('POST', VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/shippingLabels', $body);
});

it('getShippingLabel GETs /shippingLabels/PO1', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingEndpoint()->getShippingLabel('PO1');

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingAssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/shippingLabels/PO1');
});

it('createShippingLabels POSTs /shippingLabels/PO%201 with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfShippingEndpoint()->createShippingLabels('PO 1', $body);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingAssertSent('POST', VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/shippingLabels/PO%201', $body);
});

it('submitShipmentConfirmations POSTs /shipmentConfirmations with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfShippingEndpoint()->submitShipmentConfirmations($body);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingAssertSent('POST', VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/shipmentConfirmations', $body);
});

it('submitShipmentStatusUpdates POSTs /shipmentStatusUpdates with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfShippingEndpoint()->submitShipmentStatusUpdates($body);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingAssertSent('POST', VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/shipmentStatusUpdates', $body);
});

it('getCustomerInvoices GETs /customerInvoices', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingEndpoint()->getCustomerInvoices('2026-01-01T00:00:00Z', '2026-01-02T00:00:00Z');

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingAssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/customerInvoices?createdAfter=2026-01-01T00%3A00%3A00Z&createdBefore=2026-01-02T00%3A00%3A00Z');
});

it('getCustomerInvoice GETs /customerInvoices/PO1', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingEndpoint()->getCustomerInvoice('PO1');

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingAssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/customerInvoices/PO1');
});

it('getPackingSlips GETs /packingSlips', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingEndpoint()->getPackingSlips('2026-01-01T00:00:00Z', '2026-01-02T00:00:00Z', ['nextToken' => 'abc']);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingAssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/packingSlips?createdAfter=2026-01-01T00%3A00%3A00Z&createdBefore=2026-01-02T00%3A00%3A00Z&nextToken=abc');
});

it('getPackingSlip GETs /packingSlips/PO1', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfShippingEndpoint()->getPackingSlip('PO1');

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingAssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/packingSlips/PO1');
});

it('createContainerLabel POSTs /containerLabel with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfShippingEndpoint()->createContainerLabel($body);

    expect($resp['ok'])->toBeTrue();
    vendorDfShippingAssertSent('POST', VENDOR_DIRECT_FULFILLMENT_SHIPPING_BASE.'/containerLabel', $body);
});
