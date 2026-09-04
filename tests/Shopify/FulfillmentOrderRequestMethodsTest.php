<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment\FulfillmentOrderRequestMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1FulfillmentOrderRequestIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1FulfillmentOrderRequest(): FulfillmentOrderRequestMethods
{
    $integration = shopifyRest1FulfillmentOrderRequestIntegration();

    return new FulfillmentOrderRequestMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1FulfillmentOrderRequestAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(function (Request $request) use ($method, $url, $body): bool {
        return $request->method() === $method
            && $request->url() === $url
            && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
            && ($body === null || $request->data() === $body);
    });
}

beforeEach(function () {
    config(['marketplaces.shopify.api_version' => '2024-04']);
    Http::preventStrayRequests();
});

describe('Shopify FulfillmentOrderRequestMethods', function () {
    it('listAssigned: GET /assigned_fulfillment_orders.json?assignment_status=cancellation_requested', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1FulfillmentOrderRequest()->listAssigned(['assignment_status' => 'cancellation_requested']))->toBe(['ok' => true]);

        shopifyRest1FulfillmentOrderRequestAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/assigned_fulfillment_orders.json?assignment_status=cancellation_requested');
    });

    it('createCancellationRequest: POST /fulfillment_orders/77/cancellation_request.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1FulfillmentOrderRequest()->createCancellationRequest(77, ['message' => 'cancela']))->toBe(['ok' => true]);

        shopifyRest1FulfillmentOrderRequestAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/fulfillment_orders/77/cancellation_request.json', ['cancellation_request' => ['message' => 'cancela']]);
    });

    it('acceptCancellationRequest: POST /fulfillment_orders/77/cancellation_request/accept.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1FulfillmentOrderRequest()->acceptCancellationRequest(77, ['message' => 'ok']))->toBe(['ok' => true]);

        shopifyRest1FulfillmentOrderRequestAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/fulfillment_orders/77/cancellation_request/accept.json', ['cancellation_request' => ['message' => 'ok']]);
    });

    it('rejectCancellationRequest: POST /fulfillment_orders/77/cancellation_request/reject.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1FulfillmentOrderRequest()->rejectCancellationRequest(77))->toBe(['ok' => true]);

        shopifyRest1FulfillmentOrderRequestAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/fulfillment_orders/77/cancellation_request/reject.json', ['cancellation_request' => []]);
    });
});
