<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function productExtrasIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function productExtrasMethods(): ProductMethods
{
    $integration = productExtrasIntegration();

    return new ProductMethods(HttpClientFactory::make($integration), $integration);
}

/** Assinatura Shop: partner_id + timestamp + sign + shop_id + access_token na query. */
function productExtrasShopSigned(Request $req, string $path): bool
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

/**
 * Cada caso: [metodo, args, verbo, path, checagem extra (url ou body)].
 * A checagem extra recebe a Request e devolve bool.
 */
dataset('product_extras', [
    'searchItem' => ['searchItem', [50, 'abc', ['item_name' => 'whey', 'item_status' => ['NORMAL', 'UNLIST'], 'deboost_only' => true]], 'GET', '/api/v2/product/search_item',
        fn (Request $r) => str_contains($r->url(), 'page_size=50') && str_contains($r->url(), 'offset=abc') && str_contains($r->url(), 'item_status=NORMAL%2CUNLIST') && str_contains($r->url(), 'deboost_only=true')],
    'getItemExtraInfo' => ['getItemExtraInfo', [[1, 2]], 'GET', '/api/v2/product/get_item_extra_info',
        fn (Request $r) => str_contains($r->url(), 'item_id_list=1%2C2')],
    'getItemPromotion' => ['getItemPromotion', [[7, 8]], 'GET', '/api/v2/product/get_item_promotion',
        fn (Request $r) => str_contains($r->url(), 'item_id_list=7%2C8')],
    'getItemViolationInfo' => ['getItemViolationInfo', [[5]], 'GET', '/api/v2/product/get_item_violation_info',
        fn (Request $r) => str_contains($r->url(), 'item_id_list=5')],
    'getItemLimit' => ['getItemLimit', [123], 'GET', '/api/v2/product/get_item_limit',
        fn (Request $r) => str_contains($r->url(), 'category_id=123')],
    'getItemLimit sem categoria' => ['getItemLimit', [], 'GET', '/api/v2/product/get_item_limit',
        fn (Request $r) => ! str_contains($r->url(), 'category_id=')],
    'addItem' => ['addItem', [['item_name' => 'Whey 900g', 'original_price' => 99.9, 'category_id' => 100]], 'POST', '/api/v2/product/add_item',
        fn (Request $r) => $r['item_name'] === 'Whey 900g' && $r['category_id'] === 100],
    'updateItem' => ['updateItem', [555, ['item_name' => 'Novo nome']], 'POST', '/api/v2/product/update_item',
        fn (Request $r) => $r['item_id'] === 555 && $r['item_name'] === 'Novo nome'],
    'deleteItem' => ['deleteItem', [555], 'POST', '/api/v2/product/delete_item',
        fn (Request $r) => $r['item_id'] === 555],
    'unlistItems' => ['unlistItems', [[1, 2], true], 'POST', '/api/v2/product/unlist_item',
        fn (Request $r) => $r['item_list'] === [['item_id' => 1, 'unlist' => true], ['item_id' => 2, 'unlist' => true]]],
    'boostItems' => ['boostItems', [[1, 2, 3]], 'POST', '/api/v2/product/boost_item',
        fn (Request $r) => $r['item_id_list'] === [1, 2, 3]],
    'getBoostedList' => ['getBoostedList', [], 'GET', '/api/v2/product/get_boosted_list',
        fn (Request $r) => true],
    'initTierVariation' => ['initTierVariation', [10, [['tier_index' => [0], 'original_price' => 10.0]], [['variation_name' => 'Sabor']]], 'POST', '/api/v2/product/init_tier_variation',
        fn (Request $r) => $r['item_id'] === 10 && $r['model'][0]['tier_index'] === [0] && $r['standardise_tier_variation'][0]['variation_name'] === 'Sabor'],
    'updateTierVariation' => ['updateTierVariation', [10, [['variation_name' => 'Peso']], [['model_id' => 1, 'tier_index' => [0]]]], 'POST', '/api/v2/product/update_tier_variation',
        fn (Request $r) => $r['item_id'] === 10 && $r['model_list'][0]['model_id'] === 1 && $r['standardise_tier_variation'][0]['variation_name'] === 'Peso'],
    'addModels' => ['addModels', [10, [['tier_index' => [1], 'original_price' => 12.5]]], 'POST', '/api/v2/product/add_model',
        fn (Request $r) => $r['item_id'] === 10 && $r['model_list'][0]['original_price'] === 12.5],
    'updateModels' => ['updateModels', [10, [['model_id' => 99, 'model_sku' => 'SKU-1']]], 'POST', '/api/v2/product/update_model',
        fn (Request $r) => $r['item_id'] === 10 && $r['model'][0]['model_sku'] === 'SKU-1'],
    'deleteModel' => ['deleteModel', [10, 99], 'POST', '/api/v2/product/delete_model',
        fn (Request $r) => $r['item_id'] === 10 && $r['model_id'] === 99],
    'updateSipItemPrice' => ['updateSipItemPrice', [10, [['model_id' => 99, 'sip_item_price' => 20.0]]], 'POST', '/api/v2/product/update_sip_item_price',
        fn (Request $r) => $r['item_id'] === 10 && $r['sip_item_price'][0]['model_id'] === 99],
    'getAitemByPitemId' => ['getAitemByPitemId', [42], 'GET', '/api/v2/product/get_aitem_by_pitem_id',
        fn (Request $r) => str_contains($r->url(), 'pitem_id=42')],
    'getComments' => ['getComments', [50, 'cur', 10, null], 'GET', '/api/v2/product/get_comment',
        fn (Request $r) => str_contains($r->url(), 'page_size=50') && str_contains($r->url(), 'cursor=cur') && str_contains($r->url(), 'item_id=10') && ! str_contains($r->url(), 'comment_id=')],
    'replyComments' => ['replyComments', [[['comment_id' => 1, 'comment' => 'Obrigado!']]], 'POST', '/api/v2/product/reply_comment',
        fn (Request $r) => $r['comment_list'][0]['comment'] === 'Obrigado!'],
    'getWeightRecommendation' => ['getWeightRecommendation', [['item_name' => 'Whey', 'category_id' => 1]], 'POST', '/api/v2/product/get_weight_recommendation',
        fn (Request $r) => $r['item_name'] === 'Whey'],
    'getSizeChartList' => ['getSizeChartList', [100, 20, 'c1'], 'GET', '/api/v2/product/get_size_chart_list',
        fn (Request $r) => str_contains($r->url(), 'category_id=100') && str_contains($r->url(), 'page_size=20') && str_contains($r->url(), 'cursor=c1')],
    'getSizeChartDetail' => ['getSizeChartDetail', [7], 'GET', '/api/v2/product/get_size_chart_detail',
        fn (Request $r) => str_contains($r->url(), 'size_chart_id=7')],
    'getAllVehicleList' => ['getAllVehicleList', [100, 200, 'pt-br'], 'GET', '/api/v2/product/get_all_vehicle_list',
        fn (Request $r) => str_contains($r->url(), 'page_size=100') && str_contains($r->url(), 'offset=200') && str_contains($r->url(), 'language=pt-br')],
    'getVehicleListByCompatibilityDetail' => ['getVehicleListByCompatibilityDetail', ['Model', ['brand_id' => 3]], 'GET', '/api/v2/product/get_vehicle_list_by_compatibility_detail',
        fn (Request $r) => str_contains($r->url(), 'compatibility_details=Model') && str_contains($r->url(), 'brand_id=3')],
    'getItemContentDiagnosisResult' => ['getItemContentDiagnosisResult', [[1, 2]], 'POST', '/api/v2/product/get_item_content_diagnosis_result',
        fn (Request $r) => $r['item_id_list'] === [1, 2]],
    'getItemListByContentDiagnosis' => ['getItemListByContentDiagnosis', [48, 'off', [1], [2, 3]], 'POST', '/api/v2/product/get_item_list_by_content_diagnosis',
        fn (Request $r) => $r['page_size'] === 48 && $r['offset'] === 'off' && $r['quality_level'] === [1] && $r['issue_type'] === [2, 3]],
    'getKitItemLimit' => ['getKitItemLimit', [5], 'GET', '/api/v2/product/get_kit_item_limit',
        fn (Request $r) => str_contains($r->url(), 'category_id=5')],
    'addKitItem' => ['addKitItem', [['item_name' => 'Kit'], ['auto_sync_dts' => true]], 'POST', '/api/v2/product/add_kit_item',
        fn (Request $r) => $r['item_setting']['item_name'] === 'Kit' && $r['sync_setting']['auto_sync_dts'] === true],
    'updateKitItem' => ['updateKitItem', [77, ['item_name' => 'Kit 2']], 'POST', '/api/v2/product/update_kit_item',
        fn (Request $r) => $r['item_id'] === 77 && $r['item_setting']['item_name'] === 'Kit 2' && ! isset($r['sync_setting'])],
    'getKitItemInfo' => ['getKitItemInfo', [77], 'GET', '/api/v2/product/get_kit_item_info',
        fn (Request $r) => str_contains($r->url(), 'item_id=77')],
    'generateKitImage' => ['generateKitImage', [[['component_item_id' => 1, 'component_model_id' => 2]]], 'POST', '/api/v2/product/generate_kit_image',
        fn (Request $r) => $r['component_list'][0]['component_item_id'] === 1],
    'searchUnpackagedModelList' => ['searchUnpackagedModelList', [20, 'cc', ['item_id' => 9]], 'POST', '/api/v2/product/search_unpackaged_model_list',
        fn (Request $r) => $r['page_size'] === 20 && $r['cursor'] === 'cc' && $r['item_id'] === 9],
    'getMainItemList' => ['getMainItemList', [[1, 2]], 'GET', '/api/v2/product/get_main_item_list',
        fn (Request $r) => str_contains($r->url(), 'direct_item_id=1%2C2')],
    'getDirectItemList' => ['getDirectItemList', [[3]], 'GET', '/api/v2/product/get_direct_item_list',
        fn (Request $r) => str_contains($r->url(), 'main_item_id=3')],
    'getDirectShopRecommendedPrice' => ['getDirectShopRecommendedPrice', [3, ['BR', 'MX'], ['category_id' => 9]], 'GET', '/api/v2/product/get_direct_shop_recommended_price',
        fn (Request $r) => str_contains($r->url(), 'main_item_id=3') && str_contains($r->url(), 'direct_shop_regions=BR%2CMX') && str_contains($r->url(), 'category_id=9')],
    'getMartItemMappingById' => ['getMartItemMappingById', [11, [22, 33]], 'POST', '/api/v2/product/get_mart_item_mapping_by_id',
        fn (Request $r) => $r['mart_item_id'] === 11 && $r['outlet_shop_id_list'] === [22, 33]],
    'getMartItemByOutletItemId' => ['getMartItemByOutletItemId', [44], 'POST', '/api/v2/product/get_mart_item_by_outlet_item_id',
        fn (Request $r) => $r['outlet_item_id'] === 44],
    'batchUpdateOutletPrice' => ['batchUpdateOutletPrice', [[['outlet_shop_id' => 1, 'item_id' => 2, 'price_list' => [['original_price' => 5.0]]]]], 'POST', '/api/v2/product/batch_update_outlet_price',
        fn (Request $r) => $r['item_list'][0]['outlet_shop_id'] === 1],
    'batchUpdateOutletStock' => ['batchUpdateOutletStock', [[['outlet_shop_id' => 1, 'item_id' => 2, 'stock_list' => [['normal_stock' => 3]]]]], 'POST', '/api/v2/product/batch_update_outlet_stock',
        fn (Request $r) => $r['item_list'][0]['stock_list'][0]['normal_stock'] === 3],
    'batchPublishItemToOutletShop' => ['batchPublishItemToOutletShop', [[['mart_item_id' => 1, 'outlet_shop_id' => 2, 'publish_item' => []]]], 'POST', '/api/v2/product/batch_publish_item_to_outlet_shop',
        fn (Request $r) => $r['item_list'][0]['mart_item_id'] === 1],
    'batchAddItems' => ['batchAddItems', [[['item_name' => 'A'], ['item_name' => 'B']]], 'POST', '/api/v2/product/batch_add_item',
        fn (Request $r) => count($r['item_list']) === 2],
    'getBatchTaskResult' => ['getBatchTaskResult', [4, 987], 'GET', '/api/v2/product/get_batch_task_result',
        fn (Request $r) => str_contains($r->url(), 'task_type=4') && str_contains($r->url(), 'task_id=987')],
]);

