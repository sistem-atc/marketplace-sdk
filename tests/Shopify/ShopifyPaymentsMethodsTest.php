<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\ShopifyPaymentsMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1ShopifyPaymentsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1ShopifyPayments(): ShopifyPaymentsMethods
{
    $integration = shopifyRest1ShopifyPaymentsIntegration();

    return new ShopifyPaymentsMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1ShopifyPaymentsAssertSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify ShopifyPaymentsMethods', function () {
    it('balance: GET /shopify_payments/balance.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ShopifyPayments()->balance())->toBe(['ok' => true]);

        shopifyRest1ShopifyPaymentsAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/shopify_payments/balance.json');
    });

    it('listDisputes: GET /shopify_payments/disputes.json?status=won', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ShopifyPayments()->listDisputes(['status' => 'won']))->toBe(['ok' => true]);

        shopifyRest1ShopifyPaymentsAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/shopify_payments/disputes.json?status=won');
    });

    it('getDispute: GET /shopify_payments/disputes/88.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ShopifyPayments()->getDispute(88))->toBe(['ok' => true]);

        shopifyRest1ShopifyPaymentsAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/shopify_payments/disputes/88.json');
    });
});
