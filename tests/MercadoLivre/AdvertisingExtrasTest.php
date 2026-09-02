<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Advertising\AdvertisingMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function advertisingExtrasMethods(): AdvertisingMethods
{
    $integration = new FakeIntegration(accessToken: 'ml-bearer', refreshToken: 'rt', settings: ['client_id' => 'cli', 'client_secret' => 'sec'], active: true, expired: false);

    return new AdvertisingMethods(HttpClientFactory::make($integration), $integration);
}

function advertisingExtrasAssertSent(string $urlPart, string $apiVersion, ?callable $extra = null): void
{
    Http::assertSent(fn (Request $req) => $req->method() === 'GET'
        && str_contains($req->url(), $urlPart)
        && $req->hasHeader('Authorization', 'Bearer ml-bearer')
        && $req->header('Api-Version') === [$apiVersion]
        && ($extra === null || $extra($req)));
}

beforeEach(function () {
    config(['marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com', 'mercadolivre.api_base' => 'https://api.mercadolibre.com', 'mercadolivre.access_token_ttl_seconds' => 21600, 'mercadolivre.default_site_id' => 'MLB']);
    Http::preventStrayRequests();
    Http::fake(['api.mercadolibre.com/*' => Http::response(['ok' => true])]);
});

describe('AdvertisingMethods — Brand Ads extras (Api-Version 1)', function () {
    it('detalhe, items, keywords, keywords/metrics', function () {
        $m = advertisingExtrasMethods();
        $m->getBrandCampaign(10, 123);
        advertisingExtrasAssertSent('/advertising/advertisers/10/brand_ads/campaigns/123', '1', fn ($r) => str_ends_with($r->url(), '/campaigns/123'));
        $m->brandCampaignItems(10, 123);
        advertisingExtrasAssertSent('/advertising/advertisers/10/brand_ads/campaigns/123/items', '1');
        $m->brandCampaignKeywords(10, 123);
        advertisingExtrasAssertSent('/advertising/advertisers/10/brand_ads/campaigns/123/keywords', '1', fn ($r) => str_ends_with($r->url(), '/keywords'));
        $m->brandCampaignKeywordsMetrics(10, 123, '2024-07-01', '2024-07-10');
        advertisingExtrasAssertSent('/advertising/advertisers/10/brand_ads/campaigns/123/keywords/metrics?date_from=2024-07-01&date_to=2024-07-10', '1');
    });

    it('campaigns/metrics agregado e full_summary', function () {
        $m = advertisingExtrasMethods();
        $m->brandCampaignsMetrics(10, '2025-04-01', '2025-04-07');
        advertisingExtrasAssertSent('/advertising/advertisers/10/brand_ads/campaigns/metrics?date_from=2025-04-01&date_to=2025-04-07&aggregation_type=daily', '1');
        $m->brandCampaignsFullSummary(10, '2024-07-01', '2024-07-10');
        advertisingExtrasAssertSent('/advertising/advertisers/10/brand_ads/campaigns/full_summary?date_from=2024-07-01&date_to=2024-07-10', '1');
    });
});

describe('AdvertisingMethods — Display extras (Api-Version 1)', function () {
    it('line_items, creatives e metrics por dimensao', function () {
        $m = advertisingExtrasMethods();
        $m->displayCampaignLineItems(123456, 987654, ['sort_by' => 'start_date', 'sort_order' => 'asc']);
        advertisingExtrasAssertSent('/advertising/advertisers/123456/display/campaigns/987654/line_items?sort_by=start_date&sort_order=asc', '1');
        $m->displayLineItemCreatives(1, 999999, 1, ['sort_by' => 'start_date']);
        advertisingExtrasAssertSent('/advertising/advertisers/1/display/campaigns/999999/line_items/1/creatives?sort_by=start_date', '1');
        $m->displayMetrics(0, 'line_items', '2024-09-19', '2024-09-19', ['campaign_id' => 1111]);
        advertisingExtrasAssertSent('/advertising/advertisers/0/display/metrics?dimension=line_items&date_from=2024-09-19&date_to=2024-09-19&campaign_id=1111', '1');
    });
});

describe('AdvertisingMethods — Product Ads /advertising/{site} (Api-Version 2)', function () {
    it('campaigns/search, ad_groups/search, ads/search com filters[]', function () {
        $m = advertisingExtrasMethods();
        $m->productAdsCampaignsSearch('MLM', 12345, ['filters' => ['status' => 'active']]);
        advertisingExtrasAssertSent('/advertising/MLM/advertisers/12345/product_ads/campaigns/search?filters%5Bstatus%5D=active', '2');
        $m->productAdsAdGroupsSearch('MLA', 882927, ['filters' => ['item_ids' => 'MLA1,MLA2']]);
        advertisingExtrasAssertSent('/advertising/MLA/advertisers/882927/product_ads/ad_groups/search?filters%5Bitem_ids%5D=MLA1%2CMLA2', '2');
        $m->productAdsAdsSearch('MLM', 12345, ['limit' => 1]);
        advertisingExtrasAssertSent('/advertising/MLM/advertisers/12345/product_ads/ads/search?limit=1', '2');
    });

    it('campanha, ad group e metricas', function () {
        $m = advertisingExtrasMethods();
        $m->productAdsCampaignAdsMetrics('MLM', 12345, 777, ['date_from' => '2025-10-28', 'date_to' => '2025-10-29']);
        advertisingExtrasAssertSent('/advertising/MLM/advertisers/12345/product_ads/campaigns/777/ads/metrics?date_from=2025-10-28&date_to=2025-10-29', '2');
        $m->getProductAdsCampaign('MLM', 355189450, ['metrics' => 'clicks,cost']);
        advertisingExtrasAssertSent('/advertising/MLM/product_ads/campaigns/355189450?metrics=clicks%2Ccost', '2');
        $m->productAdsCampaignAdGroupsMetrics('MCO', 355771832, ['date_from' => '2026-04-01', 'date_to' => '2026-04-01']);
        advertisingExtrasAssertSent('/advertising/MCO/product_ads/campaigns/355771832/ad_groups/metrics?date_from=2026-04-01&date_to=2026-04-01', '2');
        $m->getProductAdsAdGroup('MLM', 65867);
        advertisingExtrasAssertSent('/advertising/MLM/product_ads/ad_groups/65867', '2', fn ($r) => str_ends_with($r->url(), '/ad_groups/65867'));
        $m->productAdsAdGroupAds('MLM', 1142185192, ['metrics' => 'clicks']);
        advertisingExtrasAssertSent('/advertising/MLM/product_ads/ad_groups/1142185192/ads?metrics=clicks', '2');
    });

    it('bonifications', function () {
        advertisingExtrasMethods()->bonifications();
        advertisingExtrasAssertSent('/advertising/advertisers/bonifications', '1');
    });
});