it('chama o endpoint certo com assinatura Shop e parametros', function (string $method, array $args, string $verb, string $path, Closure $extra) {
    Http::fake(['partner.shopeemobile.com'.$path.'*' => Http::response(['error' => '', 'response' => []])]);

    productExtrasMethods()->{$method}(...$args);

    Http::assertSent(fn (Request $req) => $req->method() === $verb && productExtrasShopSigned($req, $path) && $extra($req));
})->with('product_extras');

it('getItemExtraInfo devolve item_list e getBoostedList devolve item_list', function () {
    Http::fake([
        'partner.shopeemobile.com/api/v2/product/get_item_extra_info*' => Http::response(['error' => '', 'response' => ['item_list' => [['item_id' => 1, 'sale' => 3]]]]),
        'partner.shopeemobile.com/api/v2/product/get_boosted_list*' => Http::response(['error' => '', 'response' => ['item_list' => [['item_id' => 2]]]]),
    ]);

    $m = productExtrasMethods();

    expect($m->getItemExtraInfo([1]))->toBe([['item_id' => 1, 'sale' => 3]])
        ->and($m->getBoostedList())->toBe([['item_id' => 2]]);
});

it('deleteItem devolve o envelope completo (sem response)', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/product/delete_item*' => Http::response(['error' => '', 'message' => '', 'request_id' => 'abc'])]);

    expect(productExtrasMethods()->deleteItem(1))->toBe(['error' => '', 'message' => '', 'request_id' => 'abc']);
});
