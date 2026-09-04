<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Merchant\MerchantMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function merchantMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function merchantMethods(): MerchantMethods
{
    $integration = merchantMethodsIntegration();

    return new MerchantMethods(HttpClientFactory::make($integration), $integration);
}

function merchantMethodsAssertSigned(string $method, string $path, array $queryContains = [], array $bodyContains = []): void
{
    Http::assertSent(function ($req) use ($method, $path, $queryContains, $bodyContains) {
        $url = $req->url();
        $ok = $req->method() === $method
            && str_contains($url, $path)
            && str_contains($url, 'partner_id=2030136')
            && str_contains($url, 'merchant_id=777')
            && ! str_contains($url, 'shop_id=')
            && str_contains($url, 'timestamp=')
            && str_contains($url, 'sign=');
        foreach ($queryContains as $needle) {
            $ok = $ok && str_contains($url, $needle);
        }
        foreach ($bodyContains as $needle) {
            $ok = $ok && str_contains($req->body(), $needle);
        }

        return $ok;
    });
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
    Http::fake(['partner.shopeemobile.com/api/v2/merchant/*' => Http::response(['error' => '', 'response' => ['ok' => true]])]);
});

describe('MerchantMethods', function () {
    it('getMerchantInfo', function () {
        expect(merchantMethods()->getMerchantInfo()['response']['ok'])->toBeTrue();
        merchantMethodsAssertSigned('GET', '/api/v2/merchant/get_merchant_info');
    });

    it('getShopListByMerchant', function () {
        merchantMethods()->getShopListByMerchant(pageNo: 2, pageSize: 50);
        merchantMethodsAssertSigned('GET', '/api/v2/merchant/get_shop_list_by_merchant', ['page_no=2', 'page_size=50']);
    });

    it('getMerchantWarehouseLocationList', function () {
        merchantMethods()->getMerchantWarehouseLocationList();
        merchantMethodsAssertSigned('GET', '/api/v2/merchant/get_merchant_warehouse_location_list');
    });

    it('getMerchantWarehouseList manda cursor bidirecional no body', function () {
        merchantMethods()->getMerchantWarehouseList(warehouseType: 2, pageSize: 10, nextId: 5);
        merchantMethodsAssertSigned('POST', '/api/v2/merchant/get_merchant_warehouse_list', [], [
            '"warehouse_type":2', '"cursor":{"next_id":5,"prev_id":0,"page_size":10}',
        ]);
    });

    it('getWarehouseEligibleShopList', function () {
        merchantMethods()->getWarehouseEligibleShopList(warehouseId: 88, warehouseType: 1);
        merchantMethodsAssertSigned('POST', '/api/v2/merchant/get_warehouse_eligible_shop_list', [], [
            '"warehouse_id":88', '"warehouse_type":1', '"page_size":30',
        ]);
    });

    it('getMerchantPrepaidAccountList', function () {
        merchantMethods()->getMerchantPrepaidAccountList();
        merchantMethodsAssertSigned('GET', '/api/v2/merchant/get_merchant_prepaid_account_list', ['page_no=1', 'page_size=100']);
    });
});
