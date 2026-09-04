<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Shipping\CarrierServiceMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1CarrierServiceIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1CarrierService(): CarrierServiceMethods
{
    $integration = shopifyRest1CarrierServiceIntegration();

    return new CarrierServiceMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1CarrierServiceAssertSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify CarrierServiceMethods', function () {
    it('list: GET /carrier_services.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1CarrierService()->list())->toBe(['ok' => true]);

        shopifyRest1CarrierServiceAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/carrier_services.json');
    });

    it('get: GET /carrier_services/2.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1CarrierService()->get(2))->toBe(['ok' => true]);

        shopifyRest1CarrierServiceAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/carrier_services/2.json');
    });

    it('create: POST /carrier_services.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1CarrierService()->create(['name' => 'Frete X', 'callback_url' => 'https://x/y']))->toBe(['ok' => true]);

        shopifyRest1CarrierServiceAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/carrier_services.json', ['carrier_service' => ['name' => 'Frete X', 'callback_url' => 'https://x/y']]);
    });

    it('update: PUT /carrier_services/2.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1CarrierService()->update(2, ['active' => false]))->toBe(['ok' => true]);

        shopifyRest1CarrierServiceAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/carrier_services/2.json', ['carrier_service' => ['active' => false]]);
    });

    it('delete: DELETE /carrier_services/2.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1CarrierService()->delete(2))->toBe(['ok' => true]);

        shopifyRest1CarrierServiceAssertSent('DELETE', 'https://loja-teste.myshopify.com/admin/api/2024-04/carrier_services/2.json');
    });
});
