<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Awd;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function awdIntegration(): FakeIntegration
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

function awdEndpoint(): Awd
{
    return new Awd(new Client(awdIntegration()));
}

function awdAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const AWD_BASE = 'https://sellingpartnerapi-na.amazon.com/awd/2024-05-09';

// ---- inbound orders

it('createInbound POSTs /inboundOrders with the body', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['orderId' => 'IN-1'], 201)]);
    $body = ['originAddress' => ['countryCode' => 'BR'], 'packagesToInbound' => []];

    $resp = awdEndpoint()->createInbound($body);

    expect($resp['orderId'])->toBe('IN-1');
    awdAssertSent('POST', AWD_BASE.'/inboundOrders', $body);
});

it('getInbound GETs /inboundOrders/{orderId}', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['orderId' => 'IN-1', 'orderStatus' => 'DRAFT'])]);

    $resp = awdEndpoint()->getInbound('IN-1');

    expect($resp['orderStatus'])->toBe('DRAFT');
    awdAssertSent('GET', AWD_BASE.'/inboundOrders/IN-1');
});

it('updateInbound PUTs /inboundOrders/{orderId} with the body', function () {
    Http::fake([AWD_BASE.'/*' => Http::response([], 200)]);
    $body = ['packagesToInbound' => [['packageQuantity' => 2]]];

    awdEndpoint()->updateInbound('IN-1', $body);

    awdAssertSent('PUT', AWD_BASE.'/inboundOrders/IN-1', $body);
});

it('cancelInbound POSTs /inboundOrders/{orderId}/cancellation', function () {
    Http::fake([AWD_BASE.'/*' => Http::response([], 200)]);

    awdEndpoint()->cancelInbound('IN 1');

    awdAssertSent('POST', AWD_BASE.'/inboundOrders/IN%201/cancellation');
});

it('confirmInbound POSTs /inboundOrders/{orderId}/confirmation', function () {
    Http::fake([AWD_BASE.'/*' => Http::response([], 200)]);

    awdEndpoint()->confirmInbound('IN-1');

    awdAssertSent('POST', AWD_BASE.'/inboundOrders/IN-1/confirmation');
});

// ---- inbound shipments

it('getInboundShipment GETs /inboundShipments/{shipmentId}?skuQuantities=', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['shipmentId' => 'SH-1'])]);

    $resp = awdEndpoint()->getInboundShipment('SH-1', ['skuQuantities' => 'SHOW']);

    expect($resp['shipmentId'])->toBe('SH-1');
    awdAssertSent('GET', AWD_BASE.'/inboundShipments/SH-1?skuQuantities=SHOW');
});

it('getInboundShipmentLabels GETs /inboundShipments/{shipmentId}/labels with pageType + formatType', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['labelStatus' => 'GENERATED'])]);

    awdEndpoint()->getInboundShipmentLabels('SH-1', ['pageType' => 'PLAIN_PAPER', 'formatType' => 'PDF']);

    awdAssertSent('GET', AWD_BASE.'/inboundShipments/SH-1/labels?pageType=PLAIN_PAPER&formatType=PDF');
});

it('getLabelPageTypes GETs /inboundShipments/{shipmentId}/labelPageTypes', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['labelPageTypes' => ['PLAIN_PAPER']])]);

    $resp = awdEndpoint()->getLabelPageTypes('SH-1');

    expect($resp['labelPageTypes'])->toBe(['PLAIN_PAPER']);
    awdAssertSent('GET', AWD_BASE.'/inboundShipments/SH-1/labelPageTypes');
});

it('updateInboundShipmentTransportDetails PUTs /inboundShipments/{shipmentId}/transport with the body', function () {
    Http::fake([AWD_BASE.'/*' => Http::response([], 200)]);
    $body = ['transportationDetails' => [['carrierCode' => 'UPS', 'trackingId' => 'T1']]];

    awdEndpoint()->updateInboundShipmentTransportDetails('SH-1', $body);

    awdAssertSent('PUT', AWD_BASE.'/inboundShipments/SH-1/transport', $body);
});

it('checkInboundEligibility POSTs /inboundEligibility with the body', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['packagesToInbound' => []])]);
    $body = ['packagesToInbound' => [['distributionPackage' => ['type' => 'CASE']]]];

    awdEndpoint()->checkInboundEligibility($body);

    awdAssertSent('POST', AWD_BASE.'/inboundEligibility', $body);
});

