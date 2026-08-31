<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttAnalyticsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: [
            'app_key' => 'ak',
            'app_secret' => 'as',
            'shop_id' => 'shop1',
            'shop_cipher' => 'cipher1',
        ],
        active: true,
        expired: false,
    );
}

describe('MarketPlaces::Tiktok()->analytics()', function () {
    it('poe o id do recurso no PATH e os filtros na query assinada', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/analytics/202509/shop_products/*' => Http::response([
                'code' => 0,
                'message' => 'Success',
                'data' => ['performance' => ['intervals' => [['start_date' => '2024-04-01']]], 'latest_available_date' => '2024-04-07'],
            ]),
        ]);

        $resp = MarketPlaces::Tiktok()->analytics(ttAnalyticsIntegration())
            ->getShopProductPerformanceDetail('1729592969712207008', [
                'start_date_ge' => '2024-04-01',
                'end_date_lt' => '2024-04-08',
            ]);

        expect($resp['performance']->intervals[0]->startDate)->toBe('2024-04-01')
            ->and($resp['latest_available_date'])->toBe('2024-04-07');

        Http::assertSent(function ($req) {
            return $req->method() === 'GET'
                && str_contains($req->url(), '/analytics/202509/shop_products/1729592969712207008/performance')
                // janela: inicio INCLUSIVO, fim EXCLUSIVO, no fuso da loja
                && str_contains($req->url(), 'start_date_ge=2024-04-01')
                && str_contains($req->url(), 'end_date_lt=2024-04-08')
                // toda chamada do Shop leva assinatura + shop_cipher
                && str_contains($req->url(), 'shop_cipher=cipher1')
                && str_contains($req->url(), 'sign=');
        });
    });

    it('usa o path esquisito do produto-dentro-da-live (/shop/{live_id}/, 202512)', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/analytics/202512/shop/*' => Http::response([
                'code' => 0,
                'message' => 'Success',
                'data' => ['products' => [['id' => 'P1', 'traffic' => ['produt_clicks' => 7]]]],
            ]),
        ]);

        $resp = MarketPlaces::Tiktok()->analytics(ttAnalyticsIntegration())
            ->getShopLiveProductsPerformanceList('7500000000000000000');

        // O typo `produt_clicks` esta' na API; o DTO devolve o nome certo.
        expect($resp['products'][0]->traffic->productClicks)->toBe(7);

        Http::assertSent(fn ($req) => str_contains(
            $req->url(),
            '/analytics/202512/shop/7500000000000000000/products_performance',
        ));
    });

    it('devolve lista VAZIA quando data vem sem a chave, em vez de estourar', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'message' => 'Success', 'data' => []]),
        ]);

        $analytics = MarketPlaces::Tiktok()->analytics(ttAnalyticsIntegration());

        // Fora da janela com dado fechado o TikTok responde 0/vazio, nao erro.
        expect($analytics->getBestsellingCreators(['date' => '2025-11-13', 'time_slot' => '7D'])['creators'])->toBe([])
            ->and($analytics->getShopPerformance()['performance'])->toBeNull()
            ->and($analytics->getShopSkuPerformanceList()['skus'])->toBe([]);
    });

    it('trends de view e de interacao caem em paths diferentes com o MESMO DTO', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/analytics/202502/live_rooms/*' => Http::response([
                'code' => 0,
                'message' => 'Success',
                'data' => [
                    'view_trend_performances' => [['stats_type' => 'TREND_ONLINE_VIEWER', 'data_points' => [['value' => '9', 'timestamp' => 1623812664]]]],
                    'interactive_trend_performances' => [['stats_type' => 'SHARE_PV', 'data_points' => [['value' => '4', 'timestamp' => 1623812664]]]],
                ],
            ]),
        ]);

        $analytics = MarketPlaces::Tiktok()->analytics(ttAnalyticsIntegration());

        $views = $analytics->getLiveRoomViewTrends('L1')['view_trend_performances'][0];
        $inter = $analytics->getLiveRoomInteractiveTrends('L1')['interactive_trend_performances'][0];

        expect($views)->toBeInstanceOf($inter::class)
            ->and($views->statsType)->toBe('TREND_ONLINE_VIEWER')
            ->and($inter->statsType)->toBe('SHARE_PV');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/live_rooms/L1/view_trend_performances')
            || str_contains($req->url(), '/live_rooms/L1/interactive_trend_performances'));
    });
});
