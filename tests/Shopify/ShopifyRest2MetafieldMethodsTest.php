<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Metafield\MetafieldMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify MetafieldMethods (chunk2: get/update/count + loja)', function () {
    it('get por recurso', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MetafieldMethods::class)->get('products/20', 7), 'GET', 'products/20/metafields/7.json'));
    it('update por recurso embrulha em metafield', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(MetafieldMethods::class)->update('orders/10', 7, ['value' => 'x', 'type' => 'single_line_text_field']),
        'PUT', 'orders/10/metafields/7.json', ['metafield' => ['value' => 'x', 'type' => 'single_line_text_field']],
    ));
    it('count por recurso', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MetafieldMethods::class)->count('customers/3'), 'GET', 'customers/3/metafields/count.json', null, ['count' => 2]));
    it('funciona pra qualquer dono (articles, blogs, collections, draft_orders, pages, product_images, variants)', function () {
        foreach (['articles/1', 'blogs/1', 'collections/1', 'draft_orders/1', 'pages/1', 'product_images/1', 'variants/1'] as $owner) {
            Http::fake(['*' => Http::response(['metafields' => []], 200)]);
            shopifyRest2Make(MetafieldMethods::class)->list($owner);
            Http::assertSent(fn ($r) => $r->url() === shopifyRest2Url("{$owner}/metafields.json"));
        }
    });
    it('listShop', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MetafieldMethods::class)->listShop(['namespace' => 'erp']), 'GET', 'metafields.json?namespace=erp'));
    it('countShop', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MetafieldMethods::class)->countShop(), 'GET', 'metafields/count.json', null, ['count' => 5]));
    it('getShop', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MetafieldMethods::class)->getShop(7), 'GET', 'metafields/7.json'));
    it('createShop embrulha em metafield', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(MetafieldMethods::class)->createShop(['namespace' => 'erp', 'key' => 'k', 'value' => 'v', 'type' => 'single_line_text_field']),
        'POST', 'metafields.json', ['metafield' => ['namespace' => 'erp', 'key' => 'k', 'value' => 'v', 'type' => 'single_line_text_field']],
    ));
    it('updateShop', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MetafieldMethods::class)->updateShop(7, ['value' => 'z']), 'PUT', 'metafields/7.json', ['metafield' => ['value' => 'z']]));
    it('deleteShop', fn () => shopifyRest2Call(fn () => shopifyRest2Make(MetafieldMethods::class)->deleteShop(7), 'DELETE', 'metafields/7.json'));
    it('metodos preexistentes continuam intactos', function () {
        $m = shopifyRest2Make(MetafieldMethods::class);
        foreach (['list', 'create', 'delete'] as $method) {
            expect(method_exists($m, $method))->toBeTrue();
        }
    });
});
