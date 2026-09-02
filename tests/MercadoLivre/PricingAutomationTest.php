<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Pricing\PricingAutomationMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function pricingAutoIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function pricingAutoMethods(): PricingAutomationMethods
{
    $integration = pricingAutoIntegration();

    return new PricingAutomationMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL (todos os pedaços) + Bearer + body/headers da única
 * requisição disparada.
 *
 * @param  array<int,string>  $urlParts
 * @param  array<int,string>  $bodyParts
 * @param  array<string,string>  $headers
 */
function pricingAutoAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
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

describe('PricingAutomationMethods', function () {
    it('itemRules: GET /pricing-automation/items/{id}/rules', function () {
        Http::fake(['api.mercadolibre.com/pricing-automation/items/MLB1/rules' => Http::response(['ok' => true])]);

        $result = pricingAutoMethods()->itemRules('MLB1');

        expect($result)->toBe(['ok' => true]);

        pricingAutoAssertSent('GET', ['/pricing-automation/items/MLB1/rules']);
    });

    it('productRules: GET /pricing-automation/products/{id}/rules', function () {
        Http::fake(['api.mercadolibre.com/pricing-automation/products/MLB99/rules' => Http::response(['ok' => true])]);

        $result = pricingAutoMethods()->productRules('MLB99');

        pricingAutoAssertSent('GET', ['/pricing-automation/products/MLB99/rules']);
    });

    it('userItems: GET paginado com limit capado em 100', function () {
        Http::fake(['api.mercadolibre.com/pricing-automation/users/7/items*' => Http::response(['ok' => true])]);

        $result = pricingAutoMethods()->userItems(7, 500, 100);

        pricingAutoAssertSent('GET', ['/pricing-automation/users/7/items', 'limit=100', 'offset=100']);
    });

    it('getAutomation: GET automation', function () {
        Http::fake(['api.mercadolibre.com/pricing-automation/items/MLB1/automation' => Http::response(['ok' => true])]);

        $result = pricingAutoMethods()->getAutomation('MLB1');

        pricingAutoAssertSent('GET', ['/pricing-automation/items/MLB1/automation']);
    });

    it('createAutomation: POST rule_id/min/max', function () {
        Http::fake(['api.mercadolibre.com/pricing-automation/items/MLB1/automation' => Http::response(['ok' => true])]);

        $result = pricingAutoMethods()->createAutomation('MLB1', 'INT_EXT', 100, 1000);

        pricingAutoAssertSent('POST', ['/pricing-automation/items/MLB1/automation'], ['"rule_id":"INT_EXT"', '"min_price":100', '"max_price":1000']);
    });

    it('createAutomationByProduct: POST automation/by-product/{cpid}', function () {
        Http::fake(['api.mercadolibre.com/pricing-automation/items/MLB1/automation/by-product/MLB38' => Http::response(['ok' => true])]);

        $result = pricingAutoMethods()->createAutomationByProduct('MLB1', 'MLB38', 'INT', 1890, 2000);

        pricingAutoAssertSent('POST', ['/pricing-automation/items/MLB1/automation/by-product/MLB38'], ['"rule_id":"INT"', '"min_price":1890']);
    });

    it('updateAutomation: PUT', function () {
        Http::fake(['api.mercadolibre.com/pricing-automation/items/MLB1/automation' => Http::response(['ok' => true])]);

        $result = pricingAutoMethods()->updateAutomation('MLB1', 'INT', 5, 9);

        pricingAutoAssertSent('PUT', ['/pricing-automation/items/MLB1/automation'], ['"rule_id":"INT"', '"max_price":9']);
    });

    it('deleteAutomation: DELETE', function () {
        Http::fake(['api.mercadolibre.com/pricing-automation/items/MLB1/automation' => Http::response([])]);

        $result = pricingAutoMethods()->deleteAutomation('MLB1');

        pricingAutoAssertSent('DELETE', ['/pricing-automation/items/MLB1/automation']);
    });

    it('priceHistory: GET com days/page/size só quando informados', function () {
        Http::fake(['api.mercadolibre.com/pricing-automation/items/MLB1/price/history*' => Http::response(['ok' => true])]);

        $result = pricingAutoMethods()->priceHistory('MLB1', days: 7, size: 50);

        Http::assertNotSent(fn ($req) => str_contains($req->url(), 'page='));

        pricingAutoAssertSent('GET', ['/pricing-automation/items/MLB1/price/history', 'days=7', 'size=50']);
    });

    it('itemsWithPriceReference: GET /suggestions/user/{id}/items', function () {
        Http::fake(['api.mercadolibre.com/suggestions/user/7/items' => Http::response(['total' => 1, 'items' => ['MLB1']])]);

        $result = pricingAutoMethods()->itemsWithPriceReference(7);

        expect($result['items'])->toBe(['MLB1']);

        pricingAutoAssertSent('GET', ['/suggestions/user/7/items']);
    });
});