it('listInboundShipments GETs /inboundShipments with filters + nextToken', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['shipments' => [], 'nextToken' => null])]);

    $resp = awdEndpoint()->listInboundShipments(['shipmentStatus' => 'CREATED', 'maxResults' => 25, 'nextToken' => 'n1']);

    expect($resp['shipments'])->toBe([]);
    awdAssertSent('GET', AWD_BASE.'/inboundShipments?shipmentStatus=CREATED&maxResults=25&nextToken=n1');
});

// ---- inventory

it('listInventory GETs /inventory with sku + details', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['inventory' => [['sku' => 'SKU-A', 'totalOnhandQuantity' => 10]]])]);

    $resp = awdEndpoint()->listInventory(['sku' => 'SKU-A', 'details' => 'SHOW']);

    expect($resp['inventory'][0]['totalOnhandQuantity'])->toBe(10);
    awdAssertSent('GET', AWD_BASE.'/inventory?sku=SKU-A&details=SHOW');
});

// ---- outbound orders

it('listOutbounds GETs /outboundOrders with updatedAfter', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['outbounds' => []])]);

    awdEndpoint()->listOutbounds(['updatedAfter' => '2026-01-01T00:00:00Z']);

    awdAssertSent('GET', AWD_BASE.'/outboundOrders?updatedAfter=2026-01-01T00%3A00%3A00Z');
});

it('createOutbound POSTs /outboundOrders with the body', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['orderId' => 'OUT-1'], 201)]);
    $body = ['orderPreferences' => [], 'packagesToOutbound' => []];

    $resp = awdEndpoint()->createOutbound($body);

    expect($resp['orderId'])->toBe('OUT-1');
    awdAssertSent('POST', AWD_BASE.'/outboundOrders', $body);
});

it('getOutbound GETs /outboundOrders/{orderId}', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['orderId' => 'OUT-1'])]);

    awdEndpoint()->getOutbound('OUT-1');

    awdAssertSent('GET', AWD_BASE.'/outboundOrders/OUT-1');
});

it('updateOutbound PUTs /outboundOrders/{orderId} with the body', function () {
    Http::fake([AWD_BASE.'/*' => Http::response([], 200)]);
    $body = ['packagesToOutbound' => [['packageQuantity' => 1]]];

    awdEndpoint()->updateOutbound('OUT-1', $body);

    awdAssertSent('PUT', AWD_BASE.'/outboundOrders/OUT-1', $body);
});

it('confirmOutbound POSTs /outboundOrders/{orderId}/confirmation', function () {
    Http::fake([AWD_BASE.'/*' => Http::response([], 200)]);

    awdEndpoint()->confirmOutbound('OUT-1');

    awdAssertSent('POST', AWD_BASE.'/outboundOrders/OUT-1/confirmation');
});

// ---- replenishment orders

it('listReplenishmentOrders GETs /replenishmentOrders with nextToken', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['replenishmentOrders' => []])]);

    awdEndpoint()->listReplenishmentOrders(['nextToken' => 'n1']);

    awdAssertSent('GET', AWD_BASE.'/replenishmentOrders?nextToken=n1');
});

it('createReplenishmentOrder POSTs /replenishmentOrders with the body', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['orderId' => 'REP-1'], 201)]);
    $body = ['skuQuantities' => [['sku' => 'SKU-A', 'quantity' => 5]]];

    awdEndpoint()->createReplenishmentOrder($body);

    awdAssertSent('POST', AWD_BASE.'/replenishmentOrders', $body);
});

it('getReplenishmentOrder GETs /replenishmentOrders/{orderId}', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['orderId' => 'REP-1'])]);

    awdEndpoint()->getReplenishmentOrder('REP-1');

    awdAssertSent('GET', AWD_BASE.'/replenishmentOrders/REP-1');
});

it('confirmReplenishmentOrder POSTs /replenishmentOrders/{orderId}/confirmation', function () {
    Http::fake([AWD_BASE.'/*' => Http::response([], 200)]);

    awdEndpoint()->confirmReplenishmentOrder('REP-1');

    awdAssertSent('POST', AWD_BASE.'/replenishmentOrders/REP-1/confirmation');
});

it('getInbound returns [] on 404', function () {
    Http::fake([AWD_BASE.'/*' => Http::response(['errors' => []], 404)]);

    expect(awdEndpoint()->getInbound('NOPE'))->toBe([]);
});
