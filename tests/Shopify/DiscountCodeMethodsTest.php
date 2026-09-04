<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Discount\DiscountCodeMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1DiscountCodeIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1DiscountCode(): DiscountCodeMethods
{
    $integration = shopifyRest1DiscountCodeIntegration();

    return new DiscountCodeMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1DiscountCodeAssertSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify DiscountCodeMethods', function () {
    it('list: GET /price_rules/20/discount_codes.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1DiscountCode()->list(20))->toBe(['ok' => true]);

        shopifyRest1DiscountCodeAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/price_rules/20/discount_codes.json');
    });

    it('count: GET /discount_codes/count.json?times_used_min=1', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1DiscountCode()->count(['times_used_min' => 1]))->toBe(['ok' => true]);

        shopifyRest1DiscountCodeAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/discount_codes/count.json?times_used_min=1');
    });

    it('get: GET /price_rules/20/discount_codes/30.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1DiscountCode()->get(20, 30))->toBe(['ok' => true]);

        shopifyRest1DiscountCodeAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/price_rules/20/discount_codes/30.json');
    });

    it('lookup: GET /discount_codes/lookup.json?code=PROMO10', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1DiscountCode()->lookup('PROMO10'))->toBe(['ok' => true]);

        shopifyRest1DiscountCodeAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/discount_codes/lookup.json?code=PROMO10');
    });

    it('create: POST /price_rules/20/discount_codes.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1DiscountCode()->create(20, ['code' => 'PROMO10']))->toBe(['ok' => true]);

        shopifyRest1DiscountCodeAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/price_rules/20/discount_codes.json', ['discount_code' => ['code' => 'PROMO10']]);
    });

    it('update: PUT /price_rules/20/discount_codes/30.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1DiscountCode()->update(20, 30, ['code' => 'PROMO20']))->toBe(['ok' => true]);

        shopifyRest1DiscountCodeAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/price_rules/20/discount_codes/30.json', ['discount_code' => ['code' => 'PROMO20']]);
    });

    it('delete: DELETE /price_rules/20/discount_codes/30.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1DiscountCode()->delete(20, 30))->toBe(['ok' => true]);

        shopifyRest1DiscountCodeAssertSent('DELETE', 'https://loja-teste.myshopify.com/admin/api/2024-04/price_rules/20/discount_codes/30.json');
    });

    it('createBatch: POST /price_rules/20/batch.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1DiscountCode()->createBatch(20, [['code' => 'A'], ['code' => 'B']]))->toBe(['ok' => true]);

        shopifyRest1DiscountCodeAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/price_rules/20/batch.json', ['discount_codes' => [['code' => 'A'], ['code' => 'B']]]);
    });

    it('getBatch: GET /price_rules/20/batch/40.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1DiscountCode()->getBatch(20, 40))->toBe(['ok' => true]);

        shopifyRest1DiscountCodeAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/price_rules/20/batch/40.json');
    });

    it('listBatchCodes: GET /price_rules/20/batch/40/discount_codes.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1DiscountCode()->listBatchCodes(20, 40))->toBe(['ok' => true]);

        shopifyRest1DiscountCodeAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/price_rules/20/batch/40/discount_codes.json');
    });

    it('each: segue o Link header (page_info) ate esgotar', function () {
        Http::fake([
            '*/price_rules/20/discount_codes.json*' => Http::sequence()
                ->push(['discount_codes' => [['id' => 1], ['id' => 2]]], 200, [
                    'Link' => '<https://loja-teste.myshopify.com/admin/api/2024-04/price_rules/20/discount_codes.json?limit=250&page_info=CURSOR2>; rel="next"',
                ])
                ->push(['discount_codes' => [['id' => 3]]], 200, []),
        ]);

        $items = iterator_to_array(shopifyRest1DiscountCode()->each(20));

        expect(array_column($items, 'id'))->toBe([1, 2, 3]);

        Http::assertSent(fn (Request $r) => $r->url() === 'https://loja-teste.myshopify.com/admin/api/2024-04/price_rules/20/discount_codes.json?limit=250&page_info=CURSOR2');
    });
});
