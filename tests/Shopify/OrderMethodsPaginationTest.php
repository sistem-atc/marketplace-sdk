<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopifyOrdersIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shpat_fake',
        refreshToken: null,
        settings: ['shop_domain' => 'test-store.myshopify.com', 'api_version' => '2024-04'],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.shopify.api_version' => '2024-04']);
    Http::preventStrayRequests();
});

describe('Shopify OrderMethods::eachByPeriod (paginacao por cursor)', function () {
    it('segue o Link header (page_info) ate esgotar todas as paginas', function () {
        Http::fake([
            '*/orders.json*' => Http::sequence()
                ->push(['orders' => [['id' => 1], ['id' => 2]]], 200, [
                    'Link' => '<https://test-store.myshopify.com/admin/api/2024-04/orders.json?limit=250&page_info=CURSOR2>; rel="next"',
                ])
                ->push(['orders' => [['id' => 3]]], 200, []),
        ]);

        $orders = iterator_to_array(
            MarketPlaces::Shopify()->orders(shopifyOrdersIntegration())
                ->eachByPeriod('2026-03-01T00:00:00-03:00', '2026-03-31T23:59:59-03:00')
        );

        expect($orders)->toHaveCount(3)
            ->and(array_column($orders, 'id'))->toBe([1, 2, 3]);
    });

    it('para na primeira pagina quando nao ha rel="next"', function () {
        Http::fake([
            '*/orders.json*' => Http::response(['orders' => [['id' => 10]]], 200, []),
        ]);

        $orders = iterator_to_array(
            MarketPlaces::Shopify()->orders(shopifyOrdersIntegration())
                ->eachByPeriod('2026-03-01T00:00:00-03:00', '2026-03-31T23:59:59-03:00')
        );

        expect($orders)->toHaveCount(1)
            ->and($orders[0]['id'])->toBe(10);
    });
});
