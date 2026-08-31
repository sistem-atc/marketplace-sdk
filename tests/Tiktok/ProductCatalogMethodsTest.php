<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttCatalogIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: ['app_key' => 'ak', 'app_secret' => 'as', 'shop_id' => 'shop1', 'shop_cipher' => 'cipher1'],
        active: true,
        expired: false,
    );
}

function ttCatalog(): SistemAtc\Marketplaces\Tiktok\Endpoints\Product\ProductMethods
{
    return MarketPlaces::Tiktok()->products(ttCatalogIntegration());
}

describe('MarketPlaces::Tiktok()->products() — catalogo', function () {
    it('searchInventory manda product_ids/sku_ids no BODY e nao pagina', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/product/202309/inventory/search*' => Http::response([
                'code' => 0,
                'data' => ['inventory' => [['product_id' => 'P1', 'skus' => [['id' => 'S1', 'total_available_quantity' => 7]]]]],
            ]),
        ]);

        $resp = ttCatalog()->searchInventory(productIds: ['P1'], skuIds: ['S1']);

        expect($resp->inventory[0]->skus[0]->totalAvailableQuantity)->toBe(7);

        Http::assertSent(function ($req) {
            return $req->method() === 'POST'
                && str_contains($req->body(), '"product_ids":["P1"]')
                && str_contains($req->body(), '"sku_ids":["S1"]')
                && ! str_contains($req->url(), 'page_size');
        });
    });

    it('os GETs de sugestao mandam product_ids na QUERY separados por virgula', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/product/202405/products/seo_words*' => Http::response([
                'code' => 0, 'data' => ['products' => [['id' => '1', 'seo_words' => [['text' => 'whey']]]]],
            ]),
        ]);

        expect(ttCatalog()->getSeoWords(['1', '2'])->products[0]->seoWords[0]->text)->toBe('whey');

        // Body nao serve aqui: o endpoint e' GET e le' so' a query string.
        Http::assertSent(fn ($req) => $req->method() === 'GET' && str_contains(urldecode($req->url()), 'product_ids=1,2'));
    });

    it('listOpportunities desembrulha as DUAS camadas de data', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/product/202604/opportunities/query*' => Http::response([
                'code' => 0,
                // A API repete a chave `data` dentro de `data` — sem desembrulhar
                // as duas, o DTO vinha inteiro nulo e ninguem percebia.
                'data' => ['data' => ['opportunities' => [['id' => 'O1', 'title' => 'Creatina']], 'total_count' => 1]],
            ]),
        ]);

        $resp = ttCatalog()->listOpportunities('PRODUCT', ['tag_codes' => ['CORE_PRODUCT']], pageSize: 50);

        expect($resp->opportunities[0]->id)->toBe('O1')
            ->and($resp->totalCount)->toBe(1);

        Http::assertSent(function ($req) {
            return str_contains($req->url(), 'page_size=50')            // paginacao na QUERY
                && str_contains($req->body(), '"opportunity_type":"PRODUCT"'); // filtro no BODY
        });
    });

    it('activate/deactivate/recover batem no mesmo shape e recover nao manda listing_platforms', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/product/202309/products/*' => Http::response(['code' => 0, 'data' => []]),
        ]);

        expect(ttCatalog()->activate(['P1'], ['TIKTOK_SHOP'])->errors)->toBeNull();
        expect(ttCatalog()->recover(['P1'])->errors)->toBeNull();

        Http::assertSent(fn ($req) => str_contains($req->url(), '/products/activate')
            ? str_contains($req->body(), '"listing_platforms":["TIKTOK_SHOP"]')
            : true);
        Http::assertSent(fn ($req) => str_contains($req->url(), '/products/recover')
            ? ! str_contains($req->body(), 'listing_platforms')
            : true);
    });

    it('listSkppStatus pagina por page_no no BODY, nao por page_token', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/product/202606/skpps/search*' => Http::response([
                'code' => 0, 'data' => ['products' => [], 'total_count' => 0],
            ]),
        ]);

        ttCatalog()->listSkppStatus(['P1'], 'QUALIFIED', pageSize: 10, pageNo: 3);

        Http::assertSent(function ($req) {
            return str_contains($req->body(), '"page_no":3')
                && str_contains($req->body(), '"skpp_status":"QUALIFIED"')
                && ! str_contains($req->url(), 'page_token');
        });
    });

    it('submitAppeal nao tem DTO porque a API devolve data vazio', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/product/202604/appeals/submit*' => Http::response([
                'code' => 0, 'message' => 'Success', 'data' => [],
            ]),
        ]);

        // Sucesso aqui e' "nao lancou excecao": nao ha' id de protocolo pra guardar.
        $resp = ttCatalog()->submitAppeal('7298374612938475620', [
            'field_name' => 'APPEAL_MESSAGE_FIELD_SIZE_CHART',
            'indicator_details' => [['indicator_type' => 'REQUIRED_DIMENSION']],
        ]);

        expect($resp['data'])->toBe([]);

        // product_id vai como STRING mesmo a doc declarando int — snowflake de
        // 19 digitos nao cabe com folga em int e o JSON aceita string.
        Http::assertSent(fn ($req) => str_contains($req->body(), '"product_id":"7298374612938475620"'));
    });
});
