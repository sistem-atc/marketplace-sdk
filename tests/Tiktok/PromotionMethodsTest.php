<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttPromotionIntegration(): FakeIntegration
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

describe('MarketPlaces::Tiktok()->promotion()', function () {
    it('getActivities usa GET /promotion/202309/activities e normaliza data', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/promotion/202309/activities*' => Http::response([
                'code' => 0,
                'message' => 'success',
                'data' => [
                    'activities' => [['activity_id' => 'A1', 'title' => 'Flash', 'status' => 'ONGOING']],
                    'next_page_token' => 'NEXT',
                    'total_count' => 1,
                ],
            ]),
        ]);

        $resp = MarketPlaces::Tiktok()->promotion(ttPromotionIntegration())
            ->getActivities(pageSize: 100, pageToken: '');

        expect($resp['activities'])->toHaveCount(1)
            ->and($resp['activities'][0]['activity_id'])->toBe('A1')
            ->and($resp['next_page_token'])->toBe('NEXT')
            ->and($resp['total_count'])->toBe(1);

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/promotion/202309/activities')
                && str_contains($req->url(), 'page_size=100')
                && $req->method() === 'GET';
        });
    });

    it('getActivity usa GET /promotion/202309/activities/{id}', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/promotion/202309/activities/A1*' => Http::response([
                'code' => 0, 'data' => ['activity_id' => 'A1', 'title' => 'Flash'],
            ]),
        ]);

        $resp = MarketPlaces::Tiktok()->promotion(ttPromotionIntegration())->getActivity('A1');

        expect($resp['data']['activity_id'])->toBe('A1');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/promotion/202309/activities/A1'));
    });
});
