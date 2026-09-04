<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\FulfillmentOutbound2026;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function fulfillmentOutbound2026Integration(): FakeIntegration
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

function fulfillmentOutbound2026Endpoint(): FulfillmentOutbound2026
{
    return new FulfillmentOutbound2026(new Client(fulfillmentOutbound2026Integration()));
}

function fulfillmentOutbound2026AssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const FO26_BASE = 'https://sellingpartnerapi-na.amazon.com/fulfillment/outbound/2026-07-04';

it('getOrderPreview POSTs /previews with the body', function () {
    Http::fake([FO26_BASE.'/*' => Http::response(['previews' => []])]);
    $body = ['destination' => ['countryCode' => 'BR'], 'items' => [['sku' => 'SKU-A', 'quantity' => 1]]];

    $resp = fulfillmentOutbound2026Endpoint()->getOrderPreview($body);

    expect($resp)->toBe(['previews' => []]);
    fulfillmentOutbound2026AssertSent('POST', FO26_BASE.'/previews', $body);
});

it('getOffers POSTs /offers with the body', function () {
    Http::fake([FO26_BASE.'/*' => Http::response(['offers' => []])]);
    $body = ['product' => ['sku' => 'SKU-A']];

    fulfillmentOutbound2026Endpoint()->getOffers($body);

    fulfillmentOutbound2026AssertSent('POST', FO26_BASE.'/offers', $body);
});

it('cancelOrder PUTs /orders/{orderId}/cancel', function () {
    Http::fake([FO26_BASE.'/*' => Http::response([], 200)]);

    fulfillmentOutbound2026Endpoint()->cancelOrder('ORD-1');

    fulfillmentOutbound2026AssertSent('PUT', FO26_BASE.'/orders/ORD-1/cancel');
});

it('updateOrderStatus PUTs /orders/{orderId}/status with the body', function () {
    Http::fake([FO26_BASE.'/*' => Http::response([], 200)]);
    $body = ['status' => 'COMPLETE'];

    fulfillmentOutbound2026Endpoint()->updateOrderStatus('ORD-1', $body);

    fulfillmentOutbound2026AssertSent('PUT', FO26_BASE.'/orders/ORD-1/status', $body);
});

it('updatePackage PUTs /orders/{orderId}/packages/{packageId} with the body (ids url-encoded)', function () {
    Http::fake([FO26_BASE.'/*' => Http::response([], 200)]);
    $body = ['trackingId' => 'TRK-1'];

    fulfillmentOutbound2026Endpoint()->updatePackage('ORD 1', 'PKG/1', $body);

    fulfillmentOutbound2026AssertSent('PUT', FO26_BASE.'/orders/ORD%201/packages/PKG%2F1', $body);
});

it('updateOrder PUTs /orders/{orderId} with the body', function () {
    Http::fake([FO26_BASE.'/*' => Http::response([], 200)]);
    $body = ['shippingSpeed' => 'EXPEDITED'];

    fulfillmentOutbound2026Endpoint()->updateOrder('ORD-1', $body);

    fulfillmentOutbound2026AssertSent('PUT', FO26_BASE.'/orders/ORD-1', $body);
});

it('getOrder GETs /orders/{orderId} with the shipments flag', function () {
    Http::fake([FO26_BASE.'/*' => Http::response(['orderId' => 'ORD-1', 'status' => 'RECEIVED'])]);

    $resp = fulfillmentOutbound2026Endpoint()->getOrder('ORD-1', ['shipments' => 'true']);

    expect($resp['status'])->toBe('RECEIVED');
    fulfillmentOutbound2026AssertSent('GET', FO26_BASE.'/orders/ORD-1?shipments=true');
});

it('listOrders GETs /orders with updatedAfter + pageToken', function () {
    Http::fake([FO26_BASE.'/*' => Http::response(['orders' => [], 'pagination' => ['nextToken' => null]])]);

    $resp = fulfillmentOutbound2026Endpoint()->listOrders(['updatedAfter' => '2026-01-01T00:00:00Z', 'pageToken' => 'p1']);

    expect($resp['orders'])->toBe([]);
    fulfillmentOutbound2026AssertSent('GET', FO26_BASE.'/orders?updatedAfter=2026-01-01T00%3A00%3A00Z&pageToken=p1');
});

it('createOrder POSTs /orders with the body', function () {
    Http::fake([FO26_BASE.'/*' => Http::response(['orderId' => 'ORD-2'], 200)]);
    $body = ['sellerOrderId' => 'MCF-2', 'items' => [['sku' => 'SKU-A', 'quantity' => 1]]];

    $resp = fulfillmentOutbound2026Endpoint()->createOrder($body);

    expect($resp['orderId'])->toBe('ORD-2');
    fulfillmentOutbound2026AssertSent('POST', FO26_BASE.'/orders', $body);
});

it('withFulfillmentServiceId envia x-amzn-fulfillment-service-id em todas as chamadas (clone imutável)', function () {
    Http::fake(['*' => Http::response(['ok' => true], 200)]);

    $plain = fulfillmentOutbound2026Endpoint();
    $svc = $plain->withFulfillmentServiceId('svc-123');

    expect($svc)->not->toBe($plain);

    $svc->listOrders(['updatedAfter' => '2026-01-01T00:00:00Z']);
    $svc->createOrder(['orderId' => 'o1']);
    $svc->cancelOrder('o1');
    $svc->updatePackage('o1', 'p1', ['status' => 'x']);
    $plain->listOrders();

    Http::assertSent(fn ($r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/fulfillment/outbound/2026-07-04/orders?updatedAfter=2026-01-01T00%3A00%3A00Z'
        && $r->hasHeader('x-amzn-fulfillment-service-id', 'svc-123'));
    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/fulfillment/outbound/2026-07-04/orders'
        && $r->hasHeader('x-amzn-fulfillment-service-id', 'svc-123'));
    Http::assertSent(fn ($r) => $r->method() === 'PUT'
        && str_ends_with($r->url(), '/orders/o1/cancel')
        && $r->hasHeader('x-amzn-fulfillment-service-id', 'svc-123'));
    Http::assertSent(fn ($r) => $r->method() === 'PUT'
        && str_ends_with($r->url(), '/orders/o1/packages/p1')
        && $r->hasHeader('x-amzn-fulfillment-service-id', 'svc-123'));
    // instância original: sem header
    Http::assertSent(fn ($r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/fulfillment/outbound/2026-07-04/orders'
        && ! $r->hasHeader('x-amzn-fulfillment-service-id'));
});
