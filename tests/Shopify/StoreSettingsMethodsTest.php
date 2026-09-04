<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Store\StoreSettingsMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1StoreSettingsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1StoreSettings(): StoreSettingsMethods
{
    $integration = shopifyRest1StoreSettingsIntegration();

    return new StoreSettingsMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1StoreSettingsAssertSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify StoreSettingsMethods', function () {
    it('accessScopes: GET https://loja-teste.myshopify.com/admin/oauth/access_scopes.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1StoreSettings()->accessScopes())->toBe(['ok' => true]);

        shopifyRest1StoreSettingsAssertSent('GET', 'https://loja-teste.myshopify.com/admin/oauth/access_scopes.json');
    });

    it('currencies: GET /currencies.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1StoreSettings()->currencies())->toBe(['ok' => true]);

        shopifyRest1StoreSettingsAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/currencies.json');
    });

    it('deprecatedApiCalls: GET /deprecated_api_calls.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1StoreSettings()->deprecatedApiCalls())->toBe(['ok' => true]);

        shopifyRest1StoreSettingsAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/deprecated_api_calls.json');
    });

    it('listCountries: GET /countries.json?since_id=5', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1StoreSettings()->listCountries(['since_id' => 5]))->toBe(['ok' => true]);

        shopifyRest1StoreSettingsAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/countries.json?since_id=5');
    });

    it('countCountries: GET /countries/count.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1StoreSettings()->countCountries())->toBe(['ok' => true]);

        shopifyRest1StoreSettingsAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/countries/count.json');
    });

    it('getCountry: GET /countries/7.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1StoreSettings()->getCountry(7))->toBe(['ok' => true]);

        shopifyRest1StoreSettingsAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/countries/7.json');
    });

    it('createCountry: POST /countries.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1StoreSettings()->createCountry(['code' => 'BR']))->toBe(['ok' => true]);

        shopifyRest1StoreSettingsAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/countries.json', ['country' => ['code' => 'BR']]);
    });

    it('updateCountry: PUT /countries/7.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1StoreSettings()->updateCountry(7, ['tax' => 0.1]))->toBe(['ok' => true]);

        shopifyRest1StoreSettingsAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/countries/7.json', ['country' => ['tax' => 0.1]]);
    });

    it('deleteCountry: DELETE /countries/7.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1StoreSettings()->deleteCountry(7))->toBe(['ok' => true]);

        shopifyRest1StoreSettingsAssertSent('DELETE', 'https://loja-teste.myshopify.com/admin/api/2024-04/countries/7.json');
    });
});
