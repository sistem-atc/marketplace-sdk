<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Site\SiteMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function siteMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function siteMethodsMethods(): SiteMethods
{
    $integration = siteMethodsIntegration();

    return new SiteMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL (todos os pedaços) + Bearer + body/headers da única
 * requisição disparada.
 *
 * @param  array<int,string>  $urlParts
 * @param  array<int,string>  $bodyParts
 * @param  array<string,string>  $headers
 */
function siteMethodsAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
{
    Http::assertSent(function ($req) use ($method, $urlParts, $bodyParts, $headers) {
        if ($req->method() !== $method || $req->header('Authorization')[0] !== 'Bearer ml-bearer') {
            return false;
        }
        foreach ($urlParts as $part) {
            if (! str_contains($req->url(), $part)) {
                return false;
            }
        }
        foreach ($bodyParts as $part) {
            if (! str_contains($req->body(), $part)) {
                return false;
            }
        }
        foreach ($headers as $name => $value) {
            if (($req->header($name)[0] ?? null) !== $value) {
                return false;
            }
        }

        return true;
    });
}

beforeEach(function () {
    config([
        'marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.access_token_ttl_seconds' => 21600,
        'mercadolivre.default_site_id' => 'MLB',
    ]);
    Http::preventStrayRequests();
});

describe('SiteMethods', function () {
    it('sites: GET /sites', function () {
        Http::fake(['api.mercadolibre.com/sites' => Http::response([['id' => 'MLB']])]);

        $result = siteMethodsMethods()->sites();

        expect($result)->toBe([['id' => 'MLB']]);

        siteMethodsAssertSent('GET', ['/sites']);
    });

    it('categoriesDump: GET /sites/{id}/categories/all sem query por padrão', function () {
        Http::fake(['api.mercadolibre.com/sites/MLB/categories/all' => Http::response(['ok' => true])]);

        $result = siteMethodsMethods()->categoriesDump('MLB');

        Http::assertNotSent(fn ($req) => str_contains($req->url(), 'withAttributes'));

        siteMethodsAssertSent('GET', ['/sites/MLB/categories/all']);
    });

    it('categoriesDump: withAttributes=true', function () {
        Http::fake(['api.mercadolibre.com/sites/MLA/categories/all*' => Http::response(['ok' => true])]);

        $result = siteMethodsMethods()->categoriesDump('MLA', true);

        siteMethodsAssertSent('GET', ['/sites/MLA/categories/all', 'withAttributes=true']);
    });

    it('listingPrices: GET /sites/{id}/listing_prices com price + extras', function () {
        Http::fake(['api.mercadolibre.com/sites/MLB/listing_prices*' => Http::response([])]);

        $result = siteMethodsMethods()->listingPrices('MLB', 99.9, ['listing_type_id' => 'gold_special', 'category_id' => 'MLB1234', 'logistic_type' => 'drop_off']);

        siteMethodsAssertSent('GET', ['/sites/MLB/listing_prices', 'price=99.9', 'listing_type_id=gold_special', 'category_id=MLB1234', 'logistic_type=drop_off']);
    });

    it('search: GET /sites/{id}/search com filtros', function () {
        Http::fake(['api.mercadolibre.com/sites/MLB/search*' => Http::response(['ok' => true])]);

        $result = siteMethodsMethods()->search('MLB', ['seller_id' => 77, 'sort' => 'price_asc']);

        siteMethodsAssertSent('GET', ['/sites/MLB/search', 'seller_id=77', 'sort=price_asc']);
    });

    it('paymentMethods: GET /sites/{id}/payment_methods', function () {
        Http::fake(['api.mercadolibre.com/sites/MLB/payment_methods' => Http::response([])]);

        $result = siteMethodsMethods()->paymentMethods('MLB');

        siteMethodsAssertSent('GET', ['/sites/MLB/payment_methods']);
    });

    it('paymentMethod: GET /sites/{id}/payment_methods/{pm}', function () {
        Http::fake(['api.mercadolibre.com/sites/MLB/payment_methods/amex' => Http::response(['ok' => true])]);

        $result = siteMethodsMethods()->paymentMethod('MLB', 'amex');

        siteMethodsAssertSent('GET', ['/sites/MLB/payment_methods/amex']);
    });

    it('currencies: GET /currencies', function () {
        Http::fake(['api.mercadolibre.com/currencies' => Http::response([])]);

        $result = siteMethodsMethods()->currencies();

        siteMethodsAssertSent('GET', ['/currencies']);
    });

    it('currency: GET /currencies/{id}', function () {
        Http::fake(['api.mercadolibre.com/currencies/BRL' => Http::response(['ok' => true])]);

        $result = siteMethodsMethods()->currency('BRL');

        siteMethodsAssertSent('GET', ['/currencies/BRL']);
    });

    it('zipCode: GET /countries/{cc}/zip_codes/{zip}', function () {
        Http::fake(['api.mercadolibre.com/countries/BR/zip_codes/01310100' => Http::response(['ok' => true])]);

        $result = siteMethodsMethods()->zipCode('BR', '01310100');

        siteMethodsAssertSent('GET', ['/countries/BR/zip_codes/01310100']);
    });

    it('zipCodesBetween: GET /country/{cc}/zip_codes/search_between', function () {
        Http::fake(['api.mercadolibre.com/country/AR/zip_codes/search_between*' => Http::response(['ok' => true])]);

        $result = siteMethodsMethods()->zipCodesBetween('AR', '5000', '5100');

        siteMethodsAssertSent('GET', ['/country/AR/zip_codes/search_between', 'zip_code_from=5000', 'zip_code_to=5100']);
    });

    it('notices: GET /communications/notices?limit&offset', function () {
        Http::fake(['api.mercadolibre.com/communications/notices*' => Http::response(['ok' => true])]);

        $result = siteMethodsMethods()->notices(20, 40);

        siteMethodsAssertSent('GET', ['/communications/notices', 'limit=20', 'offset=40']);
    });

    it('usersMultiGet: GET /users?ids=a,b', function () {
        Http::fake(['api.mercadolibre.com/users?*' => Http::response([])]);

        $result = siteMethodsMethods()->usersMultiGet([401114259, 287440999]);

        siteMethodsAssertSent('GET', ['/users?', 'ids=401114259%2C287440999']);
    });

    it('blockedUsers: GET /block-api/search/users/{id}?type', function () {
        Http::fake(['api.mercadolibre.com/block-api/search/users/77*' => Http::response(['ok' => true])]);

        $result = siteMethodsMethods()->blockedUsers(77);

        siteMethodsAssertSent('GET', ['/block-api/search/users/77', 'type=blocked_by_order']);
    });

    it('createTestUser: POST /users/test_user com site_id', function () {
        Http::fake(['api.mercadolibre.com/users/test_user' => Http::response(['id' => 1, 'nickname' => 'TEST1', 'password' => 'x'])]);

        $result = siteMethodsMethods()->createTestUser('MLA');

        expect($result['nickname'])->toBe('TEST1');

        siteMethodsAssertSent('POST', ['/users/test_user'], ['"site_id":"MLA"']);
    });
});

describe('SiteMethods — HEAD do dump', function () {
    it('categoriesDumpHeaders: HEAD e devolve X-Content-Created/MD5', function () {
        Http::fake(['api.mercadolibre.com/sites/MLB/categories/all' => Http::response('', 200, ['X-Content-Created' => '2026-01-01T00:00:00Z', 'X-Content-MD5' => 'abc'])]);

        $result = siteMethodsMethods()->categoriesDumpHeaders('MLB');

        expect($result)->toBe(['created' => '2026-01-01T00:00:00Z', 'md5' => 'abc']);
        siteMethodsAssertSent('HEAD', ['/sites/MLB/categories/all']);
    });
});
