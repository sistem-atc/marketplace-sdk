<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\ApplePayCertificateMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyRest1ApplePayCertificateIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_x',
        refreshToken: null,
        settings: ['shop_domain' => 'loja-teste.myshopify.com'],
        active: true,
        expired: false,
    );
}

function shopifyRest1ApplePayCertificate(): ApplePayCertificateMethods
{
    $integration = shopifyRest1ApplePayCertificateIntegration();

    return new ApplePayCertificateMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL completa + header do token (+ body, quando informado).
 */
function shopifyRest1ApplePayCertificateAssertSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify ApplePayCertificateMethods', function () {
    it('get: GET /apple_pay_certificates/3.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ApplePayCertificate()->get(3))->toBe(['ok' => true]);

        shopifyRest1ApplePayCertificateAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/apple_pay_certificates/3.json');
    });

    it('csr: GET /apple_pay_certificates/3/csr.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ApplePayCertificate()->csr(3))->toBe(['ok' => true]);

        shopifyRest1ApplePayCertificateAssertSent('GET', 'https://loja-teste.myshopify.com/admin/api/2024-04/apple_pay_certificates/3/csr.json');
    });

    it('create: POST /apple_pay_certificates.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ApplePayCertificate()->create())->toBe(['ok' => true]);

        shopifyRest1ApplePayCertificateAssertSent('POST', 'https://loja-teste.myshopify.com/admin/api/2024-04/apple_pay_certificates.json', ['apple_pay_certificate' => []]);
    });

    it('update: PUT /apple_pay_certificates/3.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ApplePayCertificate()->update(3, ['status' => 'completed']))->toBe(['ok' => true]);

        shopifyRest1ApplePayCertificateAssertSent('PUT', 'https://loja-teste.myshopify.com/admin/api/2024-04/apple_pay_certificates/3.json', ['apple_pay_certificate' => ['status' => 'completed']]);
    });

    it('delete: DELETE /apple_pay_certificates/3.json', function () {
        Http::fake(['*' => Http::response(['ok' => true], 200)]);

        expect(shopifyRest1ApplePayCertificate()->delete(3))->toBe(['ok' => true]);

        shopifyRest1ApplePayCertificateAssertSent('DELETE', 'https://loja-teste.myshopify.com/admin/api/2024-04/apple_pay_certificates/3.json');
    });
});
