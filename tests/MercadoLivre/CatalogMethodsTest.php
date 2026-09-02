<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Catalog\CatalogMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function catalogMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function catalogMethods(): CatalogMethods
{
    $integration = catalogMethodsIntegration();

    return new CatalogMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * @param  array<int,string>  $urlParts
 * @param  array<int,string>  $bodyParts
 * @param  array<string,string>  $headers
 */
function catalogMethodsAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
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
    Http::fake(['api.mercadolibre.com/*' => Http::response(['ok' => true], 200)]);
});

describe('CatalogMethods — buscador de produtos', function () {
    it('searchProducts GET /products/search com site_id + filtros', function () {
        catalogMethods()->searchProducts(['q' => 'whey 900g', 'status' => 'active', 'listing_strategy' => 'catalog_required']);
        catalogMethodsAssertSent('GET', ['/products/search', 'site_id=MLB', 'q=whey%20900g', 'status=active', 'listing_strategy=catalog_required']);
    });

    it('searchProductsByAttributes POST /products/search com domain_id/site_id/status/attributes', function () {
        catalogMethods()->searchProductsByAttributes('MLB-CELLPHONES', [['id' => 'BRAND', 'value_id' => '206']]);
        catalogMethodsAssertSent('POST', ['/products/search'], [
            '"domain_id":"MLB-CELLPHONES"', '"site_id":"MLB"', '"status":"active"', '"attributes":[{"id":"BRAND","value_id":"206"}]',
        ]);
    });

    it('product GET /products/{id}', function () {
        catalogMethods()->product('MLB14719808');
        catalogMethodsAssertSent('GET', ['/products/MLB14719808']);
    });

    it('domainCategories GET /catalog_domains/{domain}/categories', function () {
        catalogMethods()->domainCategories('MLB-CELLPHONES');
        catalogMethodsAssertSent('GET', ['/catalog_domains/MLB-CELLPHONES/categories']);
    });
});

describe('CatalogMethods — fichas técnicas', function () {
    it('technicalSpecs GET /domains/{id}/technical_specs?channel_id', function () {
        catalogMethods()->technicalSpecs('MLB-CELLPHONES', ['channel_id' => 'catalog_suggestions']);
        catalogMethodsAssertSent('GET', ['/domains/MLB-CELLPHONES/technical_specs?', 'channel_id=catalog_suggestions']);
    });

    it('technicalSpecsInput GET /domains/{id}/technical_specs/input', function () {
        catalogMethods()->technicalSpecsInput('MLB-CELLPHONES');
        catalogMethodsAssertSent('GET', ['/domains/MLB-CELLPHONES/technical_specs/input']);
    });

    it('technicalSpecsOutput GET /domains/{id}/technical_specs/output', function () {
        catalogMethods()->technicalSpecsOutput('MLB-CELLPHONES');
        catalogMethodsAssertSent('GET', ['/domains/MLB-CELLPHONES/technical_specs/output']);
    });

    it('technicalSpecsFor POST /domains/{id}/technical_specs?section=grids com attributes', function () {
        catalogMethods()->technicalSpecsFor('MLB-SNEAKERS', [['id' => 'BRAND', 'value_id' => '14671']]);
        catalogMethodsAssertSent('POST', ['/domains/MLB-SNEAKERS/technical_specs', 'section=grids'], ['"attributes":[{"id":"BRAND","value_id":"14671"}]']);
    });
});

describe('CatalogMethods — Brand Central', function () {
    it('suggestionsQuota GET /catalog_suggestions/users/{id}/quota', function () {
        catalogMethods()->suggestionsQuota(123456);
        catalogMethodsAssertSent('GET', ['/catalog_suggestions/users/123456/quota']);
    });

    it('suggestionAvailableDomains GET /catalog_suggestions/domains/{site}/available/full', function () {
        catalogMethods()->suggestionAvailableDomains();
        catalogMethodsAssertSent('GET', ['/catalog_suggestions/domains/MLB/available/full']);
    });

    it('createSuggestion POST /catalog_suggestions', function () {
        catalogMethods()->createSuggestion(['title' => 'Sugestao', 'domain_id' => 'MLB-CELLPHONES']);
        catalogMethodsAssertSent('POST', ['/catalog_suggestions'], ['"title":"Sugestao"', '"domain_id":"MLB-CELLPHONES"']);
    });

    it('suggestion GET /catalog_suggestions/{id}', function () {
        catalogMethods()->suggestion(123456);
        catalogMethodsAssertSent('GET', ['/catalog_suggestions/123456']);
    });

    it('updateSuggestion PUT /catalog_suggestions/{id}', function () {
        catalogMethods()->updateSuggestion(123456, ['title' => 'Novo']);
        catalogMethodsAssertSent('PUT', ['/catalog_suggestions/123456'], ['"title":"Novo"']);
    });

    it('setSuggestionDescription POST /catalog_suggestions/{id}/description', function () {
        catalogMethods()->setSuggestionDescription(123456, 'Texto');
        catalogMethodsAssertSent('POST', ['/catalog_suggestions/123456/description'], ['"plain_text":"Texto"']);
    });

    it('updateSuggestionDescription PUT /catalog_suggestions/{id}/description', function () {
        catalogMethods()->updateSuggestionDescription(123456, 'Texto 2');
        catalogMethodsAssertSent('PUT', ['/catalog_suggestions/123456/description'], ['"plain_text":"Texto 2"']);
    });

    it('suggestionValidations GET /catalog_suggestions/{id}/validations', function () {
        catalogMethods()->suggestionValidations(123456);
        catalogMethodsAssertSent('GET', ['/catalog_suggestions/123456/validations']);
    });

    it('searchUserSuggestions GET /catalog_suggestions/users/{id}/suggestions/search', function () {
        catalogMethods()->searchUserSuggestions(7724, ['status' => 'UNDER_REVIEW']);
        catalogMethodsAssertSent('GET', ['/catalog_suggestions/users/7724/suggestions/search', 'status=UNDER_REVIEW']);
    });
});

