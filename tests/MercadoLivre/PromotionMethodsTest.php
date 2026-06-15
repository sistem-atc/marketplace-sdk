<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function mlPromotionIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com']);
    Http::preventStrayRequests();
});

describe('MarketPlaces::MercadoLivre()->promotions()->listItems', function () {
    it('usa /seller-promotions/promotions/{id}/items com promotion_type + app_version', function () {
        Http::fake([
            'api.mercadolibre.com/seller-promotions/promotions/P-MLB1/items*' => Http::response([
                'results' => [[
                    'id' => 'MLB898490337',
                    'status' => 'started',
                    'price' => 96.9,
                    'original_price' => 156.79,
                    'meli_percentage' => 3.7,
                    'seller_percentage' => 34.5,
                ]],
                'paging' => ['total' => 1, 'limit' => 50, 'searchAfter' => 'CURSOR1'],
            ], 200),
        ]);

        $resp = MarketPlaces::MercadoLivre()->promotions(mlPromotionIntegration())
            ->listItems('P-MLB1', 'SMART', limit: 50);

        expect($resp['results'])->toHaveCount(1)
            ->and($resp['results'][0]['id'])->toBe('MLB898490337')
            ->and($resp['paging']['searchAfter'])->toBe('CURSOR1');

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/seller-promotions/promotions/P-MLB1/items')
                && str_contains($req->url(), 'promotion_type=SMART')
                && str_contains($req->url(), 'app_version=v2');
        });
    });

    it('repassa o cursor search_after quando informado', function () {
        Http::fake([
            'api.mercadolibre.com/seller-promotions/promotions/P-MLB1/items*' => Http::response([
                'results' => [], 'paging' => ['total' => 1],
            ], 200),
        ]);

        MarketPlaces::MercadoLivre()->promotions(mlPromotionIntegration())
            ->listItems('P-MLB1', 'SMART', limit: 50, searchAfter: 'CURSOR1');

        Http::assertSent(fn ($req) => str_contains($req->url(), 'search_after=CURSOR1'));
    });
});

describe('MarketPlaces::MercadoLivre()->promotions() enroll/withdraw item', function () {
    it('enrollItem faz POST /seller-promotions/items/{id} com body', function () {
        Http::fake(['api.mercadolibre.com/seller-promotions/items/MLB1*' => Http::response(['status' => 'started'], 200)]);

        MarketPlaces::MercadoLivre()->promotions(mlPromotionIntegration())
            ->enrollItem('MLB1', 'P-MLB1', 'DEAL', 40.90);

        Http::assertSent(fn ($req) => str_contains($req->url(), '/seller-promotions/items/MLB1')
            && $req->method() === 'POST'
            && str_contains($req->body(), '"promotion_id":"P-MLB1"')
            && str_contains($req->body(), '"deal_price":40.9'));
    });

    it('withdrawItem faz DELETE com promotion_id/type na query', function () {
        Http::fake(['api.mercadolibre.com/seller-promotions/items/MLB1*' => Http::response([], 200)]);

        MarketPlaces::MercadoLivre()->promotions(mlPromotionIntegration())
            ->withdrawItem('MLB1', 'P-MLB1', 'DEAL');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/seller-promotions/items/MLB1')
            && $req->method() === 'DELETE'
            && str_contains($req->url(), 'promotion_id=P-MLB1')
            && str_contains($req->url(), 'promotion_type=DEAL'));
    });
});
