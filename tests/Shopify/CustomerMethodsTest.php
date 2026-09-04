<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Customer\CustomerMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1CustomerIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1Customer(): CustomerMethods
{
    $integration = shopifyRest1CustomerIntegration();

    return new CustomerMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1CustomerAssertSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify CustomerMethods', function () {
    it('list: GET /customers.json?limit=5', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Customer()->list(['limit' => 5]))->toBe(['ok' => true]);

        shopifyRest1CustomerAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/customers.json?limit=5');
    });

    it('count: GET /customers/count.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Customer()->count())->toBe(['ok' => true]);

        shopifyRest1CustomerAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/customers/count.json');
    });

    it('get: GET /customers/100.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Customer()->get(100))->toBe(['ok' => true]);

        shopifyRest1CustomerAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/customers/100.json');
    });

    it('search: GET /customers/search.json?query=email%3Aa%40b.c', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Customer()->search('email:a@b.c'))->toBe(['ok' => true]);

        shopifyRest1CustomerAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/customers/search.json?query=email%3Aa%40b.c');
    });

    it('orders: GET /customers/100/orders.json?status=any', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Customer()->orders(100, ['status' => 'any']))->toBe(['ok' => true]);

        shopifyRest1CustomerAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/customers/100/orders.json?status=any');
    });

    it('create: POST /customers.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Customer()->create(['email' => 'a@b.c']))->toBe(['ok' => true]);

        shopifyRest1CustomerAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/customers.json', ['customer' => ['email' => 'a@b.c']]);
    });

    it('update: PUT /customers/100.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Customer()->update(100, ['tags' => 'vip']))->toBe(['ok' => true]);

        shopifyRest1CustomerAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/customers/100.json', ['customer' => ['tags' => 'vip']]);
    });

    it('delete: DELETE /customers/100.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Customer()->delete(100))->toBe(['ok' => true]);

        shopifyRest1CustomerAssertSent('DELETE', 'https://loja-teste.myshopify.com/admin/api/2024-04/customers/100.json');
    });

    it('accountActivationUrl: POST /customers/100/account_activation_url.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Customer()->accountActivationUrl(100))->toBe(['ok' => true]);

        shopifyRest1CustomerAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/customers/100/account_activation_url.json');
    });

    it('sendInvite: POST /customers/100/send_invite.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Customer()->sendInvite(100, ['subject' => 'Oi']))->toBe(['ok' => true]);

        shopifyRest1CustomerAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/customers/100/send_invite.json', ['customer_invite' => ['subject' => 'Oi']]);
    });

    it('each: segue o Link header (page_info) ate esgotar', function () {
        Http::fake([
            '*/customers.json*' => Http::sequence()
                ->push(['customers' => [['id' => 1], ['id' => 2]]], 200, [
                    'Link' => '<https://loja-teste.myshopify.com/admin/api/2024-04/customers.json?limit=250&page_info=CURSOR2>; rel="next"',
                ])
                ->push(['customers' => [['id' => 3]]], 200, []),
        ]);

        $items = iterator_to_array(shopifyRest1Customer()->each());

        expect(array_column($items, 'id'))->toBe([1, 2, 3]);

        Http::assertSent(fn (Request $r) => $r->url() === 'https://loja-teste.myshopify.com/admin/api/2024-04/customers.json?limit=250&page_info=CURSOR2');
    });
});
