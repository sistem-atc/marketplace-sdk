<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\Shop\ShopMethods;

function shopifyRest3ShopMethods(): SistemAtc\Marketplaces\Shopify\Endpoints\Shop\ShopMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shpat_fake',
        refreshToken: null,
        settings: ['shop_domain' => 'test-store.myshopify.com'],
        active: true,
        expired: false,
    );

    return new SistemAtc\Marketplaces\Shopify\Endpoints\Shop\ShopMethods(HttpClientFactory::make($integration), $integration);
}

/** Afirma verbo + URL completa + header do token (+ body, quando informado). */
function shopifyRest3ShopSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify ShopMethods (REST chunk 3)', function () use ($base) {
    it('get -> GET shop.json', function () use ($base) {
        Http::fake(['*' => Http::response(['shop' => ['id' => 1]])]);
        expect(shopifyRest3ShopMethods()->get()['shop']['id'])->toBe(1);
        shopifyRest3ShopSent('GET', "$base/shop.json");
    });

    it('policies -> GET policies.json', function () use ($base) {
        Http::fake(['*' => Http::response(['policies' => []])]);
        shopifyRest3ShopMethods()->policies();
        shopifyRest3ShopSent('GET', "$base/policies.json");
    });

    it('shippingZones -> GET shipping_zones.json', function () use ($base) {
        Http::fake(['*' => Http::response(['shipping_zones' => []])]);
        shopifyRest3ShopMethods()->shippingZones();
        shopifyRest3ShopSent('GET', "$base/shipping_zones.json");
    });

    it('listProvinces / countProvinces / getProvince / updateProvince', function () use ($base) {
        Http::fake(['*' => Http::sequence()
            ->push(['provinces' => []])
            ->push(['count' => 27])
            ->push(['province' => ['id' => 2]])
            ->push(['province' => ['id' => 2]])]);
        $m = shopifyRest3ShopMethods();
        $m->listProvinces(55);
        expect($m->countProvinces(55))->toBe(27);
        $m->getProvince(55, 2);
        $m->updateProvince(55, 2, ['tax' => 0.18]);
        shopifyRest3ShopSent('GET', "$base/countries/55/provinces.json");
        shopifyRest3ShopSent('GET', "$base/countries/55/provinces/count.json");
        shopifyRest3ShopSent('GET', "$base/countries/55/provinces/2.json");
        shopifyRest3ShopSent('PUT', "$base/countries/55/provinces/2.json", ['province' => ['tax' => 0.18]]);
    });

    it('listUsers / getUser / currentUser', function () use ($base) {
        Http::fake(['*' => Http::response(['users' => [], 'user' => ['id' => 3]])]);
        $m = shopifyRest3ShopMethods();
        $m->listUsers();
        $m->getUser(3);
        $m->currentUser();
        shopifyRest3ShopSent('GET', "$base/users.json");
        shopifyRest3ShopSent('GET', "$base/users/3.json");
        shopifyRest3ShopSent('GET', "$base/users/current.json");
    });

    it('storefront access tokens: list / create (embrulhado) / delete', function () use ($base) {
        Http::fake(['*' => Http::response(['storefront_access_tokens' => [], 'storefront_access_token' => ['id' => 4]])]);
        $m = shopifyRest3ShopMethods();
        $m->listStorefrontAccessTokens();
        $m->createStorefrontAccessToken(['title' => 'Bunker']);
        $m->deleteStorefrontAccessToken(4);
        shopifyRest3ShopSent('GET', "$base/storefront_access_tokens.json");
        shopifyRest3ShopSent('POST', "$base/storefront_access_tokens.json", ['storefront_access_token' => ['title' => 'Bunker']]);
        shopifyRest3ShopSent('DELETE', "$base/storefront_access_tokens/4.json");
    });
});
