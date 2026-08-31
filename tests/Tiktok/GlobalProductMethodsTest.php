<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttGlobalIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: [
            'app_key' => 'ak',
            'app_secret' => 'as',
            'shop_id' => 'shop1',
            'shop_cipher' => 'cipher1',
        ],
        active: true,
        expired: false,
    );
}

function ttGlobalProduct()
{
    return MarketPlaces::Tiktok()->products(ttGlobalIntegration());
}

/** Envelope de sucesso do TikTok em volta do `data` do fixture. */
function ttGlobalOk(string $slug): array
{
    return [
        'code' => 0,
        'message' => 'Success',
        'data' => json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'), true),
    ];
}

describe('MarketPlaces::Tiktok()->product() — produto global', function () {
    it('getGlobalProduct usa GET /product/202309/global_products/{id}', function () {
        Http::fake([
            '*/product/202309/global_products/1729592969712207008*' => Http::response(ttGlobalOk('get-global-product')),
        ]);

        $dto = ttGlobalProduct()->getGlobalProduct('1729592969712207008');

        expect($dto->title)->toBe('Short Boat Invisible Socks');
        Http::assertSent(fn ($r) => $r->method() === 'GET'
            && str_contains($r->url(), '/product/202309/global_products/1729592969712207008'));
    });

    it('searchGlobalProducts manda page_size na QUERY (a API recusa no body) e usa a versao 202312', function () {
        Http::fake(['*/global_products/search*' => Http::response(ttGlobalOk('search-global-products'))]);

        $dto = ttGlobalProduct()->searchGlobalProducts(['status' => 'PUBLISHED'], pageSize: 50, pageToken: 'TOK');

        expect($dto->globalProducts)->toHaveCount(1);
        Http::assertSent(fn ($r) => $r->method() === 'POST'
            && str_contains($r->url(), '/product/202312/global_products/search')
            && str_contains($r->url(), 'page_size=50')
            && str_contains($r->url(), 'page_token=TOK')
            && str_contains($r->body(), '"status":"PUBLISHED"')
            && ! str_contains($r->body(), 'page_size'));
    });

    it('deleteGlobalProducts usa o verbo DELETE com corpo (nao POST)', function () {
        Http::fake(['*/global_products*' => Http::response(ttGlobalOk('delete-global-products'))]);

        $dto = ttGlobalProduct()->deleteGlobalProducts(['A', 'B']);

        expect($dto->errors)->toHaveCount(1);
        Http::assertSent(fn ($r) => $r->method() === 'DELETE'
            && str_contains($r->body(), '"global_product_ids":["A","B"]'));
    });

    it('partialEditGlobalProduct usa a versao 202509, diferente da edicao completa', function () {
        Http::fake(['*partial_edit*' => Http::response(ttGlobalOk('partial-edit-global-product'))]);

        ttGlobalProduct()->partialEditGlobalProduct('P1', ['title' => 'Novo']);

        Http::assertSent(fn ($r) => str_contains($r->url(), '/product/202509/global_products/P1/partial_edit'));
    });

    it('updateGlobalInventory embrulha os SKUs em global_skus', function () {
        Http::fake(['*inventory/update*' => Http::response(['code' => 0, 'data' => []])]);

        ttGlobalProduct()->updateGlobalInventory('P1', [
            ['id' => 'S1', 'inventory' => [['global_warehouse_id' => 'W1', 'quantity' => 7]]],
        ]);

        Http::assertSent(fn ($r) => str_contains($r->url(), '/product/202309/global_products/P1/inventory/update')
            && str_contains($r->body(), '"global_skus"')
            && str_contains($r->body(), '"global_warehouse_id":"W1"'));
    });

    it('getGlobalListingRules usa a versao 202507, fora do 202309 do resto do grupo', function () {
        Http::fake(['*global_listing_rules*' => Http::response(ttGlobalOk('get-global-listing-rules'))]);

        expect(ttGlobalProduct()->getGlobalListingRules()->listingMethods)->toBe('GLOBAL_PUBLISHING');
        Http::assertSent(fn ($r) => str_contains($r->url(), '/product/202507/global_listing_rules'));
    });

    it('getGlobalCategories repassa locale/keyword/category_version na query', function () {
        Http::fake(['*global_categories*' => Http::response(ttGlobalOk('get-global-categories'))]);

        ttGlobalProduct()->getGlobalCategories(['locale' => 'pt-BR', 'category_version' => 'v2']);

        Http::assertSent(fn ($r) => $r->method() === 'GET'
            && str_contains($r->url(), 'locale=pt-BR')
            && str_contains($r->url(), 'category_version=v2'));
    });

    it('getImageTranslationTasks concatena os ids por virgula numa unica query', function () {
        Http::fake(['*translation_tasks*' => Http::response(ttGlobalOk('get-image-translation-tasks'))]);

        ttGlobalProduct()->getImageTranslationTasks(['t1', 't2']);

        Http::assertSent(fn ($r) => $r->method() === 'GET'
            && str_contains($r->url(), '/product/202506/images/translation_tasks')
            && str_contains(urldecode($r->url()), 'translation_task_ids=t1,t2'));
    });

    it('searchManufacturers e searchResponsiblePersons usam a versao 202501 e paginam pela query', function () {
        Http::fake([
            '*manufacturers/search*' => Http::response(ttGlobalOk('search-manufacturers')),
            '*responsible_persons/search*' => Http::response(ttGlobalOk('search-responsible-persons')),
        ]);

        expect(ttGlobalProduct()->searchManufacturers(pageSize: 10)->totalCount)->toBe(26)
            ->and(ttGlobalProduct()->searchResponsiblePersons(pageSize: 10)->responsiblePersons)->toHaveCount(1);

        Http::assertSent(fn ($r) => str_contains($r->url(), '/product/202501/compliance/manufacturers/search')
            && str_contains($r->url(), 'page_size=10'));
    });

    it('uploadProductImage sobe multipart e NAO deixa o corpo entrar na assinatura', function () {
        Http::fake(['*images/upload*' => Http::response(ttGlobalOk('upload-product-image'))]);

        $dto = ttGlobalProduct()->uploadProductImage('conteudo-binario', 'capa.jpg', 'MAIN_IMAGE');

        expect($dto->uri)->toContain('c668cdf70b7f483c94dbe');
        Http::assertSent(function ($r) {
            return $r->method() === 'POST'
                && str_contains($r->url(), '/product/202309/images/upload')
                // o boundary tem que estar no header, senao o TikTok nao parseia:
                && str_contains($r->header('Content-Type')[0] ?? '', 'multipart/form-data; boundary=')
                && str_contains($r->body(), 'capa.jpg')
                && str_contains($r->body(), 'MAIN_IMAGE');
        });
    });

    it('uploadProductFile manda o nome tambem como campo do form (a API exige)', function () {
        Http::fake(['*files/upload*' => Http::response(ttGlobalOk('upload-product-file'))]);

        expect(ttGlobalProduct()->uploadProductFile('%PDF-1.4', 'certificado.pdf')->id)
            ->toBe('v09e40f40000cfu0ovhc77ub7fl97k4w');

        Http::assertSent(fn ($r) => str_contains($r->body(), 'name="name"')
            && str_contains($r->body(), 'certificado.pdf'));
    });

    it('nao contamina as chamadas seguintes com o body multipart do upload', function () {
        Http::fake([
            '*images/upload*' => Http::response(ttGlobalOk('upload-product-image')),
            '*global_products/P1*' => Http::response(ttGlobalOk('get-global-product')),
        ]);

        $product = ttGlobalProduct();
        $product->uploadProductImage('bin', 'a.jpg');
        $product->getGlobalProduct('P1');

        // o segundo request tem que voltar a ser JSON: o attach() muta o
        // PendingRequest, por isso o upload usa um cliente proprio.
        Http::assertSent(fn ($r) => str_contains($r->url(), 'global_products/P1')
            && ! str_contains($r->header('Content-Type')[0] ?? '', 'multipart'));
    });
});
