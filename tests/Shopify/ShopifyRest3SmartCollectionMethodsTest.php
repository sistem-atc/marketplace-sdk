<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\SmartCollectionMethods;

function shopifyRest3SmartCollectionMethods(): SistemAtc\Marketplaces\Shopify\Endpoints\Product\SmartCollectionMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shpat_fake',
        refreshToken: null,
        settings: ['shop_domain' => 'test-store.myshopify.com'],
        active: true,
        expired: false,
    );

    return new SistemAtc\Marketplaces\Shopify\Endpoints\Product\SmartCollectionMethods(HttpClientFactory::make($integration), $integration);
}

/** Afirma verbo + URL completa + header do token (+ body, quando informado). */
function shopifyRest3SmartCollectionSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify SmartCollectionMethods (REST chunk 3)', function () use ($base) {
    it('list -> GET smart_collections.json com query', function () use ($base) {
        Http::fake(['*' => Http::response(['smart_collections' => []])]);
        shopifyRest3SmartCollectionMethods()->list(['published_status' => 'published']);
        shopifyRest3SmartCollectionSent('GET', "$base/smart_collections.json?published_status=published");
    });

    it('count -> GET smart_collections/count.json', function () use ($base) {
        Http::fake(['*' => Http::response(['count' => 2])]);
        expect(shopifyRest3SmartCollectionMethods()->count())->toBe(2);
        shopifyRest3SmartCollectionSent('GET', "$base/smart_collections/count.json");
    });

    it('get -> GET smart_collections/{id}.json', function () use ($base) {
        Http::fake(['*' => Http::response(['smart_collection' => ['id' => 5]])]);
        shopifyRest3SmartCollectionMethods()->get(5);
        shopifyRest3SmartCollectionSent('GET', "$base/smart_collections/5.json");
    });

    it('create -> POST embrulhado em smart_collection', function () use ($base) {
        Http::fake(['*' => Http::response(['smart_collection' => ['id' => 5]], 201)]);
        $payload = ['title' => 'Whey', 'rules' => [['column' => 'type', 'relation' => 'equals', 'condition' => 'Whey']]];
        shopifyRest3SmartCollectionMethods()->create($payload);
        shopifyRest3SmartCollectionSent('POST', "$base/smart_collections.json", ['smart_collection' => $payload]);
    });

    it('update -> PUT smart_collections/{id}.json embrulhado', function () use ($base) {
        Http::fake(['*' => Http::response(['smart_collection' => ['id' => 5]])]);
        shopifyRest3SmartCollectionMethods()->update(5, ['title' => 'Whey 2']);
        shopifyRest3SmartCollectionSent('PUT', "$base/smart_collections/5.json", ['smart_collection' => ['title' => 'Whey 2']]);
    });

    it('delete -> DELETE smart_collections/{id}.json', function () use ($base) {
        Http::fake(['*' => Http::response([])]);
        shopifyRest3SmartCollectionMethods()->delete(5);
        shopifyRest3SmartCollectionSent('DELETE', "$base/smart_collections/5.json");
    });

    it('order -> PUT smart_collections/{id}/order.json com products[] e sort_order na query', function () use ($base) {
        Http::fake(['*' => Http::response([])]);
        shopifyRest3SmartCollectionMethods()->order(5, [11, 22], 'manual');
        shopifyRest3SmartCollectionSent('PUT', "$base/smart_collections/5/order.json?products%5B0%5D=11&products%5B1%5D=22&sort_order=manual");
    });

    it('each segue o Link header', function () use ($base) {
        Http::fake(['*' => Http::sequence()
            ->push(['smart_collections' => [['id' => 1]]], 200, ['Link' => "<$base/smart_collections.json?limit=250&page_info=X2>; rel=\"next\""])
            ->push(['smart_collections' => [['id' => 2]]], 200, [])]);
        expect(array_column(iterator_to_array(shopifyRest3SmartCollectionMethods()->each()), 'id'))->toBe([1, 2]);
    });
});
