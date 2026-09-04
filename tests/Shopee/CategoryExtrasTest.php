<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Product\CategoryMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopeeCategoryExtrasMethods(): CategoryMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new CategoryMethods(HttpClientFactory::make($integration), $integration);
}

function shopeeCategoryExtrasShopSigned(Request $req, string $path): bool
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

dataset('category_extras', [
    'getAttributeTree' => ['getAttributeTree', [[100, 200], 'pt-br'], 'GET', '/api/v2/product/get_attribute_tree',
        fn (Request $r) => str_contains($r->url(), 'category_id_list=100%2C200') && str_contains($r->url(), 'language=pt-br')],
    'searchAttributeValueList' => ['searchAttributeValueList', [55, 'Growth', 0, 50], 'POST', '/api/v2/product/search_attribute_value_list',
        fn (Request $r) => $r['attribute_id'] === 55 && $r['value_name'] === 'Growth' && $r['cursor'] === 0 && $r['limit'] === 50],
    'recommendCategory' => ['recommendCategory', ['Whey Protein', 'img123'], 'GET', '/api/v2/product/category_recommend',
        fn (Request $r) => str_contains($r->url(), 'item_name=Whey%20Protein') && str_contains($r->url(), 'product_cover_image=img123')],
    'getRecommendAttributes' => ['getRecommendAttributes', ['Whey', 100, null], 'GET', '/api/v2/product/get_recommend_attribute',
        fn (Request $r) => str_contains($r->url(), 'item_name=Whey') && str_contains($r->url(), 'category_id=100') && ! str_contains($r->url(), 'cover_image_id=')],
    'registerBrand' => ['registerBrand', [['original_brand_name' => 'Soldiers', 'category_list' => [1], 'brand_region' => 'BR']], 'POST', '/api/v2/product/register_brand',
        fn (Request $r) => $r['original_brand_name'] === 'Soldiers' && $r['brand_region'] === 'BR'],
    'getProductCertificationRule' => ['getProductCertificationRule', [100, [['attribute_id' => 1, 'attribute_value_list' => []]]], 'POST', '/api/v2/product/get_product_certification_rule',
        fn (Request $r) => $r['category_id'] === 100 && $r['attribute_list'][0]['attribute_id'] === 1],
]);

it('chama o endpoint certo com assinatura Shop e parametros', function (string $method, array $args, string $verb, string $path, Closure $extra) {
    Http::fake(['partner.shopeemobile.com'.$path.'*' => Http::response(['error' => '', 'response' => []])]);

    shopeeCategoryExtrasMethods()->{$method}(...$args);

    Http::assertSent(fn (Request $req) => $req->method() === $verb && shopeeCategoryExtrasShopSigned($req, $path) && $extra($req));
})->with('category_extras');

it('getAttributeTree devolve response.list e recommendCategory devolve category_id[]', function () {
    Http::fake([
        'partner.shopeemobile.com/api/v2/product/get_attribute_tree*' => Http::response(['error' => '', 'response' => ['list' => [['category_id' => 100, 'attribute_tree' => []]]]]),
        'partner.shopeemobile.com/api/v2/product/category_recommend*' => Http::response(['error' => '', 'response' => ['category_id' => [100, 101]]]),
    ]);

    $m = shopeeCategoryExtrasMethods();

    expect($m->getAttributeTree([100]))->toBe([['category_id' => 100, 'attribute_tree' => []]])
        ->and($m->recommendCategory('Whey'))->toBe([100, 101]);
});
