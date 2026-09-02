<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\UserProduct\UserProductMethods;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function userProductMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function userProductMethods(): UserProductMethods
{
    $integration = userProductMethodsIntegration();

    return new UserProductMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * @param  array<int,string>  $urlParts
 * @param  array<int,string>  $bodyParts
 * @param  array<string,string>  $headers
 */
function userProductMethodsAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
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

describe('UserProductMethods — user product e famílias', function () {
    beforeEach(fn () => Http::fake(['api.mercadolibre.com/*' => Http::response(['ok' => true], 200)]));

    it('get GET /user-products/{id}', function () {
        userProductMethods()->get('MLBU22012');
        userProductMethodsAssertSent('GET', ['/user-products/MLBU22012']);
    });

    it('createItem POST /user-products/{id}/items', function () {
        userProductMethods()->createItem('MLBU3691277914', ['price' => 45000, 'category_id' => 'MLB37525', 'currency_id' => 'BRL', 'buying_mode' => 'buy_it_now', 'listing_type_id' => 'gold_special']);
        userProductMethodsAssertSent('POST', ['/user-products/MLBU3691277914/items'], ['"price":45000', '"listing_type_id":"gold_special"']);
    });

    it('family GET /user-products-families/{id}', function () {
        userProductMethods()->family(1034108706118545);
        userProductMethodsAssertSent('GET', ['/user-products-families/1034108706118545']);
    });

    it('siteFamily GET /sites/{site}/user-products-families/{id}', function () {
        userProductMethods()->siteFamily(9871232123, 'MLA');
        userProductMethodsAssertSent('GET', ['/sites/MLA/user-products-families/9871232123']);
    });

    it('updateFamily PUT /user-products-families/{id}', function () {
        userProductMethods()->updateFamily(1, ['family_name' => 'Bone Dry Fit']);
        userProductMethodsAssertSent('PUT', ['/user-products-families/1'], ['"family_name":"Bone Dry Fit"']);
    });

    it('familyUserProducts GET /user-products-families/{id}/user-products', function () {
        userProductMethods()->familyUserProducts(1);
        userProductMethodsAssertSent('GET', ['/user-products-families/1/user-products']);
    });

    it('addFamilyUserProduct POST /user-products-families/{id}/user-products', function () {
        userProductMethods()->addFamilyUserProduct(1, ['attributes' => [['id' => 'COLOR', 'values' => [['name' => 'Laranja']]]]]);
        userProductMethodsAssertSent('POST', ['/user-products-families/1/user-products'], ['"attributes":[{"id":"COLOR"']);
    });

    it('updateFamilyUserProducts PUT /user-products-families/{id}/user-products com user_products', function () {
        userProductMethods()->updateFamilyUserProducts(1, [['id' => 'MLBU1234', 'attributes' => []]]);
        userProductMethodsAssertSent('PUT', ['/user-products-families/1/user-products'], ['"user_products":[{"id":"MLBU1234","attributes":[]}]']);
    });

    it('createFamilyTask POST /user-products-families/{id}/tasks', function () {
        userProductMethods()->createFamilyTask(1, ['common_content' => ['family_name' => 'x', 'domain_id' => 'MLA-FLASK']]);
        userProductMethodsAssertSent('POST', ['/user-products-families/1/tasks'], ['"common_content":{"family_name":"x","domain_id":"MLA-FLASK"}']);
    });

    it('familyTask GET /user-products-families/tasks/{id}', function () {
        userProductMethods()->familyTask('abc-123');
        userProductMethodsAssertSent('GET', ['/user-products-families/tasks/abc-123']);
    });
});

describe('UserProductMethods — estoque distribuído', function () {
    it('stock GET /user-products/{id}/stock', function () {
        Http::fake(['api.mercadolibre.com/*' => Http::response(['locations' => []], 200)]);
        userProductMethods()->stock('MLBU206642488');
        userProductMethodsAssertSent('GET', ['/user-products/MLBU206642488/stock']);
    });

    it('stockWithVersion devolve body + header x-version', function () {
        Http::fake(['api.mercadolibre.com/*' => Http::response(['locations' => [['type' => 'seller_warehouse', 'quantity' => 5]]], 200, ['x-version' => '17'])]);
        $out = userProductMethods()->stockWithVersion('MLBU1');
        expect($out['version'])->toBe('17')
            ->and($out['stock']['locations'][0]['quantity'])->toBe(5);
        userProductMethodsAssertSent('GET', ['/user-products/MLBU1/stock']);
    });

    it('stockWithVersion lança MercadoLivreRequestException em erro', function () {
        Http::fake(['api.mercadolibre.com/*' => Http::response(['message' => 'not found'], 404)]);
        expect(fn () => userProductMethods()->stockWithVersion('MLBU1'))->toThrow(MercadoLivreRequestException::class);
    });

    it('updateSellerWarehouseStock PUT /user-products/{id}/stock/type/seller_warehouse com x-version', function () {
        Http::fake(['api.mercadolibre.com/*' => Http::response(['ok' => true], 200)]);
        userProductMethods()->updateSellerWarehouseStock('MLMU123456789', [['store_id' => '123456', 'network_node_id' => 'MXP123451', 'quantity' => 10]], 1);
        userProductMethodsAssertSent(
            'PUT',
            ['/user-products/MLMU123456789/stock/type/seller_warehouse'],
            ['"locations":[{"store_id":"123456","network_node_id":"MXP123451","quantity":10}]'],
            ['x-version' => '1']
        );
    });

    it('searchStores GET /users/{id}/stores/search?tags=stock_location', function () {
        Http::fake(['api.mercadolibre.com/*' => Http::response(['results' => []], 200)]);
        userProductMethods()->searchStores(1008002397);
        userProductMethodsAssertSent('GET', ['/users/1008002397/stores/search', 'tags=stock_location']);
    });

    it('createMultiwarehouseItem POST /items/multiwarehouse', function () {
        Http::fake(['api.mercadolibre.com/*' => Http::response(['id' => 'MLB1'], 201)]);
        userProductMethods()->createMultiwarehouseItem(['title' => 'Lata', 'stock_locations' => [['store_id' => '1', 'network_node_id' => '2', 'quantity' => 10]]]);
        userProductMethodsAssertSent('POST', ['/items/multiwarehouse'], ['"stock_locations":[{"store_id":"1","network_node_id":"2","quantity":10}]']);
    });
});
