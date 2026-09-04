<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorShipments;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorShipmentsIntegration(): FakeIntegration
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

function vendorShipmentsEndpoint(): VendorShipments
{
    return new VendorShipments(new Client(vendorShipmentsIntegration()));
}

function vendorShipmentsAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_SHIPMENTS_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/shipping/v1';

it('submitShipmentConfirmations POSTs /shipmentConfirmations with the body', function () {
    Http::fake([VENDOR_SHIPMENTS_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorShipmentsEndpoint()->submitShipmentConfirmations($body);

    expect($resp['ok'])->toBeTrue();
    vendorShipmentsAssertSent('POST', VENDOR_SHIPMENTS_BASE.'/shipmentConfirmations', $body);
});

it('submitShipments POSTs /shipments with the body', function () {
    Http::fake([VENDOR_SHIPMENTS_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorShipmentsEndpoint()->submitShipments($body);

    expect($resp['ok'])->toBeTrue();
    vendorShipmentsAssertSent('POST', VENDOR_SHIPMENTS_BASE.'/shipments', $body);
});

it('getShipmentDetails GETs /shipments', function () {
    Http::fake([VENDOR_SHIPMENTS_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorShipmentsEndpoint()->getShipmentDetails(['limit' => 5, 'currentShipmentStatus' => 'SHIPPED']);

    expect($resp['ok'])->toBeTrue();
    vendorShipmentsAssertSent('GET', VENDOR_SHIPMENTS_BASE.'/shipments?limit=5&currentShipmentStatus=SHIPPED');
});

it('getShipmentLabels GETs /transportLabels', function () {
    Http::fake([VENDOR_SHIPMENTS_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorShipmentsEndpoint()->getShipmentLabels(['labelCreatedAfter' => '2026-01-01T00:00:00Z']);

    expect($resp['ok'])->toBeTrue();
    vendorShipmentsAssertSent('GET', VENDOR_SHIPMENTS_BASE.'/transportLabels?labelCreatedAfter=2026-01-01T00%3A00%3A00Z');
});
