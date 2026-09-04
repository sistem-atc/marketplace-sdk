<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Ads\AdsMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function adsExtrasIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function adsExtrasMethods(): AdsMethods
{
    $integration = adsExtrasIntegration();

    return new AdsMethods(HttpClientFactory::make($integration), $integration);
}

function adsExtrasFake(string $name, array $response = []): void
{
    Http::fake(["partner.shopeemobile.com/api/v2/ads/{$name}*" => Http::response(['error' => '', 'message' => '', 'response' => $response])]);
}

function adsExtrasAssertCall(string $verb, string $name, ?callable $extra = null): void
{
    Http::assertSent(function (Request $req) use ($verb, $name, $extra) {
        $url = $req->url();

        return $req->method() === $verb
            && str_contains($url, "/api/v2/ads/{$name}")
            && str_contains($url, 'partner_id=2030136')
            && str_contains($url, 'timestamp=')
            && str_contains($url, 'sign=')
            && str_contains($url, 'shop_id=999999')
            && ($extra === null || $extra($req));
    });
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('AdsMethods extras — loja / recomendações', function () {
    it('getShopToggleInfo GET', function () {
        adsExtrasFake('get_shop_toggle_info', ['auto_top_up' => true, 'campaign_surge' => false]);
        expect(adsExtrasMethods()->getShopToggleInfo()['auto_top_up'])->toBeTrue();
        adsExtrasAssertCall('GET', 'get_shop_toggle_info');
    });

    it('getRecommendedKeywordList GET com item_id + input_keyword', function () {
        adsExtrasFake('get_recommended_keyword_list', ['item_id' => 10, 'suggested_keywords' => [['keyword' => 'whey']]]);
        expect(adsExtrasMethods()->getRecommendedKeywordList(10, 'whey')['suggested_keywords'][0]['keyword'])->toBe('whey');
        adsExtrasAssertCall('GET', 'get_recommended_keyword_list', fn ($req) => str_contains($req->url(), 'item_id=10') && str_contains($req->url(), 'input_keyword=whey'));
    });

    it('getRecommendedItemList GET devolve lista', function () {
        adsExtrasFake('get_recommended_item_list', [['item_id' => 1, 'sku_tag_list' => ['best_selling']]]);
        expect(adsExtrasMethods()->getRecommendedItemList()[0]['item_id'])->toBe(1);
        adsExtrasAssertCall('GET', 'get_recommended_item_list');
    });

    it('getAdsFacilShopRate GET no path sem acento', function () {
        adsExtrasFake('get_ads_facil_shop_rate', ['rate' => 2.5, 'update_at' => 1700000000]);
        expect(adsExtrasMethods()->getAdsFacilShopRate()['rate'])->toBe(2.5);
        adsExtrasAssertCall('GET', 'get_ads_facil_shop_rate');
    });
});

describe('AdsMethods extras — performance', function () {
    it('getAllCpcAdsHourlyPerformance GET com performance_date DD-MM-YYYY', function () {
        adsExtrasFake('get_all_cpc_ads_hourly_performance', [['hour' => 0, 'expense' => 1.0]]);
        expect(adsExtrasMethods()->getAllCpcAdsHourlyPerformance('05-08-2026'))->toHaveCount(1);
        adsExtrasAssertCall('GET', 'get_all_cpc_ads_hourly_performance', fn ($req) => str_contains($req->url(), 'performance_date=05-08-2026'));
    });

    it('getProductCampaignDailyPerformance GET com campaign_id_list csv', function () {
        adsExtrasFake('get_product_campaign_daily_performance', ['campaign_list' => []]);
        adsExtrasMethods()->getProductCampaignDailyPerformance('01-08-2026', '05-08-2026', [11, 22]);
        adsExtrasAssertCall('GET', 'get_product_campaign_daily_performance', fn ($req) => str_contains($req->url(), 'start_date=01-08-2026')
            && str_contains($req->url(), 'end_date=05-08-2026')
            && str_contains($req->url(), 'campaign_id_list=11%2C22'));
    });

    it('getProductCampaignHourlyPerformance GET com performance_date + csv', function () {
        adsExtrasFake('get_product_campaign_hourly_performance', ['campaign_list' => []]);
        adsExtrasMethods()->getProductCampaignHourlyPerformance('05-08-2026', [11]);
        adsExtrasAssertCall('GET', 'get_product_campaign_hourly_performance', fn ($req) => str_contains($req->url(), 'performance_date=05-08-2026') && str_contains($req->url(), 'campaign_id_list=11'));
    });
});

describe('AdsMethods extras — campanhas de produto', function () {
    it('getProductLevelCampaignIdList GET com ad_type/offset/limit', function () {
        adsExtrasFake('get_product_level_campaign_id_list', ['has_next_page' => false, 'campaign_list' => [['campaign_id' => 5]]]);
        expect(adsExtrasMethods()->getProductLevelCampaignIdList('manual', 10, 50)['campaign_list'][0]['campaign_id'])->toBe(5);
        adsExtrasAssertCall('GET', 'get_product_level_campaign_id_list', fn ($req) => str_contains($req->url(), 'ad_type=manual') && str_contains($req->url(), 'offset=10') && str_contains($req->url(), 'limit=50'));
    });

    it('getProductLevelCampaignSettingInfo GET com info_type_list + campaign_id_list csv', function () {
        adsExtrasFake('get_product_level_campaign_setting_info', ['campaign_list' => []]);
        adsExtrasMethods()->getProductLevelCampaignSettingInfo([5, 6], [1, 2]);
        adsExtrasAssertCall('GET', 'get_product_level_campaign_setting_info', fn ($req) => str_contains($req->url(), 'info_type_list=1%2C2') && str_contains($req->url(), 'campaign_id_list=5%2C6'));
    });

    it('createAutoProductAds POST (deprecated) devolve campaign_id', function () {
        adsExtrasFake('create_auto_product_ads', ['campaign_id' => 900]);
        expect(adsExtrasMethods()->createAutoProductAds('ref-1', 50.0, '05-08-2026'))->toBe(900);
        adsExtrasAssertCall('POST', 'create_auto_product_ads', fn ($req) => str_contains($req->body(), '"reference_id":"ref-1"')
            && str_contains($req->body(), '"budget":50')
            && str_contains($req->body(), '"start_date":"05-08-2026"')
            && ! str_contains($req->body(), 'end_date'));
    });

    it('editAutoProductAds POST (deprecated) com edit_action e campos extras', function () {
        adsExtrasFake('edit_auto_product_ads', ['campaign_id' => 900]);
        expect(adsExtrasMethods()->editAutoProductAds('ref-2', 900, 'change_budget', ['budget' => 80.0]))->toBe(900);
        adsExtrasAssertCall('POST', 'edit_auto_product_ads', fn ($req) => str_contains($req->body(), '"campaign_id":900')
            && str_contains($req->body(), '"edit_action":"change_budget"')
            && str_contains($req->body(), '"budget":80'));
    });

    it('createManualProductAds POST com obrigatórios + keywords', function () {
        adsExtrasFake('create_manual_product_ads', ['campaign_id' => 901]);
        $id = adsExtrasMethods()->createManualProductAds('ref-3', 30.0, '05-08-2026', 'manual', 123, [
            'selected_keywords' => [['keyword' => 'whey', 'match_type' => 'broad', 'bid_price_per_click' => 0.5]],
        ]);
        expect($id)->toBe(901);
        adsExtrasAssertCall('POST', 'create_manual_product_ads', fn ($req) => str_contains($req->body(), '"bidding_method":"manual"')
            && str_contains($req->body(), '"item_id":123')
            && str_contains($req->body(), '"selected_keywords":[{"keyword":"whey"'));
    });

    it('editManualProductAdKeywords POST com selected_keywords', function () {
        adsExtrasFake('edit_manual_product_ad_keywords', ['campaign_id' => 901, 'failed_edits' => []]);
        $out = adsExtrasMethods()->editManualProductAdKeywords('ref-4', 901, [['edit_action' => 'add', 'keyword' => 'creatina', 'match_type' => 'exact', 'bid_price_per_click' => 0.4]]);
        expect($out['failed_edits'])->toBe([]);
        adsExtrasAssertCall('POST', 'edit_manual_product_ad_keywords', fn ($req) => str_contains($req->body(), '"campaign_id":901') && str_contains($req->body(), '"edit_action":"add"'));
    });

    it('editManualProductAds POST com edit_action', function () {
        adsExtrasFake('edit_manual_product_ads', ['campaign_id' => 901]);
        expect(adsExtrasMethods()->editManualProductAds('ref-5', 901, 'pause'))->toBe(901);
        adsExtrasAssertCall('POST', 'edit_manual_product_ads', fn ($req) => str_contains($req->body(), '"edit_action":"pause"') && str_contains($req->body(), '"reference_id":"ref-5"'));
    });

    it('getCreateProductAdBudgetSuggestion GET devolve float', function () {
        adsExtrasFake('get_create_product_ad_budget_suggestion', ['budget' => 42.5]);
        expect(adsExtrasMethods()->getCreateProductAdBudgetSuggestion('ref-6', 'manual', 'all', 'auto', ['item_id' => 123]))->toBe(42.5);
        adsExtrasAssertCall('GET', 'get_create_product_ad_budget_suggestion', fn ($req) => str_contains($req->url(), 'product_selection=manual')
            && str_contains($req->url(), 'campaign_placement=all')
            && str_contains($req->url(), 'bidding_method=auto')
            && str_contains($req->url(), 'item_id=123'));
    });

    it('getProductRecommendedRoiTarget GET', function () {
        adsExtrasFake('get_product_recommended_roi_target', ['lower_bound' => 1.5, 'exact' => 2.5, 'upper_bound' => 3.5]);
        expect(adsExtrasMethods()->getProductRecommendedRoiTarget('ref-7', 123)['exact'])->toBe(2.5);
        adsExtrasAssertCall('GET', 'get_product_recommended_roi_target', fn ($req) => str_contains($req->url(), 'reference_id=ref-7') && str_contains($req->url(), 'item_id=123'));
    });
});

describe('AdsMethods extras — GMS', function () {
    it('checkCreateGmsProductCampaignEligibility GET', function () {
        adsExtrasFake('check_create_gms_product_campaign_eligibility', ['is_eligible' => true, 'reason' => '']);
        expect(adsExtrasMethods()->checkCreateGmsProductCampaignEligibility()['is_eligible'])->toBeTrue();
        adsExtrasAssertCall('GET', 'check_create_gms_product_campaign_eligibility');
    });

    it('createGmsProductCampaign POST com start_date + daily_budget + opcionais', function () {
        adsExtrasFake('create_gms_product_campaign', ['campaign_id' => 7]);
        expect(adsExtrasMethods()->createGmsProductCampaign('30-11-2026', 100.0, roasTarget: 3.5))->toBe(7);
        adsExtrasAssertCall('POST', 'create_gms_product_campaign', fn ($req) => str_contains($req->body(), '"start_date":"30-11-2026"')
            && str_contains($req->body(), '"daily_budget":100')
            && str_contains($req->body(), '"roas_target":3.5')
            && ! str_contains($req->body(), 'end_date'));
    });

    it('editGmsProductCampaign POST com edit_action + campos', function () {
        adsExtrasFake('edit_gms_product_campaign', ['campaign_id' => 7]);
        expect(adsExtrasMethods()->editGmsProductCampaign('change_budget', ['daily_budget' => 150.0], 7))->toBe(7);
        adsExtrasAssertCall('POST', 'edit_gms_product_campaign', fn ($req) => str_contains($req->body(), '"edit_action":"change_budget"') && str_contains($req->body(), '"daily_budget":150') && str_contains($req->body(), '"campaign_id":7'));
    });

    it('listGmsUserDeletedItem POST com offset/limit', function () {
        adsExtrasFake('list_gms_user_deleted_item', ['item_id_list' => [1], 'total' => 1, 'has_next_page' => false]);
        expect(adsExtrasMethods()->listGmsUserDeletedItem(0, 10)['total'])->toBe(1);
        adsExtrasAssertCall('POST', 'list_gms_user_deleted_item', fn ($req) => str_contains($req->body(), '"offset":0') && str_contains($req->body(), '"limit":10'));
    });

    it('editGmsItemProductCampaign POST com edit_action + item_id_list', function () {
        adsExtrasFake('edit_gms_item_product_campaign', ['campaign_id' => 7]);
        expect(adsExtrasMethods()->editGmsItemProductCampaign('add', [1, 2]))->toBe(7);
        adsExtrasAssertCall('POST', 'edit_gms_item_product_campaign', fn ($req) => str_contains($req->body(), '"edit_action":"add"') && str_contains($req->body(), '"item_id_list":[1,2]'));
    });

    it('getGmsCampaignPerformance POST com datas', function () {
        adsExtrasFake('get_gms_campaign_performance', ['campaign_id' => 7, 'report' => []]);
        expect(adsExtrasMethods()->getGmsCampaignPerformance('01-08-2026', '31-08-2026')['campaign_id'])->toBe(7);
        adsExtrasAssertCall('POST', 'get_gms_campaign_performance', fn ($req) => str_contains($req->body(), '"start_date":"01-08-2026"') && str_contains($req->body(), '"end_date":"31-08-2026"'));
    });

    it('getGmsItemPerformance POST com datas + offset/limit', function () {
        adsExtrasFake('get_gms_item_performance', ['result_list' => [], 'total' => 0, 'has_next_page' => false]);
        adsExtrasMethods()->getGmsItemPerformance('01-08-2026', '31-08-2026', 50, 100, 7);
        adsExtrasAssertCall('POST', 'get_gms_item_performance', fn ($req) => str_contains($req->body(), '"offset":50') && str_contains($req->body(), '"limit":100') && str_contains($req->body(), '"campaign_id":7'));
    });
});