describe('CatalogMethods — buybox sync', function () {
    it('buyboxSyncStatus GET /public/buybox/sync/{item} com x-public', function () {
        catalogMethods()->buyboxSyncStatus('MLB1');
        catalogMethodsAssertSent('GET', ['/public/buybox/sync/MLB1'], [], ['x-public' => 'True']);
    });

    it('buyboxSync POST /public/buybox/sync body {id} com x-public', function () {
        catalogMethods()->buyboxSync('MLB1');
        catalogMethodsAssertSent('POST', ['/public/buybox/sync'], ['"id":"MLB1"'], ['x-public' => 'True']);
    });
});

describe('CatalogMethods — tabelas de medidas', function () {
    it('activeChartDomains GET /catalog/charts/{site}/configurations/active_domains', function () {
        catalogMethods()->activeChartDomains('MLA');
        catalogMethodsAssertSent('GET', ['/catalog/charts/MLA/configurations/active_domains']);
    });

    it('searchChartDomains POST /catalog/charts/domains/search', function () {
        catalogMethods()->searchChartDomains('BRAND');
        catalogMethodsAssertSent('POST', ['/catalog/charts/domains/search'], ['"site_id":"MLB"', '"type":"BRAND"']);
    });

    it('searchCharts POST /catalog/charts/search?offset&limit com x-caller-id do seller_id', function () {
        catalogMethods()->searchCharts(['domain_id' => 'SNEAKERS', 'site_id' => 'MLB', 'seller_id' => 123456], offset: 1, limit: 50);
        catalogMethodsAssertSent(
            'POST',
            ['/catalog/charts/search', 'offset=1', 'limit=50'],
            ['"domain_id":"SNEAKERS"', '"seller_id":123456'],
            ['x-caller-id' => '123456']
        );
    });

    it('createChart POST /catalog/charts', function () {
        catalogMethods()->createChart(['names' => ['MLB' => 'Tabela'], 'domain_id' => 'PANTS', 'site_id' => 'MLB']);
        catalogMethodsAssertSent('POST', ['/catalog/charts'], ['"names":{"MLB":"Tabela"}', '"domain_id":"PANTS"']);
    });

    it('chart GET /catalog/charts/{id}', function () {
        catalogMethods()->chart(232382);
        catalogMethodsAssertSent('GET', ['/catalog/charts/232382']);
    });

    it('updateChart PUT /catalog/charts/{id}', function () {
        catalogMethods()->updateChart(5, ['names' => ['MLB' => 'Novo nome']]);
        catalogMethodsAssertSent('PUT', ['/catalog/charts/5'], ['"names":{"MLB":"Novo nome"}']);
    });

    it('deleteChart DELETE /catalog/charts/{id}', function () {
        catalogMethods()->deleteChart(124125);
        catalogMethodsAssertSent('DELETE', ['/catalog/charts/124125']);
    });

    it('addChartRow POST /catalog/charts/{id}/rows', function () {
        catalogMethods()->addChartRow(4, ['attributes' => [['id' => 'AR_SIZE', 'values' => [['name' => '43 AR']]]]]);
        catalogMethodsAssertSent('POST', ['/catalog/charts/4/rows'], ['"attributes":[{"id":"AR_SIZE","values":[{"name":"43 AR"}]}]']);
    });

    it('updateChartRow PUT /catalog/charts/{chart}/rows/{row}', function () {
        catalogMethods()->updateChartRow(4, 77, ['attributes' => []]);
        catalogMethodsAssertSent('PUT', ['/catalog/charts/4/rows/77'], ['"attributes":[]']);
    });
});
