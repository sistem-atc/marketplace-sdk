<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\GlobalProduct\GlobalProductMethods;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function globalProductIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function globalProductMethods(): GlobalProductMethods
{
    $integration = globalProductIntegration();

    return new GlobalProductMethods(HttpClientFactory::make($integration), $integration);
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

function globalProductFakeOk(): void
{
    Http::fake(['partner.shopeemobile.com/api/v2/global_product/*' => Http::response(['error' => '', 'response' => ['ok' => true]])]);
}

/**
 * Cada linha: [metodo, args, verbo, nome do endpoint, trechos esperados na
 * QUERY (GET) ou no BODY json (POST), trechos que NAO podem aparecer].
 */
$globalProductCases = [
    // catalogo
    'getGlobalItemList' => ['getGlobalItemList', ['abc', 20, ['update_time_from' => 1700000000, 'update_time_to' => 1700086400]], 'GET', 'get_global_item_list', ['offset=abc', 'page_size=20', 'update_time_from=1700000000', 'update_time_to=1700086400']],
    'getGlobalItemList sem offset' => ['getGlobalItemList', [], 'GET', 'get_global_item_list', ['page_size=50'], ['offset=']],
    'getGlobalItemInfo' => ['getGlobalItemInfo', [[11, 22]], 'GET', 'get_global_item_info', ['global_item_id_list=11%2C22']],
    'getGlobalItemId' => ['getGlobalItemId', [999999, [13233406680, 17924576533]], 'GET', 'get_global_item_id', ['shop_id=999999', 'item_id_list=13233406680%2C17924576533']],
    'getGlobalItemLimit' => ['getGlobalItemLimit', [100182], 'GET', 'get_global_item_limit', ['category_id=100182']],
    'addGlobalItem' => ['addGlobalItem', [['category_id' => 100182, 'global_item_name' => 'Whey', 'original_price' => 99.9]], 'POST', 'add_global_item', ['"category_id":100182', '"global_item_name":"Whey"', '"original_price":99.9']],
    'updateGlobalItem' => ['updateGlobalItem', [55, ['global_item_name' => 'Whey 2']], 'POST', 'update_global_item', ['"global_item_id":55', '"global_item_name":"Whey 2"']],
    'deleteGlobalItem' => ['deleteGlobalItem', [55], 'POST', 'delete_global_item', ['"global_item_id":55']],
    // models
    'getGlobalModelList' => ['getGlobalModelList', [55], 'GET', 'get_global_model_list', ['global_item_id=55']],
    'initTierVariation' => ['initTierVariation', [55, [['global_model_sku' => 'A', 'tier_index' => [0], 'original_price' => 10.0]], [['variation_id' => 1, 'variation_name' => 'Sabor', 'variation_option_list' => [['variation_option_name' => 'Morango']]]]], 'POST', 'init_tier_variation', ['"global_item_id":55', '"global_model":[{"global_model_sku":"A"', '"standardise_tier_variation":[{"variation_id":1']],
    'updateTierVariation' => ['updateTierVariation', [55, [['variation_id' => 1, 'variation_name' => 'Sabor']], [['model_id' => 9, 'tier_index' => [0]]]], 'POST', 'update_tier_variation', ['"global_item_id":55', '"standardise_tier_variation":[{"variation_id":1', '"model_list":[{"model_id":9,"tier_index":[0]}]']],
    'addGlobalModel' => ['addGlobalModel', [55, [['global_model_sku' => 'B', 'tier_index' => [1], 'original_price' => 12.5]]], 'POST', 'add_global_model', ['"global_item_id":55', '"global_model":[{"global_model_sku":"B"']],
    'updateGlobalModel' => ['updateGlobalModel', [55, [['global_model_id' => 9, 'global_model_sku' => 'B2']]], 'POST', 'update_global_model', ['"global_item_id":55', '"global_model":[{"global_model_id":9,"global_model_sku":"B2"}]']],
    'deleteGlobalModel' => ['deleteGlobalModel', [55, 9], 'POST', 'delete_global_model', ['"global_item_id":55', '"global_model_id":9']],
    // preco/estoque
    'updatePrice' => ['updatePrice', [55, [['global_model_id' => 9, 'original_price' => 15.9]]], 'POST', 'update_price', ['"global_item_id":55', '"price_list":[{"global_model_id":9,"original_price":15.9}]']],
    'updateStock' => ['updateStock', [55, [['global_model_id' => 9, 'seller_stock' => [['location_id' => 'BRZ', 'stock' => 3]]]]], 'POST', 'update_stock', ['"global_item_id":55', '"stock_list":[{"global_model_id":9,"seller_stock":[{"location_id":"BRZ","stock":3}]}]']],
    'getLocalAdjustmentRate' => ['getLocalAdjustmentRate', [999999], 'GET', 'get_local_adjustment_rate', ['shop_id=999999']],
    'updateLocalAdjustmentRate' => ['updateLocalAdjustmentRate', [999999, 1.25], 'POST', 'update_local_adjustment_rate', ['"shop_id":999999', '"adjustment_rate":1.25']],
    // publish
    'createPublishTask' => ['createPublishTask', [55, 999999, 'BR', ['item_name' => 'Whey BR']], 'POST', 'create_publish_task', ['"global_item_id":55', '"shop_id":999999', '"shop_region":"BR"', '"item":{"item_name":"Whey BR"}']],
    'createPublishTask sem item' => ['createPublishTask', [55, 999999, 'BR'], 'POST', 'create_publish_task', ['"shop_region":"BR"'], ['"item"']],
    'getPublishTaskResult' => ['getPublishTaskResult', [4321], 'GET', 'get_publish_task_result', ['publish_task_id=4321']],
    'getPublishableShop' => ['getPublishableShop', [55, [1, 2]], 'GET', 'get_publishable_shop', ['global_item_id=55', 'shop_id_list=1%2C2']],
    'getPublishableShop sem lista' => ['getPublishableShop', [55], 'GET', 'get_publishable_shop', ['global_item_id=55'], ['shop_id_list']],
    'getPublishedList' => ['getPublishedList', [55, [1, 2]], 'GET', 'get_published_list', ['global_item_id=55', 'shop_id_list=1%2C2']],
    'getShopPublishableStatus' => ['getShopPublishableStatus', [55, 100, 50], 'GET', 'get_shop_publishable_status', ['global_item_id=55', 'offset=100', 'page_size=50']],
    'setSyncField' => ['setSyncField', [[['shop_id' => 999999, 'shop_region' => 'BR', 'price' => true]]], 'POST', 'set_sync_field', ['"shop_sync_list":[{"shop_id":999999,"shop_region":"BR","price":true}]']],
    // categoria/marca
    'getCategory' => ['getCategory', ['zh-hans'], 'GET', 'get_category', ['language=zh-hans']],
    'categoryRecommend' => ['categoryRecommend', ['Whey Protein', 'img-1'], 'GET', 'category_recommend', ['global_item_name=Whey%20Protein', 'global_product_cover_image=img-1']],
    'getBrandList' => ['getBrandList', [100182, 10, 20, 2], 'GET', 'get_brand_list', ['category_id=100182', 'offset=10', 'page_size=20', 'status=2']],
    'getAttributeTree' => ['getAttributeTree', [[100182, 100183], 'pt-BR'], 'GET', 'get_attribute_tree', ['category_id_list=100182%2C100183', 'language=pt-BR']],
    'getRecommendAttribute' => ['getRecommendAttribute', ['Whey', 100182, 'img-1'], 'GET', 'get_recommend_attribute', ['global_item_name=Whey', 'category_id=100182', 'cover_image_id=img-1']],
    'searchGlobalAttributeValueList' => ['searchGlobalAttributeValueList', [7, 'Choc', 50, 100], 'POST', 'search_global_attribute_value_list', ['"attribute_id":7', '"value_name":"Choc"', '"limit":50', '"cursor":100']],
    'getVariations' => ['getVariations', [100182], 'GET', 'get_variations', ['category_id=100182']],
    // size chart
    'supportSizeChart' => ['supportSizeChart', [100182], 'GET', 'support_size_chart', ['category_id=100182']],
    'getSizeChartList' => ['getSizeChartList', [100182, 10, 'cur-1'], 'GET', 'get_size_chart_list', ['category_id=100182', 'page_size=10', 'cursor=cur-1']],
    'getSizeChartDetail' => ['getSizeChartDetail', [31, 'zh-Hans'], 'GET', 'get_size_chart_detail', ['size_chart_id=31', 'language=zh-Hans']],
    'updateSizeChart' => ['updateSizeChart', [55, 'img-sc'], 'POST', 'update_size_chart', ['"global_item_id":55', '"size_chart":"img-sc"']],
];

describe('GlobalProductMethods', function () use ($globalProductCases) {
    it('assina com merchant_id e manda os parametros certos', function (string $method, array $args, string $verb, string $endpoint, array $contains, array $missing = []) {
        globalProductFakeOk();
        $r = globalProductMethods()->{$method}(...$args);
        expect($r['response']['ok'])->toBeTrue();

        Http::assertSent(function ($req) use ($verb, $endpoint, $contains, $missing) {
            $url = $req->url();
            $haystack = $verb === 'GET' ? $url : $req->body();
            $ok = $req->method() === $verb
                && str_contains($url, '/api/v2/global_product/'.$endpoint.'?')
                && str_contains($url, 'partner_id=2030136')
                && str_contains($url, 'merchant_id=777')
                && str_contains($url, 'access_token=shopee-token')
                && str_contains($url, 'timestamp=')
                && str_contains($url, 'sign=');
            foreach ($contains as $needle) {
                $ok = $ok && str_contains($haystack, $needle);
            }
            foreach ($missing as $needle) {
                $ok = $ok && ! str_contains($haystack, $needle);
            }

            return $ok;
        });
    })->with($globalProductCases);

    it('cobre os 34 endpoints do modulo global_product', function () use ($globalProductCases) {
        $endpoints = array_unique(array_map(fn (array $c) => $c[3], $globalProductCases));
        expect($endpoints)->toHaveCount(34);
    });

    it('erro de negocio com HTTP 200 vira ShopeeRequestException', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/global_product/get_category*' => Http::response([
            'error' => 'error_auth', 'message' => 'Invalid merchant_id',
        ], 200)]);

        globalProductMethods()->getCategory();
    })->throws(ShopeeRequestException::class);
});
