<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\ShopCategory\ShopCategoryMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopCategoryMethods(): ShopCategoryMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new ShopCategoryMethods(HttpClientFactory::make($integration), $integration);
}

function shopCategoryShopSigned(Request $req, string $path): bool
{
    $url = $req->url();

    return str_contains($url, $path)
        && str_contains($url, 'partner_id=2030136')
        && str_contains($url, 'shop_id=999999')
        && str_contains($url, 'access_token=shopee-token')
        && preg_match('/[?&]timestamp=\d+/', $url) === 1
        && preg_match('/[?&]sign=[0-9a-f]{64}/', $url) === 1;
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

dataset('shop_category', [
    'getShopCategoryList' => ['getShopCategoryList', [2, 50], 'GET', '/api/v2/shop_category/get_shop_category_list',
        fn (Request $r) => str_contains($r->url(), 'page_no=2') && str_contains($r->url(), 'page_size=50')],
    'addShopCategory' => ['addShopCategory', ['Lancamentos', 10], 'POST', '/api/v2/shop_category/add_shop_category',
        fn (Request $r) => $r['name'] === 'Lancamentos' && $r['sort_weight'] === 10],
    'updateShopCategory' => ['updateShopCategory', [5, 'Promo', null, 'INACTIVE'], 'POST', '/api/v2/shop_category/update_shop_category',
        fn (Request $r) => $r['shop_category_id'] === 5 && $r['name'] === 'Promo' && $r['status'] === 'INACTIVE' && ! isset($r['sort_weight'])],
    'deleteShopCategory' => ['deleteShopCategory', [5], 'POST', '/api/v2/shop_category/delete_shop_category',
        fn (Request $r) => $r['shop_category_id'] === 5],
    'getItemList' => ['getItemList', [5, 1, 100], 'GET', '/api/v2/shop_category/get_item_list',
        fn (Request $r) => str_contains($r->url(), 'shop_category_id=5') && str_contains($r->url(), 'page_no=1') && str_contains($r->url(), 'page_size=100')],
    'addItemList' => ['addItemList', [5, [1, 2, 3]], 'POST', '/api/v2/shop_category/add_item_list',
        fn (Request $r) => $r['shop_category_id'] === 5 && $r['item_list'] === [1, 2, 3]],
    'deleteItemList' => ['deleteItemList', [5, [9]], 'POST', '/api/v2/shop_category/delete_item_list',
        fn (Request $r) => $r['shop_category_id'] === 5 && $r['item_list'] === [9]],
]);

it('chama o endpoint certo com assinatura Shop e parametros', function (string $method, array $args, string $verb, string $path, Closure $extra) {
    Http::fake(['partner.shopeemobile.com'.$path.'*' => Http::response(['error' => '', 'response' => []])]);

    shopCategoryMethods()->{$method}(...$args);

    Http::assertSent(fn (Request $req) => $req->method() === $verb && shopCategoryShopSigned($req, $path) && $extra($req));
})->with('shop_category');

it('getShopCategoryList devolve o response com shop_categorys/more', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/shop_category/get_shop_category_list*' => Http::response([
        'error' => '', 'response' => ['shop_categorys' => [['shop_category_id' => 1, 'name' => 'X']], 'more' => false, 'total_count' => 1],
    ])]);

    $resp = shopCategoryMethods()->getShopCategoryList();

    expect($resp['shop_categorys'][0]['name'])->toBe('X')->and($resp['more'])->toBeFalse();
});
