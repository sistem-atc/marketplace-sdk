<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Principal\PrincipalMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function principalMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777, 'principal_id' => 4242],
        active: true,
        expired: false,
    );
}

function principalMethods(): PrincipalMethods
{
    $integration = principalMethodsIntegration();

    return new PrincipalMethods(HttpClientFactory::make($integration), $integration);
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
    Http::fake(['partner.shopeemobile.com/api/v2/principal/*' => Http::response(['error' => '', 'response' => ['ok' => true]])]);
});

$principalCommon = ['"start_date":"2026-01-01"', '"end_date":"2026-01-31"', '"timezone":"GMT-3"', '"granularity":"month"'];

/** [metodo, args extras apos (start,end,tz,granularity), endpoint, trechos no body, trechos ausentes]. */
$principalCases = [
    'getShopSalesPerformanceDetail' => ['getShopSalesPerformanceDetail', [[['shop_id' => 999999, 'currency' => 'LOCAL']]], 'get_shop_sales_performance_detail', ['"shop_list":[{"shop_id":999999,"currency":"LOCAL"}]']],
    'getShopSalesPerformanceDetail sem lista' => ['getShopSalesPerformanceDetail', [], 'get_shop_sales_performance_detail', [], ['shop_list']],
    'getPrincipalSalesPerformanceDetail' => ['getPrincipalSalesPerformanceDetail', [[['region' => 'BR']]], 'get_principal_sales_performance_detail', ['"region_list":[{"region":"BR"}]']],
    'getShopAffiliatePerformance' => ['getShopAffiliatePerformance', [[['shop_id' => 999999]]], 'get_shop_affiliate_performance', ['"shop_list":[{"shop_id":999999}]']],
    'getPrincipalAffiliatePerformance' => ['getPrincipalAffiliatePerformance', [[['region' => 'BR', 'currency' => 'USD']]], 'get_principal_affiliate_performance', ['"region_list":[{"region":"BR","currency":"USD"}]']],
    'getContentAffiliatePerformance' => ['getContentAffiliatePerformance', [[['shop_id' => 999999, 'content_ids' => [1, 2]]]], 'get_content_affiliate_performance', ['"content_list":[{"shop_id":999999,"content_ids":[1,2]}]'], ['page_size', 'cursor']],
    'getContentAffiliatePerformance paginado' => ['getContentAffiliatePerformance', [[], 200, 400], 'get_content_affiliate_performance', ['"page_size":200', '"cursor":400'], ['content_list']],
    'getShopLivestreamPerformance' => ['getShopLivestreamPerformance', [[['shop_id' => 999999]]], 'get_shop_livestream_performance', ['"shop_list":[{"shop_id":999999}]']],
    'getPrincipalLivestreamPerformance' => ['getPrincipalLivestreamPerformance', [[['region' => 'BR']]], 'get_principal_livestream_performance', ['"region_list":[{"region":"BR"}]']],
    'getSessionLivestreamPerformance' => ['getSessionLivestreamPerformance', [[['shop_id' => 999999, 'session_ids' => [77]]]], 'get_session_livestream_performance', ['"session_list":[{"shop_id":999999,"session_ids":[77]}]']],
    'getShopVideoPerformance' => ['getShopVideoPerformance', [[['shop_id' => 999999]]], 'get_shop_video_performance', ['"shop_list":[{"shop_id":999999}]']],
    'getPrincipalVideoPerformance' => ['getPrincipalVideoPerformance', [[['region' => 'BR']]], 'get_principal_video_performance', ['"region_list":[{"region":"BR"}]']],
    'getClipVideoPerformance' => ['getClipVideoPerformance', [[['shop_id' => 999999, 'video_ids' => [5, 6]]]], 'get_clip_video_performance', ['"video_list":[{"shop_id":999999,"video_ids":[5,6]}]']],
    'getClipVideoPerformance paginado' => ['getClipVideoPerformance', [[], 100, 0], 'get_clip_video_performance', ['"page_size":100', '"cursor":0'], ['video_list']],
];

describe('PrincipalMethods', function () use ($principalCases, $principalCommon) {
    it('POST assinado com principal_id e body com janela + filtros', function (string $method, array $extra, string $endpoint, array $contains, array $missing = []) use ($principalCommon) {
        $r = principalMethods()->{$method}('2026-01-01', '2026-01-31', 'GMT-3', 'month', ...$extra);
        expect($r['response']['ok'])->toBeTrue();

        Http::assertSent(function ($req) use ($endpoint, $contains, $missing, $principalCommon) {
            $url = $req->url();
            $body = $req->body();
            $ok = $req->method() === 'POST'
                && str_contains($url, '/api/v2/principal/'.$endpoint.'?')
                && str_contains($url, 'partner_id=2030136')
                && str_contains($url, 'principal_id=4242')
                && str_contains($url, 'access_token=shopee-token')
                && str_contains($url, 'timestamp=')
                && str_contains($url, 'sign=')
                && ! str_contains($url, 'shop_id=')
                && ! str_contains($url, 'merchant_id=');
            foreach (array_merge($principalCommon, $contains) as $needle) {
                $ok = $ok && str_contains($body, $needle);
            }
            foreach ($missing as $needle) {
                $ok = $ok && ! str_contains($body, $needle);
            }

            return $ok;
        });
    })->with($principalCases);

    it('cobre os 11 endpoints do modulo principal', function () use ($principalCases) {
        expect(array_unique(array_map(fn (array $c) => $c[2], $principalCases)))->toHaveCount(11);
    });

    it('defaults: timezone GMT-3 e granularity day', function () {
        principalMethods()->getShopSalesPerformanceDetail('2026-01-01', '2026-01-01');
        Http::assertSent(fn ($req) => str_contains($req->body(), '"timezone":"GMT-3"') && str_contains($req->body(), '"granularity":"day"'));
    });
});
