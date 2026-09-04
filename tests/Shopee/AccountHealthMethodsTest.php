<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\AccountHealth\AccountHealthMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function accountHealthIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function accountHealthMethods(): AccountHealthMethods
{
    $integration = accountHealthIntegration();

    return new AccountHealthMethods(HttpClientFactory::make($integration), $integration);
}

function accountHealthAssertGet(string $path, ?callable $extra = null): void
{
    Http::assertSent(function (Request $req) use ($path, $extra) {
        $url = $req->url();

        return $req->method() === 'GET'
            && str_contains($url, $path)
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

describe('AccountHealthMethods', function () {
    it('getShopPerformance GET', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/account_health/get_shop_performance*' => Http::response(['error' => '', 'response' => ['overall_performance' => ['rating' => 1], 'metric_list' => []]])]);

        expect(accountHealthMethods()->getShopPerformance()['overall_performance']['rating'])->toBe(1);
        accountHealthAssertGet('/api/v2/account_health/get_shop_performance');
    });

    it('getMetricSourceDetail GET com metric_id + paginação', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/account_health/get_metric_source_detail*' => Http::response(['error' => '', 'response' => ['metric_id' => 3, 'nfr_order_list' => []]])]);

        expect(accountHealthMethods()->getMetricSourceDetail(3, 2, 10)['metric_id'])->toBe(3);
        accountHealthAssertGet('/api/v2/account_health/get_metric_source_detail', fn ($req) => str_contains($req->url(), 'metric_id=3') && str_contains($req->url(), 'page_no=2') && str_contains($req->url(), 'page_size=10'));
    });

    it('getPenaltyPointHistory GET com violation_type opcional', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/account_health/get_penalty_point_history*' => Http::response(['error' => '', 'response' => ['penalty_point_list' => [], 'total_count' => 0]])]);

        expect(accountHealthMethods()->getPenaltyPointHistory(violationType: 5)['total_count'])->toBe(0);
        accountHealthAssertGet('/api/v2/account_health/get_penalty_point_history', fn ($req) => str_contains($req->url(), 'violation_type=5') && str_contains($req->url(), 'page_no=1'));
    });

    it('getPunishmentHistory GET exige punishment_status', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/account_health/get_punishment_history*' => Http::response(['error' => '', 'response' => ['punishment_list' => [], 'total_count' => 0]])]);

        accountHealthMethods()->getPunishmentHistory(1);
        accountHealthAssertGet('/api/v2/account_health/get_punishment_history', fn ($req) => str_contains($req->url(), 'punishment_status=1'));
    });

    it('getListingsWithIssues GET paginado', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/account_health/get_listings_with_issues*' => Http::response(['error' => '', 'response' => ['listing_list' => [['item_id' => 9]], 'total_count' => 1]])]);

        expect(accountHealthMethods()->getListingsWithIssues(1, 25)['listing_list'][0]['item_id'])->toBe(9);
        accountHealthAssertGet('/api/v2/account_health/get_listings_with_issues', fn ($req) => str_contains($req->url(), 'page_size=25'));
    });

    it('getLateOrders GET paginado', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/account_health/get_late_orders*' => Http::response(['error' => '', 'response' => ['late_order_list' => [['order_sn' => 'L1']], 'total_count' => 1]])]);

        expect(accountHealthMethods()->getLateOrders()['late_order_list'][0]['order_sn'])->toBe('L1');
        accountHealthAssertGet('/api/v2/account_health/get_late_orders', fn ($req) => str_contains($req->url(), 'page_no=1') && str_contains($req->url(), 'page_size=50'));
    });
});
