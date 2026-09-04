<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Billing\ApplicationChargeMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1ApplicationChargeIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1ApplicationCharge(): ApplicationChargeMethods
{
    $integration = shopifyRest1ApplicationChargeIntegration();

    return new ApplicationChargeMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1ApplicationChargeAssertSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify ApplicationChargeMethods', function () {
    it('list: GET /application_charges.json?since_id=1', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ApplicationCharge()->list(['since_id' => 1]))->toBe(['ok' => true]);

        shopifyRest1ApplicationChargeAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/application_charges.json?since_id=1');
    });

    it('get: GET /application_charges/9.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ApplicationCharge()->get(9))->toBe(['ok' => true]);

        shopifyRest1ApplicationChargeAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/application_charges/9.json');
    });

    it('create: POST /application_charges.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ApplicationCharge()->create(['name' => 'Setup', 'price' => 10.0]))->toBe(['ok' => true]);

        shopifyRest1ApplicationChargeAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/application_charges.json', ['application_charge' => ['name' => 'Setup', 'price' => 10.0]]);
    });

    it('listCredits: GET /application_credits.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ApplicationCharge()->listCredits())->toBe(['ok' => true]);

        shopifyRest1ApplicationChargeAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/application_credits.json');
    });

    it('getCredit: GET /application_credits/4.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ApplicationCharge()->getCredit(4))->toBe(['ok' => true]);

        shopifyRest1ApplicationChargeAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/application_credits/4.json');
    });
});
