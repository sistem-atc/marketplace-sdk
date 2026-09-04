<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Checkout\CheckoutMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1CheckoutIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1Checkout(): CheckoutMethods
{
    $integration = shopifyRest1CheckoutIntegration();

    return new CheckoutMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1CheckoutAssertSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify CheckoutMethods', function () {
    it('listAbandoned: GET /checkouts.json?status=open', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Checkout()->listAbandoned(['status' => 'open']))->toBe(['ok' => true]);

        shopifyRest1CheckoutAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/checkouts.json?status=open');
    });

    it('get: GET /checkouts/tok123.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Checkout()->get('tok123'))->toBe(['ok' => true]);

        shopifyRest1CheckoutAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/checkouts/tok123.json');
    });

    it('create: POST /checkouts.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Checkout()->create(['email' => 'a@b.c']))->toBe(['ok' => true]);

        shopifyRest1CheckoutAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/checkouts.json', ['checkout' => ['email' => 'a@b.c']]);
    });

    it('update: PUT /checkouts/tok123.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Checkout()->update('tok123', ['email' => 'x@y.z']))->toBe(['ok' => true]);

        shopifyRest1CheckoutAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/checkouts/tok123.json', ['checkout' => ['email' => 'x@y.z']]);
    });

    it('complete: POST /checkouts/tok123/complete.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Checkout()->complete('tok123'))->toBe(['ok' => true]);

        shopifyRest1CheckoutAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/checkouts/tok123/complete.json');
    });

    it('shippingRates: GET /checkouts/tok123/shipping_rates.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Checkout()->shippingRates('tok123'))->toBe(['ok' => true]);

        shopifyRest1CheckoutAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/checkouts/tok123/shipping_rates.json');
    });

    it('eachAbandoned: segue o Link header (page_info) ate esgotar', function () {
        Http::fake([
            '*/checkouts.json*' => Http::sequence()
                ->push(['checkouts' => [['id' => 1], ['id' => 2]]], 200, [
                    'Link' => '<https://loja-teste.myshopify.com/admin/api/2024-04/checkouts.json?limit=250&page_info=CURSOR2>; rel="next"',
                ])
                ->push(['checkouts' => [['id' => 3]]], 200, []),
        ]);

        $items = iterator_to_array(shopifyRest1Checkout()->eachAbandoned());

        expect(array_column($items, 'id'))->toBe([1, 2, 3]);

        Http::assertSent(fn (Request $r) => $r->url() === 'https://loja-teste.myshopify.com/admin/api/2024-04/checkouts.json?limit=250&page_info=CURSOR2');
    });
});
