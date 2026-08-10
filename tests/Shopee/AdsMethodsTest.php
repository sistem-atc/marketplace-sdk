<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function makeShopeeAdsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: [
            'partner_id' => 2030136,
            'partner_key' => 'fake-key',
            'shop_id' => 347988064,
        ],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
});

describe('MarketPlaces::Shopee()->ads()', function () {
    it('getAllCpcAdsDailyPerformance devolve o expense por dia e assina a chamada', function () {
        Http::fake([
            'partner.shopeemobile.com/api/v2/ads/get_all_cpc_ads_daily_performance*' => Http::response([
                'response' => [
                    ['date' => '05-08-2026', 'expense' => 22630.51, 'broad_gmv' => 210563.34],
                    ['date' => '06-08-2026', 'expense' => 25888.81, 'broad_gmv' => 244392.22],
                ],
                'error' => '',
                'message' => '',
            ]),
        ]);

        $rows = MarketPlaces::Shopee()->ads(makeShopeeAdsIntegration())
            ->getAllCpcAdsDailyPerformance('05-08-2026', '06-08-2026');

        expect($rows)->toHaveCount(2)
            ->and($rows[0]['expense'])->toBe(22630.51)
            ->and($rows[1]['date'])->toBe('06-08-2026');

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/api/v2/ads/get_all_cpc_ads_daily_performance')
                // Datas em DD-MM-YYYY (YYYY-MM-DD devolve error_param)
                && str_contains($req->url(), 'start_date=05-08-2026')
                && str_contains($req->url(), 'end_date=06-08-2026')
                && str_contains($req->url(), 'shop_id=347988064')
                && str_contains($req->url(), 'sign=');
        });
    });

    it('trata erro que vem com HTTP 200 e campo error preenchido', function () {
        Http::fake([
            'partner.shopeemobile.com/api/v2/ads/get_all_cpc_ads_daily_performance*' => Http::response([
                'response' => null,
                'error' => 'ads.performance.error_date_range_too_long',
                'message' => 'date range too long',
            ], 200),
        ]);

        MarketPlaces::Shopee()->ads(makeShopeeAdsIntegration())
            ->getAllCpcAdsDailyPerformance('01-01-2026', '30-06-2026');
    })->throws(ShopeeRequestException::class);

    it('getTotalBalance extrai o saldo do envelope response', function () {
        Http::fake([
            'partner.shopeemobile.com/api/v2/ads/get_total_balance*' => Http::response([
                'response' => ['total_balance' => 9026.01],
                'error' => '',
            ]),
        ]);

        $balance = MarketPlaces::Shopee()->ads(makeShopeeAdsIntegration())->getTotalBalance();

        expect($balance)->toBe(9026.01);
    });
});
