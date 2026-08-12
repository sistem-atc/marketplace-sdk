<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\Ads\Exceptions\TiktokAdsRequestException;
use SistemAtc\Marketplaces\Tiktok\Ads\Support\OAuth as AdsOAuth;

function makeTiktokAdsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ads-long-lived-token',
        refreshToken: null,
        settings: ['advertiser_ids' => ['7000001']],
        active: true,
        expired: false,
    );
}

describe('MarketPlaces::Tiktok()->ads()', function () {
    it('gmvMaxDaily manda store_ids/dimensions como JSON e o Access-Token no header', function () {
        Http::fake([
            'business-api.tiktok.com/open_api/v1.3/gmv_max/report/get/*' => Http::response([
                'code' => 0, 'message' => 'OK',
                'data' => [
                    'list' => [
                        ['dimensions' => ['stat_time_day' => '2026-08-05'], 'metrics' => ['cost' => '3210.55']],
                        ['dimensions' => ['stat_time_day' => '2026-08-06'], 'metrics' => ['cost' => '2999.01']],
                    ],
                    'page_info' => ['total_page' => 1],
                ],
            ]),
        ]);

        $rows = MarketPlaces::Tiktok()->ads(makeTiktokAdsIntegration())
            ->gmvMaxDaily('7000001', ['7495'], '2026-08-05', '2026-08-06');

        expect($rows)->toHaveCount(2)
            ->and($rows[0]['metrics']['cost'])->toBe('3210.55');

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/open_api/v1.3/gmv_max/report/get/')
                && $req->header('Access-Token')[0] === 'ads-long-lived-token'
                && str_contains(urldecode($req->url()), 'store_ids=["7495"]')
                && str_contains(urldecode($req->url()), 'dimensions=["campaign_id","stat_time_day"]');
        });
    });

    it('auctionDaily pagina seguindo page_info.total_page', function () {
        $page = fn (array $list, int $totalPages) => [
            'code' => 0, 'message' => 'OK',
            'data' => ['list' => $list, 'page_info' => ['total_page' => $totalPages]],
        ];

        Http::fake([
            'business-api.tiktok.com/open_api/v1.3/report/integrated/get/*' => Http::sequence()
                ->push($page([['dimensions' => ['stat_time_day' => '2026-08-05 00:00:00'], 'metrics' => ['spend' => '10.00']]], 2))
                ->push($page([['dimensions' => ['stat_time_day' => '2026-08-06 00:00:00'], 'metrics' => ['spend' => '12.00']]], 2)),
        ]);

        $rows = MarketPlaces::Tiktok()->ads(makeTiktokAdsIntegration())
            ->auctionDaily('7000001', '2026-08-05', '2026-08-06');

        expect($rows)->toHaveCount(2)
            ->and($rows[1]['metrics']['spend'])->toBe('12.00');
    });

    it('converte code != 0 (com HTTP 200) em TiktokAdsRequestException', function () {
        Http::fake([
            'business-api.tiktok.com/*' => Http::response(['code' => 40001, 'message' => 'Invalid metric']),
        ]);

        MarketPlaces::Tiktok()->ads(makeTiktokAdsIntegration())
            ->auctionDaily('7000001', '2026-08-05', '2026-08-05');
    })->throws(TiktokAdsRequestException::class, 'Invalid metric');
});

describe('Tiktok\Ads\Support\OAuth', function () {
    it('exchangeAuthCode devolve token + advertiser_ids', function () {
        Http::fake([
            'business-api.tiktok.com/open_api/v1.3/oauth2/access_token/' => Http::response([
                'code' => 0, 'message' => 'OK',
                'data' => ['access_token' => 'LONG', 'advertiser_ids' => ['7000001'], 'scope' => [4]],
            ]),
        ]);

        $data = AdsOAuth::exchangeAuthCode('app-id', 'secret', 'auth-code');

        expect($data['access_token'])->toBe('LONG')
            ->and($data['advertiser_ids'])->toBe(['7000001']);
    });

    it('authorizationUrl aponta pro portal com app_id + redirect + state', function () {
        $url = AdsOAuth::authorizationUrl('123', 'https://host/cb', 'st4te');

        expect($url)->toStartWith('https://business-api.tiktok.com/portal/auth?')
            ->and($url)->toContain('app_id=123')
            ->and($url)->toContain('state=st4te');
    });
});
