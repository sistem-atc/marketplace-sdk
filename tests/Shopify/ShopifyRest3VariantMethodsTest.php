<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\VariantMethods;

function shopifyRest3VariantMethods(): SistemAtc\Marketplaces\Shopify\Endpoints\Product\VariantMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shpat_fake',
        refreshToken: null,
        settings: ['shop_domain' => 'test-store.myshopify.com'],
        active: true,
        expired: false,
    );

    return new SistemAtc\Marketplaces\Shopify\Endpoints\Product\VariantMethods(HttpClientFactory::make($integration), $integration);
}

/** Afirma verbo + URL completa + header do token (+ body, quando informado). */
function shopifyRest3VariantSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify VariantMethods (REST chunk 3)', function () use ($base) {
    it('list -> GET products/{id}/variants.json com query', function () use ($base) {
        Http::fake(['*' => Http::response(['variants' => [['id' => 1]]])]);
        $r = shopifyRest3VariantMethods()->list(10, ['limit' => 5]);
        expect($r['variants'][0]['id'])->toBe(1);
        shopifyRest3VariantSent('GET', "$base/products/10/variants.json?limit=5");
    });

    it('count -> GET products/{id}/variants/count.json', function () use ($base) {
        Http::fake(['*' => Http::response(['count' => 3])]);
        expect(shopifyRest3VariantMethods()->count(10))->toBe(3);
        shopifyRest3VariantSent('GET', "$base/products/10/variants/count.json");
    });

    it('get -> GET variants/{id}.json', function () use ($base) {
        Http::fake(['*' => Http::response(['variant' => ['id' => 7]])]);
        shopifyRest3VariantMethods()->get(7);
        shopifyRest3VariantSent('GET', "$base/variants/7.json");
    });

    it('create -> POST products/{id}/variants.json embrulhado em variant', function () use ($base) {
        Http::fake(['*' => Http::response(['variant' => ['id' => 8]], 201)]);
        shopifyRest3VariantMethods()->create(10, ['option1' => 'Baunilha', 'price' => '99.90']);
        shopifyRest3VariantSent('POST', "$base/products/10/variants.json", ['variant' => ['option1' => 'Baunilha', 'price' => '99.90']]);
    });

    it('update -> PUT variants/{id}.json embrulhado em variant', function () use ($base) {
        Http::fake(['*' => Http::response(['variant' => ['id' => 8]])]);
        shopifyRest3VariantMethods()->update(8, ['price' => '89.90']);
        shopifyRest3VariantSent('PUT', "$base/variants/8.json", ['variant' => ['price' => '89.90']]);
    });

    it('delete -> DELETE products/{pid}/variants/{id}.json', function () use ($base) {
        Http::fake(['*' => Http::response([])]);
        shopifyRest3VariantMethods()->delete(10, 8);
        shopifyRest3VariantSent('DELETE', "$base/products/10/variants/8.json");
    });

    it('each segue o Link header ate esgotar', function () use ($base) {
        Http::fake(['*' => Http::sequence()
            ->push(['variants' => [['id' => 1], ['id' => 2]]], 200, ['Link' => "<$base/products/10/variants.json?limit=250&page_info=CUR2>; rel=\"next\""])
            ->push(['variants' => [['id' => 3]]], 200, [])]);
        $ids = array_column(iterator_to_array(shopifyRest3VariantMethods()->each(10)), 'id');
        expect($ids)->toBe([1, 2, 3]);
        shopifyRest3VariantSent('GET', "$base/products/10/variants.json?limit=250&page_info=CUR2");
    });
});
