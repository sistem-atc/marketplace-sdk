<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\FulfillmentOutbound;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function fulfillmentOutboundIntegration(): FakeIntegration
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

function fulfillmentOutboundEndpoint(): FulfillmentOutbound
{
    return new FulfillmentOutbound(new Client(fulfillmentOutboundIntegration()));
}

/** Verbo + URL completa + header LWA + (opcional) body JSON exato. */
function fulfillmentOutboundAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const FO_BASE = 'https://sellingpartnerapi-na.amazon.com/fba/outbound/2020-07-01';

it('getFulfillmentPreview POSTs /fulfillmentOrders/preview with the body', function () {
    Http::fake([FO_BASE.'/*' => Http::response(['payload' => ['fulfillmentPreviews' => []]])]);
    $body = ['address' => ['countryCode' => 'BR'], 'items' => [['sellerSku' => 'SKU-A', 'quantity' => 1, 'sellerFulfillmentOrderItemId' => '1']]];

    $resp = fulfillmentOutboundEndpoint()->getFulfillmentPreview($body);

    expect($resp)->toBe(['payload' => ['fulfillmentPreviews' => []]]);
    fulfillmentOutboundAssertSent('POST', FO_BASE.'/fulfillmentOrders/preview', $body);
});

it('deliveryOffers POSTs /deliveryOffers with the body', function () {
    Http::fake([FO_BASE.'/*' => Http::response(['payload' => ['deliveryOffers' => []]])]);
    $body = ['product' => ['sku' => 'SKU-A', 'amount' => ['value' => 1, 'unit' => 'Each']], 'terms' => []];

    fulfillmentOutboundEndpoint()->deliveryOffers($body);

    fulfillmentOutboundAssertSent('POST', FO_BASE.'/deliveryOffers', $body);
});

it('listAllFulfillmentOrders GETs /fulfillmentOrders with queryStartDate + nextToken', function () {
    Http::fake([FO_BASE.'/*' => Http::response(['payload' => ['fulfillmentOrders' => [], 'nextToken' => null]])]);

    $resp = fulfillmentOutboundEndpoint()->listAllFulfillmentOrders(['queryStartDate' => '2025-01-01T00:00:00Z', 'nextToken' => 'abc']);

    expect(data_get($resp, 'payload.fulfillmentOrders'))->toBe([]);
    fulfillmentOutboundAssertSent('GET', FO_BASE.'/fulfillmentOrders?queryStartDate=2025-01-01T00%3A00%3A00Z&nextToken=abc');
});

it('createFulfillmentOrder POSTs /fulfillmentOrders with the body', function () {
    Http::fake([FO_BASE.'/*' => Http::response([], 200)]);
    $body = ['sellerFulfillmentOrderId' => 'MCF-1', 'displayableOrderId' => 'MCF-1', 'shippingSpeedCategory' => 'Standard'];

    fulfillmentOutboundEndpoint()->createFulfillmentOrder($body);

    fulfillmentOutboundAssertSent('POST', FO_BASE.'/fulfillmentOrders', $body);
});

it('getPackageTrackingDetails GETs /tracking?packageNumber=', function () {
    Http::fake([FO_BASE.'/*' => Http::response(['payload' => ['packageNumber' => 12345, 'currentStatus' => 'DELIVERED']])]);

    $resp = fulfillmentOutboundEndpoint()->getPackageTrackingDetails(12345);

    expect(data_get($resp, 'payload.currentStatus'))->toBe('DELIVERED');
    fulfillmentOutboundAssertSent('GET', FO_BASE.'/tracking?packageNumber=12345');
});

it('listReturnReasonCodes GETs /returnReasonCodes with sellerSku first + extras', function () {
    Http::fake([FO_BASE.'/*' => Http::response(['payload' => ['reasonCodeDetails' => []]])]);

    fulfillmentOutboundEndpoint()->listReturnReasonCodes('SKU-A', ['marketplaceId' => 'A2Q3Y263D00KWC', 'language' => 'pt_BR']);

    fulfillmentOutboundAssertSent('GET', FO_BASE.'/returnReasonCodes?sellerSku=SKU-A&marketplaceId=A2Q3Y263D00KWC&language=pt_BR');
});

