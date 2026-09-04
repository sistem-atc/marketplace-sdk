<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Collection\CollectionMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1CollectionIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1Collection(): CollectionMethods
{
    $integration = shopifyRest1CollectionIntegration();

    return new CollectionMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1CollectionAssertSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify CollectionMethods', function () {
    it('get: GET /collections/10.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->get(10))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/collections/10.json');
    });

    it('products: GET /collections/10/products.json?limit=50', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->products(10, ['limit' => 50]))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/collections/10/products.json?limit=50');
    });

    it('listCustom: GET /custom_collections.json?handle=promo', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->listCustom(['handle' => 'promo']))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/custom_collections.json?handle=promo');
    });

    it('countCustom: GET /custom_collections/count.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->countCustom())->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/custom_collections/count.json');
    });

    it('getCustom: GET /custom_collections/10.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->getCustom(10))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/custom_collections/10.json');
    });

    it('createCustom: POST /custom_collections.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->createCustom(['title' => 'Promo']))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/custom_collections.json', ['custom_collection' => ['title' => 'Promo']]);
    });

    it('updateCustom: PUT /custom_collections/10.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->updateCustom(10, ['title' => 'Promo 2']))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/custom_collections/10.json', ['custom_collection' => ['title' => 'Promo 2']]);
    });

    it('deleteCustom: DELETE /custom_collections/10.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->deleteCustom(10))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('DELETE', 'https://loja-teste.myshopify.com/admin/api/2024-04/custom_collections/10.json');
    });

    it('listCollects: GET /collects.json?collection_id=10', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->listCollects(['collection_id' => 10]))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/collects.json?collection_id=10');
    });

    it('countCollects: GET /collects/count.json?product_id=3', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->countCollects(['product_id' => 3]))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/collects/count.json?product_id=3');
    });

    it('getCollect: GET /collects/55.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->getCollect(55))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/collects/55.json');
    });

    it('createCollect: POST /collects.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->createCollect(3, 10))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/collects.json', ['collect' => ['product_id' => 3, 'collection_id' => 10]]);
    });

    it('deleteCollect: DELETE /collects/55.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->deleteCollect(55))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('DELETE', 'https://loja-teste.myshopify.com/admin/api/2024-04/collects/55.json');
    });

    it('listListings: GET /collection_listings.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->listListings())->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/collection_listings.json');
    });

    it('getListing: GET /collection_listings/10.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->getListing(10))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/collection_listings/10.json');
    });

    it('listingProductIds: GET /collection_listings/10/product_ids.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->listingProductIds(10))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/collection_listings/10/product_ids.json');
    });

    it('putListing: PUT /collection_listings/10.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->putListing(10))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/collection_listings/10.json', ['collection_listing' => []]);
    });

    it('deleteListing: DELETE /collection_listings/10.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1Collection()->deleteListing(10))->toBe(['ok' => true]);

        shopifyRest1CollectionAssertSent('DELETE', 'https://loja-teste.myshopify.com/admin/api/2024-04/collection_listings/10.json');
    });

    it('eachCustom: segue o Link header (page_info) ate esgotar', function () {
        Http::fake([
            '*/custom_collections.json*' => Http::sequence()
                ->push(['custom_collections' => [['id' => 1], ['id' => 2]]], 200, [
                    'Link' => '<https://loja-teste.myshopify.com/admin/api/2024-04/custom_collections.json?limit=250&page_info=CURSOR2>; rel="next"',
                ])
                ->push(['custom_collections' => [['id' => 3]]], 200, []),
        ]);

        $items = iterator_to_array(shopifyRest1Collection()->eachCustom());

        expect(array_column($items, 'id'))->toBe([1, 2, 3]);

        Http::assertSent(fn (Request $r) => $r->url() === 'https://loja-teste.myshopify.com/admin/api/2024-04/custom_collections.json?limit=250&page_info=CURSOR2');
    });
});
