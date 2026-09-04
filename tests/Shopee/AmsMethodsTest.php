<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Ams\AmsMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function amsMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function amsMethods(): AmsMethods
{
    $integration = amsMethodsIntegration();

    return new AmsMethods(HttpClientFactory::make($integration), $integration);
}

function amsFake(string $name, array $response = []): void
{
    Http::fake(["partner.shopeemobile.com/api/v2/ams/{$name}*" => Http::response(['error' => '', 'message' => '', 'response' => $response])]);
}

/** Verbo + path + assinatura de loja; `$extra` recebe (url, body). */
function amsAssertCall(string $verb, string $name, ?callable $extra = null): void
{
    Http::assertSent(function (Request $req) use ($verb, $name, $extra) {
        $url = $req->url();

        return $req->method() === $verb
            && str_contains($url, "/api/v2/ams/{$name}")
            && str_contains($url, 'partner_id=2030136')
            && str_contains($url, 'timestamp=')
            && str_contains($url, 'sign=')
            && str_contains($url, 'shop_id=999999')
            && ($extra === null || $extra($url, $req->body()));
    });
}

function amsUrlHas(string $url, string ...$needles): bool
{
    foreach ($needles as $n) {
        if (! str_contains($url, $n)) {
            return false;
        }
    }

    return true;
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('AmsMethods — Open Campaign', function () {
    it('getOpenCampaignNotAddedProduct GET com page_size, cursor e busca', function () {
        amsFake('get_open_campaign_not_added_product', ['item_list' => [], 'total_count' => 0, 'cursor' => '', 'has_more' => false]);
        expect(amsMethods()->getOpenCampaignNotAddedProduct(20, 'c1', 'sales', 'ITEM_ID', '1,2')['has_more'])->toBeFalse();
        amsAssertCall('GET', 'get_open_campaign_not_added_product', fn ($u) => amsUrlHas($u, 'page_size=20', 'cursor=c1', 'sort_by=sales', 'search_type=ITEM_ID', 'search_content=1%2C2'));
    });

    it('getOpenCampaignAddedProduct GET omite opcionais nulos', function () {
        amsFake('get_open_campaign_added_product', ['item_list' => [['item_id' => 1]]]);
        expect(amsMethods()->getOpenCampaignAddedProduct(50)['item_list'][0]['item_id'])->toBe(1);
        amsAssertCall('GET', 'get_open_campaign_added_product', fn ($u) => amsUrlHas($u, 'page_size=50') && ! str_contains($u, 'sort_by='));
    });

    it('getOptimizationSuggestionProduct GET com rcmd_reason_filter', function () {
        amsFake('get_optimization_suggestion_product', ['item_list' => [], 'total' => 0, 'has_more' => false]);
        amsMethods()->getOptimizationSuggestionProduct('product_opportunities', 1, 10);
        amsAssertCall('GET', 'get_optimization_suggestion_product', fn ($u) => amsUrlHas($u, 'rcmd_reason_filter=product_opportunities', 'page_no=1', 'page_size=10'));
    });

    it('batchGetProductsSuggestedRate GET csv e devolve rates', function () {
        amsFake('batch_get_products_suggested_rate', ['rates' => [['item_id' => 1, 'rate' => 5.5]]]);
        expect(amsMethods()->batchGetProductsSuggestedRate([1, 2])[0]['rate'])->toBe(5.5);
        amsAssertCall('GET', 'batch_get_products_suggested_rate', fn ($u) => amsUrlHas($u, 'item_id_list=1%2C2'));
    });

    it('getShopSuggestedRate GET', function () {
        amsFake('get_shop_suggested_rate', ['min_rate' => 1.5, 'max_rate' => 10.5]);
        expect(amsMethods()->getShopSuggestedRate()['max_rate'])->toBe(10.5);
        amsAssertCall('GET', 'get_shop_suggested_rate');
    });

    it('getAutoAddNewProductToggleStatus GET', function () {
        amsFake('get_auto_add_new_product_toggle_status', ['is_open' => true, 'commission_rate' => 3.0]);
        expect(amsMethods()->getAutoAddNewProductToggleStatus()['is_open'])->toBeTrue();
        amsAssertCall('GET', 'get_auto_add_new_product_toggle_status');
    });

    it('updateAutoAddNewProductSetting POST com open + commission_rate', function () {
        amsFake('update_auto_add_new_product_setting');
        amsMethods()->updateAutoAddNewProductSetting(true, 2.5);
        amsAssertCall('POST', 'update_auto_add_new_product_setting', fn ($u, $b) => str_contains($b, '"open":true') && str_contains($b, '"commission_rate":2.5'));
    });

    it('batchAddProductsToOpenCampaign POST com lista + rate + período', function () {
        amsFake('batch_add_products_to_open_campaign', ['success_list' => [1], 'failed_list' => []]);
        expect(amsMethods()->batchAddProductsToOpenCampaign([1], 4.0, 1700000000)['success_list'])->toBe([1]);
        amsAssertCall('POST', 'batch_add_products_to_open_campaign', fn ($u, $b) => str_contains($b, '"item_id_list":[1]') && str_contains($b, '"commission_rate":4') && str_contains($b, '"period_start_time":1700000000') && ! str_contains($b, 'period_end_time'));
    });

    it('addAllProductsToOpenCampaign POST devolve task_id', function () {
        amsFake('add_all_products_to_open_campaign', ['task_type' => 'ADD_ALL', 'task_id' => 't1']);
        expect(amsMethods()->addAllProductsToOpenCampaign(4.0)['task_id'])->toBe('t1');
        amsAssertCall('POST', 'add_all_products_to_open_campaign', fn ($u, $b) => str_contains($b, '"commission_rate":4'));
    });

    it('getOpenCampaignBatchTaskResult GET com task_id', function () {
        amsFake('get_open_campaign_batch_task_result', ['status' => 'DONE', 'progress_rate' => 100]);
        expect(amsMethods()->getOpenCampaignBatchTaskResult('t1')['status'])->toBe('DONE');
        amsAssertCall('GET', 'get_open_campaign_batch_task_result', fn ($u) => amsUrlHas($u, 'task_id=t1'));
    });

    it('batchEditProductsOpenCampaignSetting POST com campaign_ids + rate', function () {
        amsFake('batch_edit_products_open_campaign_setting', ['success_list' => [9]]);
        amsMethods()->batchEditProductsOpenCampaignSetting([9], 6.0, null, 32503651199);
        amsAssertCall('POST', 'batch_edit_products_open_campaign_setting', fn ($u, $b) => str_contains($b, '"campaign_ids":[9]') && str_contains($b, '"commission_rate":6') && str_contains($b, '"period_end_time":32503651199') && ! str_contains($b, 'period_start_time'));
    });

    it('editAllProductsOpenCampaignSetting POST devolve task', function () {
        amsFake('edit_all_products_open_campaign_setting', ['task_id' => 't2']);
        expect(amsMethods()->editAllProductsOpenCampaignSetting(7.0)['task_id'])->toBe('t2');
        amsAssertCall('POST', 'edit_all_products_open_campaign_setting', fn ($u, $b) => str_contains($b, '"commission_rate":7'));
    });

    it('batchRemoveProductsOpenCampaignSetting POST com campaign_ids', function () {
        amsFake('batch_remove_products_open_campaign_setting', ['success_list' => [9]]);
        amsMethods()->batchRemoveProductsOpenCampaignSetting([9, 10]);
        amsAssertCall('POST', 'batch_remove_products_open_campaign_setting', fn ($u, $b) => str_contains($b, '"campaign_ids":[9,10]'));
    });

    it('removeAllProductsOpenCampaignSetting POST sem body', function () {
        amsFake('remove_all_products_open_campaign_setting', ['task_id' => 't3']);
        expect(amsMethods()->removeAllProductsOpenCampaignSetting()['task_id'])->toBe('t3');
        amsAssertCall('POST', 'remove_all_products_open_campaign_setting');
    });
});

describe('AmsMethods — Targeted Campaign', function () {
    it('getTargetedCampaignList GET com paginação + filtros', function () {
        amsFake('get_targeted_campaign_list', ['total_count' => 1, 'campaign_list' => [['campaign_id' => 3]]]);
        expect(amsMethods()->getTargetedCampaignList(1, 50, ['campaign_status' => 'Ongoing'])['total_count'])->toBe(1);
        amsAssertCall('GET', 'get_targeted_campaign_list', fn ($u) => amsUrlHas($u, 'page_no=1', 'page_size=50', 'campaign_status=Ongoing'));
    });

    it('getTargetedCampaignSettings GET com campaign_id', function () {
        amsFake('get_targeted_campaign_settings', ['campaign_name' => 'X', 'item_list' => [], 'affiliate_list' => []]);
        expect(amsMethods()->getTargetedCampaignSettings(3)['campaign_name'])->toBe('X');
        amsAssertCall('GET', 'get_targeted_campaign_settings', fn ($u) => amsUrlHas($u, 'campaign_id=3'));
    });

    it('getTargetedCampaignAddableProductList GET com cursor', function () {
        amsFake('get_targeted_campaign_addable_product_list', ['item_list' => [], 'total_count' => 0, 'cursor' => '']);
        amsMethods()->getTargetedCampaignAddableProductList(30, 'cc', searchType: 'ITEM_NAME', searchContent: 'whey');
        amsAssertCall('GET', 'get_targeted_campaign_addable_product_list', fn ($u) => amsUrlHas($u, 'page_size=30', 'cursor=cc', 'search_type=ITEM_NAME', 'search_content=whey'));
    });

    it('getRecommendedAffiliateList GET com page_size', function () {
        amsFake('get_recommended_affiliate_list', ['total_count' => 0, 'affiliate_list' => []]);
        amsMethods()->getRecommendedAffiliateList(100);
        amsAssertCall('GET', 'get_recommended_affiliate_list', fn ($u) => amsUrlHas($u, 'page_size=100'));
    });

    it('getManagedAffiliateList GET paginado', function () {
        amsFake('get_managed_affiliate_list', ['total_count' => 0, 'affiliate_list' => []]);
        amsMethods()->getManagedAffiliateList(2, 100);
        amsAssertCall('GET', 'get_managed_affiliate_list', fn ($u) => amsUrlHas($u, 'page_no=2', 'page_size=100'));
    });

    it('createNewTargetedCampaign POST com itens, afiliados e opcionais de budget', function () {
        amsFake('create_new_targeted_campaign', ['campaign_id' => 55, 'fail_item_list' => [], 'fail_affiliate_list' => []]);
        $out = amsMethods()->createNewTargetedCampaign('Camp', 1700000000, 32503651199, 'Oi', [['item_id' => 1, 'rate' => 5.0]], [['affiliate_id' => 77]], true, 100.0);
        expect($out['campaign_id'])->toBe(55);
        amsAssertCall('POST', 'create_new_targeted_campaign', fn ($u, $b) => str_contains($b, '"campaign_name":"Camp"')
            && str_contains($b, '"seller_message":"Oi"')
            && str_contains($b, '"item_list":[{"item_id":1,"rate":5}]')
            && str_contains($b, '"affiliate_list":[{"affiliate_id":77}]')
            && str_contains($b, '"is_set_budget":true')
            && str_contains($b, '"budget":100'));
    });

    it('editProductListOfTargetedCampaign POST com edit_type + item_list', function () {
        amsFake('edit_product_list_of_targeted_campaign', ['fail_item_list' => []]);
        amsMethods()->editProductListOfTargetedCampaign(55, 'update', [['item_id' => 1, 'rate' => 6.0]]);
        amsAssertCall('POST', 'edit_product_list_of_targeted_campaign', fn ($u, $b) => str_contains($b, '"campaign_id":55') && str_contains($b, '"edit_type":"update"') && str_contains($b, '"rate":6'));
    });

    it('editAffiliateListOfTargetedCampaign POST com edit_type + affiliate_list', function () {
        amsFake('edit_affiliate_list_of_targeted_campaign', ['fail_affiliate_list' => []]);
        amsMethods()->editAffiliateListOfTargetedCampaign(55, 'delete', [['affiliate_id' => 77]]);
        amsAssertCall('POST', 'edit_affiliate_list_of_targeted_campaign', fn ($u, $b) => str_contains($b, '"edit_type":"delete"') && str_contains($b, '"affiliate_list":[{"affiliate_id":77}]'));
    });

    it('updateBasicInfoOfTargetedCampaign POST só com campos informados', function () {
        amsFake('update_basic_info_of_targeted_campaign');
        amsMethods()->updateBasicInfoOfTargetedCampaign(55, ['campaign_name' => 'Nova', 'budget' => null]);
        amsAssertCall('POST', 'update_basic_info_of_targeted_campaign', fn ($u, $b) => str_contains($b, '"campaign_id":55') && str_contains($b, '"campaign_name":"Nova"') && ! str_contains($b, 'budget'));
    });

    it('terminateTargetedCampaign POST com campaign_id', function () {
        amsFake('terminate_targeted_campaign');
        amsMethods()->terminateTargetedCampaign(55);
        amsAssertCall('POST', 'terminate_targeted_campaign', fn ($u, $b) => str_contains($b, '"campaign_id":55'));
    });
});

describe('AmsMethods — performance', function () {
    it('getShopPerformance GET com period_type/datas/order_type/channel', function () {
        amsFake('get_shop_performance', ['sales' => 100.0, 'orders' => 3]);
        expect(amsMethods()->getShopPerformance('Day', '2026-08-01', '2026-08-01')['orders'])->toBe(3);
        amsAssertCall('GET', 'get_shop_performance', fn ($u) => amsUrlHas($u, 'period_type=Day', 'start_date=2026-08-01', 'end_date=2026-08-01', 'order_type=PlacedOrder', 'channel=AllChannel'));
    });

    it('getProductPerformance GET paginado com item_id', function () {
        amsFake('get_product_performance', ['list' => [], 'total_count' => 0, 'has_more' => false]);
        amsMethods()->getProductPerformance('Last7d', '2026-08-01', '2026-08-07', 1, 50, 'ConfirmedOrder', 'ShopeeVideo', 123);
        amsAssertCall('GET', 'get_product_performance', fn ($u) => amsUrlHas($u, 'period_type=Last7d', 'page_no=1', 'order_type=ConfirmedOrder', 'channel=ShopeeVideo', 'item_id=123'));
    });

    it('getAffiliatePerformance GET com affiliate_id', function () {
        amsFake('get_affiliate_performance', ['list' => []]);
        amsMethods()->getAffiliatePerformance('Month', '2026-08-01', '2026-08-31', affiliateId: 77);
        amsAssertCall('GET', 'get_affiliate_performance', fn ($u) => amsUrlHas($u, 'period_type=Month', 'affiliate_id=77'));
    });

    it('queryAffiliateList GET por id csv', function () {
        amsFake('query_affiliate_list', ['total_count' => 2, 'affiliate_list' => []]);
        expect(amsMethods()->queryAffiliateList(1, [77, 78])['total_count'])->toBe(2);
        amsAssertCall('GET', 'query_affiliate_list', fn ($u) => amsUrlHas($u, 'query_type=1', 'affiliate_id_list=77%2C78') && ! str_contains($u, 'name='));
    });

    it('queryAffiliateList GET por nome', function () {
        amsFake('query_affiliate_list', ['total_count' => 0, 'affiliate_list' => []]);
        amsMethods()->queryAffiliateList(2, name: 'joao');
        amsAssertCall('GET', 'query_affiliate_list', fn ($u) => amsUrlHas($u, 'query_type=2', 'name=joao'));
    });

    it('getContentPerformance GET com channel obrigatório', function () {
        amsFake('get_content_performance', ['list' => []]);
        amsMethods()->getContentPerformance('Week', '2026-08-02', '2026-08-08', 'LiveStreaming', itemId: 5);
        amsAssertCall('GET', 'get_content_performance', fn ($u) => amsUrlHas($u, 'channel=LiveStreaming', 'item_id=5', 'page_no=1'));
    });

    it('getCampaignKeyMetricsPerformance GET', function () {
        amsFake('get_campaign_key_metrics_performance', ['open_campaign_key_metircs' => []]);
        amsMethods()->getCampaignKeyMetricsPerformance('Last30d', '2026-07-01', '2026-07-30');
        amsAssertCall('GET', 'get_campaign_key_metrics_performance', fn ($u) => amsUrlHas($u, 'period_type=Last30d', 'start_date=2026-07-01'));
    });

    it('getOpenCampaignPerformance GET paginado', function () {
        amsFake('get_open_campaign_performance', ['list' => [], 'has_more' => false]);
        amsMethods()->getOpenCampaignPerformance('Day', '2026-08-01', '2026-08-01', 2, 25);
        amsAssertCall('GET', 'get_open_campaign_performance', fn ($u) => amsUrlHas($u, 'page_no=2', 'page_size=25'));
    });

    it('getTargetedCampaignPerformance GET com campaign_id', function () {
        amsFake('get_targeted_campaign_performance', ['list' => []]);
        amsMethods()->getTargetedCampaignPerformance('Day', '2026-08-01', '2026-08-01', campaignId: 55);
        amsAssertCall('GET', 'get_targeted_campaign_performance', fn ($u) => amsUrlHas($u, 'campaign_id=55'));
    });
});

describe('AmsMethods — relatórios', function () {
    it('getConversionReport GET com paginação + filtros de dedução', function () {
        amsFake('get_conversion_report', ['list' => [['order_sn' => 'S1', 'deduction_method' => 'OrderEscrow']], 'total_count' => 1, 'has_more' => false]);
        $out = amsMethods()->getConversionReport(1, 500, ['deduction_status' => 'Deducted', 'place_order_time_start' => 1700000000, 'order_sn' => null]);
        expect($out['list'][0]['deduction_method'])->toBe('OrderEscrow');
        amsAssertCall('GET', 'get_conversion_report', fn ($u) => amsUrlHas($u, 'page_no=1', 'page_size=500', 'deduction_status=Deducted', 'place_order_time_start=1700000000') && ! str_contains($u, 'order_sn='));
    });

    it('getValidationList GET devolve validation_list', function () {
        amsFake('get_validation_list', ['validation_list' => [['validation_id' => 'v1', 'validation_month' => 202508]]]);
        expect(amsMethods()->getValidationList()[0]['validation_id'])->toBe('v1');
        amsAssertCall('GET', 'get_validation_list');
    });

    it('getValidationReport GET com obrigatórios + paginação', function () {
        amsFake('get_validation_report', ['list' => [], 'total_count' => 0, 'has_more' => false]);
        amsMethods()->getValidationReport('v1', 202508, 'Seller', 1700000000, 1702000000, 1, 200, ['verified_status' => 'Valid']);
        amsAssertCall('GET', 'get_validation_report', fn ($u) => amsUrlHas($u, 'validation_id=v1', 'validation_month=202508', 'campaign_source=Seller', 'place_order_time_start=1700000000', 'place_order_time_end=1702000000', 'page_size=200', 'verified_status=Valid'));
    });

    it('getPerformanceDataUpdateTime GET com marker_type', function () {
        amsFake('get_performance_data_update_time', ['last_report_date' => '2026-08-30']);
        expect(amsMethods()->getPerformanceDataUpdateTime()['last_report_date'])->toBe('2026-08-30');
        amsAssertCall('GET', 'get_performance_data_update_time', fn ($u) => amsUrlHas($u, 'marker_type=AmsMarker'));
    });
});