it('createFulfillmentReturn PUTs /fulfillmentOrders/{id}/return with the body (id url-encoded)', function () {
    Http::fake([FO_BASE.'/*' => Http::response(['payload' => ['returnItems' => []]])]);
    $body = ['items' => [['sellerReturnItemId' => 'R1', 'sellerFulfillmentOrderItemId' => '1', 'amazonShipmentId' => 'S1', 'returnReasonCode' => 'X']]];

    fulfillmentOutboundEndpoint()->createFulfillmentReturn('MCF 1', $body);

    fulfillmentOutboundAssertSent('PUT', FO_BASE.'/fulfillmentOrders/MCF%201/return', $body);
});

it('getFulfillmentOrder GETs /fulfillmentOrders/{id}', function () {
    Http::fake([FO_BASE.'/*' => Http::response(['payload' => ['fulfillmentOrder' => ['sellerFulfillmentOrderId' => 'MCF-1']]])]);

    $resp = fulfillmentOutboundEndpoint()->getFulfillmentOrder('MCF-1');

    expect(data_get($resp, 'payload.fulfillmentOrder.sellerFulfillmentOrderId'))->toBe('MCF-1');
    fulfillmentOutboundAssertSent('GET', FO_BASE.'/fulfillmentOrders/MCF-1');
});

it('updateFulfillmentOrder PUTs /fulfillmentOrders/{id} with the body', function () {
    Http::fake([FO_BASE.'/*' => Http::response([], 200)]);
    $body = ['shippingSpeedCategory' => 'Expedited'];

    fulfillmentOutboundEndpoint()->updateFulfillmentOrder('MCF-1', $body);

    fulfillmentOutboundAssertSent('PUT', FO_BASE.'/fulfillmentOrders/MCF-1', $body);
});

it('cancelFulfillmentOrder PUTs /fulfillmentOrders/{id}/cancel', function () {
    Http::fake([FO_BASE.'/*' => Http::response([], 200)]);

    fulfillmentOutboundEndpoint()->cancelFulfillmentOrder('MCF-1');

    fulfillmentOutboundAssertSent('PUT', FO_BASE.'/fulfillmentOrders/MCF-1/cancel');
});

it('submitFulfillmentOrderStatusUpdate PUTs /fulfillmentOrders/{id}/status with the body', function () {
    Http::fake([FO_BASE.'/*' => Http::response([], 200)]);
    $body = ['fulfillmentOrderStatus' => 'Complete'];

    fulfillmentOutboundEndpoint()->submitFulfillmentOrderStatusUpdate('MCF-1', $body);

    fulfillmentOutboundAssertSent('PUT', FO_BASE.'/fulfillmentOrders/MCF-1/status', $body);
});

it('getFeatures GETs /features?marketplaceId=', function () {
    Http::fake([FO_BASE.'/*' => Http::response(['payload' => ['features' => [['featureName' => 'BLANK_BOX']]]])]);

    $resp = fulfillmentOutboundEndpoint()->getFeatures('A2Q3Y263D00KWC');

    expect(data_get($resp, 'payload.features.0.featureName'))->toBe('BLANK_BOX');
    fulfillmentOutboundAssertSent('GET', FO_BASE.'/features?marketplaceId=A2Q3Y263D00KWC');
});

it('getFeatureInventory GETs /features/inventory/{featureName} with marketplaceId + nextToken', function () {
    Http::fake([FO_BASE.'/*' => Http::response(['payload' => ['featureSkus' => []]])]);

    fulfillmentOutboundEndpoint()->getFeatureInventory('BLANK_BOX', 'A2Q3Y263D00KWC', ['nextToken' => 'n1']);

    fulfillmentOutboundAssertSent('GET', FO_BASE.'/features/inventory/BLANK_BOX?marketplaceId=A2Q3Y263D00KWC&nextToken=n1');
});

it('getFeatureSKU GETs /features/inventory/{featureName}/{sellerSku} (sku url-encoded)', function () {
    Http::fake([FO_BASE.'/*' => Http::response(['payload' => ['isEligible' => true]])]);

    $resp = fulfillmentOutboundEndpoint()->getFeatureSKU('BLANK_BOX', 'SKU/A', 'A2Q3Y263D00KWC');

    expect(data_get($resp, 'payload.isEligible'))->toBeTrue();
    fulfillmentOutboundAssertSent('GET', FO_BASE.'/features/inventory/BLANK_BOX/SKU%2FA?marketplaceId=A2Q3Y263D00KWC');
});

it('getFulfillmentOrder returns [] on 404', function () {
    Http::fake([FO_BASE.'/*' => Http::response(['errors' => [['code' => 'NotFound']]], 404)]);

    expect(fulfillmentOutboundEndpoint()->getFulfillmentOrder('NOPE'))->toBe([]);
});
