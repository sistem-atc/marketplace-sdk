<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore\AssetMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1AssetIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1Asset(): AssetMethods
{
    $integration = shopifyRest1AssetIntegration();

    return new AssetMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1AssetAssertSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify AssetMethods', function () {
    it('list: GET /themes/11/assets.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Asset()->list(11))->toBe(['ok' => true]);

        shopifyRest1AssetAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/themes/11/assets.json');
    });

    it('get: GET /themes/11/assets.json?asset%5Bkey%5D=templates%2Findex.liquid', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Asset()->get(11, 'templates/index.liquid'))->toBe(['ok' => true]);

        shopifyRest1AssetAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/themes/11/assets.json?asset%5Bkey%5D=templates%2Findex.liquid');
    });

    it('put: PUT /themes/11/assets.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Asset()->put(11, ['key' => 'templates/index.liquid', 'value' => '<p>x</p>']))->toBe(['ok' => true]);

        shopifyRest1AssetAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/themes/11/assets.json', ['asset' => ['key' => 'templates/index.liquid', 'value' => '<p>x</p>']]);
    });

    it('delete: DELETE /themes/11/assets.json?asset%5Bkey%5D=templates%2Findex.liquid', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Asset()->delete(11, 'templates/index.liquid'))->toBe(['ok' => true]);

        shopifyRest1AssetAssertSent('DELETE', 'https://loja-teste.myshopify.com/admin/api/2024-04/themes/11/assets.json?asset%5Bkey%5D=templates%2Findex.liquid');
    });
});
