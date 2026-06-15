<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function makeShopeeMarketingIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: [
            'partner_id' => 2030136,
            'partner_key' => 'fake-key',
            'shop_id' => 999999,
        ],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
});

/**
 * Endpoints de promoção Shopee. Garante que usam os MÓDULOS REAIS
 * (/api/v2/discount e /api/v2/voucher) — e não o inexistente /api/v2/marketing,
 * que retornava error_not_found.
 */
describe('MarketPlaces::Shopee()->marketing()', function () {
    it('getDiscountList usa /api/v2/discount com discount_status + page_no', function () {
        Http::fake([
            'partner.shopeemobile.com/api/v2/discount/get_discount_list*' => Http::response([
                'response' => ['discount_list' => [['discount_id' => 123]], 'more' => false],
            ]),
        ]);

        $resp = MarketPlaces::Shopee()->marketing(makeShopeeMarketingIntegration())
            ->getDiscountList('ongoing', pageNo: 1, pageSize: 100);

        expect($resp['response']['discount_list'])->toHaveCount(1);

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/api/v2/discount/get_discount_list')
                && str_contains($req->url(), 'discount_status=ongoing')
                && str_contains($req->url(), 'page_no=1')
                && str_contains($req->url(), 'page_size=100')
                && ! str_contains($req->url(), '/api/v2/marketing/');
        });
    });

    it('getVoucherList usa /api/v2/voucher com status + page_no', function () {
        Http::fake([
            'partner.shopeemobile.com/api/v2/voucher/get_voucher_list*' => Http::response([
                'response' => ['voucher_list' => [['voucher_id' => 999]], 'more' => false],
            ]),
        ]);

        $resp = MarketPlaces::Shopee()->marketing(makeShopeeMarketingIntegration())
            ->getVoucherList('ongoing', pageNo: 1, pageSize: 100);

        expect($resp['response']['voucher_list'])->toHaveCount(1);

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/api/v2/voucher/get_voucher_list')
                && str_contains($req->url(), 'status=ongoing')
                && str_contains($req->url(), 'page_no=1')
                && ! str_contains($req->url(), '/api/v2/marketing/');
        });
    });

    it('getDiscount usa /api/v2/discount/get_discount com discount_id + page_no', function () {
        Http::fake([
            'partner.shopeemobile.com/api/v2/discount/get_discount*' => Http::response([
                'response' => ['discount_id' => 123, 'item_list' => [['item_id' => 1]], 'more' => true],
            ]),
        ]);

        $resp = MarketPlaces::Shopee()->marketing(makeShopeeMarketingIntegration())
            ->getDiscount(123, pageNo: 2, pageSize: 50);

        expect($resp['response']['item_list'])->toHaveCount(1);

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/api/v2/discount/get_discount')
                && str_contains($req->url(), 'discount_id=123')
                && str_contains($req->url(), 'page_no=2')
                && ! str_contains($req->url(), '/api/v2/marketing/');
        });
    });
});
