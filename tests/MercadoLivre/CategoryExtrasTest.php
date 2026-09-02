<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Category\CategoryMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function categoryExtrasIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function categoryExtrasMethods(): CategoryMethods
{
    $integration = categoryExtrasIntegration();

    return new CategoryMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL (todos os pedaços) + Bearer + body/headers da única
 * requisição disparada.
 *
 * @param  array<int,string>  $urlParts
 * @param  array<int,string>  $bodyParts
 * @param  array<string,string>  $headers
 */
function categoryExtrasAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
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

describe('CategoryMethods — atributos e ficha técnica', function () {
    it('conditionalAttributes: POST /categories/{id}/attributes/conditional com o item', function () {
        Http::fake(['api.mercadolibre.com/categories/MLB1/attributes/conditional' => Http::response([])]);

        $result = categoryExtrasMethods()->conditionalAttributes('MLB1', ['title' => 'Cerveja', 'price' => 900]);

        categoryExtrasAssertSent('POST', ['/categories/MLB1/attributes/conditional'], ['"title":"Cerveja"']);
    });

    it('technicalSpecsInput: GET /categories/{id}/technical_specs/input', function () {
        Http::fake(['api.mercadolibre.com/categories/MLB1/technical_specs/input' => Http::response(['ok' => true])]);

        $result = categoryExtrasMethods()->technicalSpecsInput('MLB1');

        expect($result)->toBe(['ok' => true]);

        categoryExtrasAssertSent('GET', ['/categories/MLB1/technical_specs/input']);
    });

    it('technicalSpecsOutput: GET /categories/{id}/technical_specs/output', function () {
        Http::fake(['api.mercadolibre.com/categories/MLB1/technical_specs/output' => Http::response(['ok' => true])]);

        $result = categoryExtrasMethods()->technicalSpecsOutput('MLB1');

        categoryExtrasAssertSent('GET', ['/categories/MLB1/technical_specs/output']);
    });

    it('saleTerms: GET /categories/{id}/sale_terms', function () {
        Http::fake(['api.mercadolibre.com/categories/MLB1/sale_terms' => Http::response([['id' => 'WARRANTY_TYPE']])]);

        $result = categoryExtrasMethods()->saleTerms('MLB1');

        expect($result[0]['id'])->toBe('WARRANTY_TYPE');

        categoryExtrasAssertSent('GET', ['/categories/MLB1/sale_terms']);
    });

    it('attributeTopValues: POST sem body quando não há known_attributes', function () {
        Http::fake(['api.mercadolibre.com/catalog_domains/MLB-CELLPHONES/attributes/BRAND/top_values' => Http::response([])]);

        $result = categoryExtrasMethods()->attributeTopValues('MLB-CELLPHONES', 'BRAND');

        Http::assertSent(fn ($req) => ! str_contains($req->body(), 'known_attributes'));

        categoryExtrasAssertSent('POST', ['/catalog_domains/MLB-CELLPHONES/attributes/BRAND/top_values']);
    });

    it('attributeTopValues: POST com known_attributes', function () {
        Http::fake(['api.mercadolibre.com/catalog_domains/MLB-CELLPHONES/attributes/MODEL/top_values' => Http::response([])]);

        $result = categoryExtrasMethods()->attributeTopValues('MLB-CELLPHONES', 'MODEL', [['id' => 'BRAND', 'value_id' => '206']]);

        categoryExtrasAssertSent('POST', ['/catalog_domains/MLB-CELLPHONES/attributes/MODEL/top_values'], ['"known_attributes":[', '"value_id":"206"']);
    });
});
