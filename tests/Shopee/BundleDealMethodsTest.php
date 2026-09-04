<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\BundleDeal\BundleDealMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function bundleDealMethods(): BundleDealMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new BundleDealMethods(HttpClientFactory::make($integration), $integration);
}

function bundleDealAssertShopCall(string $verb, string $path, ?callable $extra = null): void
{
    Http::assertSent(function ($req) use ($verb, $path, $extra) {
        parse_str((string) parse_url($req->url(), PHP_URL_QUERY), $q);

        return $req->method() === $verb
            && str_contains($req->url(), $path)
            && ($q['partner_id'] ?? null) === '2030136'
            && ($q['shop_id'] ?? null) === '999999'
            && isset($q['timestamp'], $q['sign'], $q['access_token'])
            && ($extra === null || $extra($req, $q));
    });
}

function bundleDealFake(string $name, array $response = []): void
{
    Http::fake(["partner.shopeemobile.com/api/v2/bundle_deal/{$name}*" => Http::response(['response' => $response])]);
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('BundleDealMethods', function () {
    it('getBundleDealList GET com time_status + paginacao (defaults 1/1/20)', function () {
        bundleDealFake('get_bundle_deal_list', ['bundle_deal_list' => [], 'more' => false]);

        bundleDealMethods()->getBundleDealList();
        bundleDealAssertShopCall('GET', '/api/v2/bundle_deal/get_bundle_deal_list', fn ($req, $q) => $q['time_status'] === '1' && $q['page_no'] === '1' && $q['page_size'] === '20');

        bundleDealMethods()->getBundleDealList(timeStatus: 3, pageNo: 2, pageSize: 500);
        bundleDealAssertShopCall('GET', '/api/v2/bundle_deal/get_bundle_deal_list', fn ($req, $q) => $q['time_status'] === '3' && $q['page_size'] === '500');
    });

    it('getBundleDeal / getBundleDealItem GET com bundle_deal_id', function () {
        bundleDealFake('get_bundle_deal', ['bundle_deal_id' => 20, 'name' => 'Kit']);
        bundleDealFake('get_bundle_deal_item', ['item_list' => []]);

        expect(bundleDealMethods()->getBundleDeal(20)['name'])->toBe('Kit');
        bundleDealMethods()->getBundleDealItem(20);

        bundleDealAssertShopCall('GET', '/api/v2/bundle_deal/get_bundle_deal', fn ($req, $q) => $q['bundle_deal_id'] === '20');
        bundleDealAssertShopCall('GET', '/api/v2/bundle_deal/get_bundle_deal_item', fn ($req, $q) => $q['bundle_deal_id'] === '20');
    });

    it('addBundleDeal POST com payload (rule_type, tiers)', function () {
        bundleDealFake('add_bundle_deal', ['bundle_deal_id' => 21]);

        $resp = bundleDealMethods()->addBundleDeal([
            'rule_type' => 2, 'discount_percentage' => 10, 'min_amount' => 2, 'start_time' => 1, 'end_time' => 2,
            'name' => 'Leve 2', 'purchase_limit' => 5, 'additional_tiers' => [['min_amount' => 3, 'discount_percentage' => 15]],
        ]);

        expect($resp['bundle_deal_id'])->toBe(21);
        bundleDealAssertShopCall('POST', '/api/v2/bundle_deal/add_bundle_deal', fn ($req) => $req['rule_type'] === 2 && $req['additional_tiers'][0]['min_amount'] === 3);
    });

    it('updateBundleDeal mescla id + campos', function () {
        bundleDealFake('update_bundle_deal', ['bundle_deal_id' => 21]);

        bundleDealMethods()->updateBundleDeal(21, ['name' => 'Novo']);

        bundleDealAssertShopCall('POST', '/api/v2/bundle_deal/update_bundle_deal', fn ($req) => $req['bundle_deal_id'] === 21 && $req['name'] === 'Novo');
    });

    it('deleteBundleDeal / endBundleDeal POST com bundle_deal_id', function () {
        bundleDealFake('delete_bundle_deal', ['bundle_deal_id' => 21]);
        bundleDealFake('end_bundle_deal', ['bundle_deal_id' => 21]);

        bundleDealMethods()->deleteBundleDeal(21);
        bundleDealMethods()->endBundleDeal(21);

        bundleDealAssertShopCall('POST', '/api/v2/bundle_deal/delete_bundle_deal', fn ($req) => $req['bundle_deal_id'] === 21);
        bundleDealAssertShopCall('POST', '/api/v2/bundle_deal/end_bundle_deal', fn ($req) => $req['bundle_deal_id'] === 21);
    });

    it('add/update item POST com item_list de objetos', function () {
        bundleDealFake('add_bundle_deal_item', ['failed_list' => []]);
        bundleDealFake('update_bundle_deal_item', ['failed_list' => []]);

        bundleDealMethods()->addBundleDealItem(21, [['item_id' => 7, 'status' => 1]]);
        bundleDealMethods()->updateBundleDealItem(21, [['item_id' => 7, 'status' => 0]]);

        bundleDealAssertShopCall('POST', '/api/v2/bundle_deal/add_bundle_deal_item', fn ($req) => $req['bundle_deal_id'] === 21 && $req['item_list'][0]['status'] === 1);
        bundleDealAssertShopCall('POST', '/api/v2/bundle_deal/update_bundle_deal_item', fn ($req) => $req['item_list'][0]['status'] === 0);
    });

    it('deleteBundleDealItem converte ids em item_list[{item_id}]', function () {
        bundleDealFake('delete_bundle_deal_item', ['failed_list' => []]);

        bundleDealMethods()->deleteBundleDealItem(21, [7, 8]);

        bundleDealAssertShopCall('POST', '/api/v2/bundle_deal/delete_bundle_deal_item', fn ($req) => $req['item_list'] === [['item_id' => 7], ['item_id' => 8]]);
    });
});
