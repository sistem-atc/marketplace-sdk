<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\ProductListingMethods;

function shopifyRest3ProductListingMethods(): SistemAtc\Marketplaces\Shopify\Endpoints\Product\ProductListingMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shpat_fake',
        refreshToken: null,
        settings: ['shop_domain' => 'test-store.myshopify.com'],
        active: true,
        expired: false,
    );

    return new SistemAtc\Marketplaces\Shopify\Endpoints\Product\ProductListingMethods(HttpClientFactory::make($integration), $integration);
}

/** Afirma verbo + URL completa + header do token (+ body, quando informado). */
function shopifyRest3ProductListingSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(function (Request $r) use ($method, $url, $body): bool {
        return $r->method() === $method
            && $r->url() === $url
            && $r->hasHeader('X-Shopify-Access-Token', 'shpat_fake')
            && ($body === null || $r->data() === $body);
    });
}

beforeEach(function () {
    config(['marketplaces.shopify.api_version' => '2024-04']);
    Http::preventStrayRequests();
});

$base = 'https://test-store.myshopify.com/admin/api/2024-04';

describe('Shopify ProductListingMethods (REST chunk 3)', function () use ($base) {
    it('list -> GET product_listings.json', function () use ($base) {
        Http::fake(['*' => Http::response(['product_listings' => []])]);
        shopifyRest3ProductListingMethods()->list(['collection_id' => 9]);
        shopifyRest3ProductListingSent('GET', "$base/product_listings.json?collection_id=9");
    });

    it('count -> GET product_listings/count.json', function () use ($base) {
        Http::fake(['*' => Http::response(['count' => 4])]);
        expect(shopifyRest3ProductListingMethods()->count())->toBe(4);
        shopifyRest3ProductListingSent('GET', "$base/product_listings/count.json");
    });

    it('productIds -> GET product_listings/product_ids.json', function () use ($base) {
        Http::fake(['*' => Http::response(['product_ids' => [1, 2]])]);
        expect(shopifyRest3ProductListingMethods()->productIds()['product_ids'])->toBe([1, 2]);
        shopifyRest3ProductListingSent('GET', "$base/product_listings/product_ids.json");
    });

    it('get -> GET product_listings/{product_id}.json', function () use ($base) {
        Http::fake(['*' => Http::response(['product_listing' => ['product_id' => 10]])]);
        shopifyRest3ProductListingMethods()->get(10);
        shopifyRest3ProductListingSent('GET', "$base/product_listings/10.json");
    });

    it('update -> PUT product_listings/{product_id}.json embrulhado com product_id', function () use ($base) {
        Http::fake(['*' => Http::response(['product_listing' => ['product_id' => 10]])]);
        shopifyRest3ProductListingMethods()->update(10);
        shopifyRest3ProductListingSent('PUT', "$base/product_listings/10.json", ['product_listing' => ['product_id' => 10]]);
    });

    it('delete -> DELETE product_listings/{product_id}.json', function () use ($base) {
        Http::fake(['*' => Http::response([])]);
        shopifyRest3ProductListingMethods()->delete(10);
        shopifyRest3ProductListingSent('DELETE', "$base/product_listings/10.json");
    });
});
