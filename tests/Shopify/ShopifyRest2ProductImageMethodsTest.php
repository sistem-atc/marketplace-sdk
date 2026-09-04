<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\ProductImageMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify ProductImageMethods', function () {
    it('list', fn () => shopifyRest2Call(fn () => shopifyRest2Make(ProductImageMethods::class)->list(20, ['fields' => 'id,src']), 'GET', 'products/20/images.json?fields=id,src'));
    it('count', fn () => shopifyRest2Call(fn () => shopifyRest2Make(ProductImageMethods::class)->count(20), 'GET', 'products/20/images/count.json', null, ['count' => 4]));
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(ProductImageMethods::class)->get(20, 3), 'GET', 'products/20/images/3.json'));
    it('create embrulha em image', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(ProductImageMethods::class)->create(20, ['src' => 'https://x/y.png']),
        'POST', 'products/20/images.json', ['image' => ['src' => 'https://x/y.png']],
    ));
    it('update embrulha em image', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(ProductImageMethods::class)->update(20, 3, ['position' => 1]),
        'PUT', 'products/20/images/3.json', ['image' => ['position' => 1]],
    ));
    it('delete', fn () => shopifyRest2Call(fn () => shopifyRest2Make(ProductImageMethods::class)->delete(20, 3), 'DELETE', 'products/20/images/3.json'));
});
