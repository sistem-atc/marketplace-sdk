<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Shop\ShopMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shopMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function shopMethods(): ShopMethods
{
    $integration = shopMethodsIntegration();

    return new ShopMethods(HttpClientFactory::make($integration), $integration);
}

function shopMethodsAssertCall(string $verb, string $path, ?callable $extra = null): void
{
    Http::assertSent(function (Request $req) use ($verb, $path, $extra) {
        $url = $req->url();

        return $req->method() === $verb
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

describe('ShopMethods', function () {
    it('getShopInfo GET devolve o body cru (dados no root)', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/shop/get_shop_info*' => Http::response(['error' => '', 'shop_name' => 'Soldiers', 'region' => 'BR', 'is_cb' => false])]);

        expect(shopMethods()->getShopInfo()['shop_name'])->toBe('Soldiers');
        shopMethodsAssertCall('GET', '/api/v2/shop/get_shop_info');
    });

    it('getProfile GET devolve response', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/shop/get_profile*' => Http::response(['error' => '', 'response' => ['shop_name' => 'S', 'invoice_issuer' => 'x']])]);

        expect(shopMethods()->getProfile()['invoice_issuer'])->toBe('x');
        shopMethodsAssertCall('GET', '/api/v2/shop/get_profile');
    });

    it('updateProfile POST só com campos informados', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/shop/update_profile*' => Http::response(['error' => '', 'response' => ['shop_name' => 'Novo']])]);

        expect(shopMethods()->updateProfile(shopName: 'Novo')['shop_name'])->toBe('Novo');
        shopMethodsAssertCall('POST', '/api/v2/shop/update_profile', fn ($req) => str_contains($req->body(), '"shop_name":"Novo"')
            && ! str_contains($req->body(), 'shop_logo')
            && ! str_contains($req->body(), 'description'));
    });

    it('getWarehouseDetail GET com warehouse_type', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/shop/get_warehouse_detail*' => Http::response(['error' => '', 'response' => [['warehouse_id' => 1, 'warehouse_name' => 'SP']]])]);

        expect(shopMethods()->getWarehouseDetail(2)[0]['warehouse_id'])->toBe(1);
        shopMethodsAssertCall('GET', '/api/v2/shop/get_warehouse_detail', fn ($req) => str_contains($req->url(), 'warehouse_type=2'));
    });

    it('getShopNotification GET com cursor + page_size', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/shop/get_shop_notification*' => Http::response(['error' => '', 'response' => ['cursor' => 12, 'data' => []]])]);

        expect(shopMethods()->getShopNotification(cursor: 10, pageSize: 50)['cursor'])->toBe(12);
        shopMethodsAssertCall('GET', '/api/v2/shop/get_shop_notification', fn ($req) => str_contains($req->url(), 'cursor=10') && str_contains($req->url(), 'page_size=50'));
    });

    it('getAuthorisedResellerBrand GET paginado', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/shop/get_authorised_reseller_brand*' => Http::response(['error' => '', 'response' => ['is_authorised_reseller' => true, 'total_count' => 1, 'more' => false, 'authorised_brand_list' => []]])]);

        expect(shopMethods()->getAuthorisedResellerBrand(2, 20)['is_authorised_reseller'])->toBeTrue();
        shopMethodsAssertCall('GET', '/api/v2/shop/get_authorised_reseller_brand', fn ($req) => str_contains($req->url(), 'page_no=2') && str_contains($req->url(), 'page_size=20'));
    });

    it('getBrShopOnboardingInfo GET devolve dados KYC', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/shop/get_br_shop_onboarding_info*' => Http::response(['error' => '', 'response' => ['tax_id_type' => 2, 'cnpj_id' => '12345678000199']])]);

        expect(shopMethods()->getBrShopOnboardingInfo()['cnpj_id'])->toBe('12345678000199');
        shopMethodsAssertCall('GET', '/api/v2/shop/get_br_shop_onboarding_info');
    });

    it('getShopHolidayMode GET', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/shop/get_shop_holiday_mode*' => Http::response(['error' => '', 'response' => ['holiday_mode_on' => false]])]);

        expect(shopMethods()->getShopHolidayMode()['holiday_mode_on'])->toBeFalse();
        shopMethodsAssertCall('GET', '/api/v2/shop/get_shop_holiday_mode');
    });

    it('setShopHolidayMode POST com flag + campos opcionais', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/shop/set_shop_holiday_mode*' => Http::response(['error' => '', 'response' => ['debug_msg' => 'ok']])]);

        shopMethods()->setShopHolidayMode(holidayModeOn: true, holidayModeType: 1, holidayModeStartTime: 1700000000, holidayModeEndTime: 1700003599, holidayModeDescription: 'Ferias');

        shopMethodsAssertCall('POST', '/api/v2/shop/set_shop_holiday_mode', fn ($req) => str_contains($req->body(), '"holiday_mode_on":true')
            && str_contains($req->body(), '"holiday_mode_type":1')
            && str_contains($req->body(), '"holiday_mode_start_time":1700000000')
            && str_contains($req->body(), '"holiday_mode_end_time":1700003599')
            && str_contains($req->body(), '"holiday_mode_description":"Ferias"'));
    });
});
