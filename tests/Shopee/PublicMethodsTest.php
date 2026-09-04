<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\PublicApi\PublicMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function publicMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function publicMethods(): PublicMethods
{
    $integration = publicMethodsIntegration();

    return new PublicMethods(HttpClientFactory::make($integration), $integration);
}

function publicMethodsAssertPublic(string $method, string $path, array $queryContains = [], array $bodyContains = []): void
{
    Http::assertSent(function ($req) use ($method, $path, $queryContains, $bodyContains) {
        $url = $req->url();
        $ok = $req->method() === $method
            && str_contains($url, $path)
            && str_contains($url, 'partner_id=2030136')
            && str_contains($url, 'timestamp=')
            && str_contains($url, 'sign=')
            && ! str_contains($url, 'access_token=')
            && ! str_contains($url, 'shop_id=')
            && ! str_contains($url, 'merchant_id=');
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
});

describe('PublicMethods', function () {
    it('getShopsByPartner', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/public/get_shops_by_partner*' => Http::response([
            'authed_shop_list' => [['shop_id' => 123, 'region' => 'BR']], 'more' => false,
        ])]);

        $r = publicMethods()->getShopsByPartner(pageNo: 1, pageSize: 20);

        expect($r['authed_shop_list'][0]['shop_id'])->toBe(123);
        publicMethodsAssertPublic('GET', '/api/v2/public/get_shops_by_partner', ['page_no=1', 'page_size=20']);
    });

    it('getMerchantsByPartner', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/public/get_merchants_by_partner*' => Http::response(['authed_merchant_list' => [], 'more' => false])]);

        publicMethods()->getMerchantsByPartner(pageNo: 3, pageSize: 10);
        publicMethodsAssertPublic('GET', '/api/v2/public/get_merchants_by_partner', ['page_no=3', 'page_size=10']);
    });

    it('getTokenByResendCode manda resend_code no body', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/public/get_token_by_resend_code*' => Http::response([
            'access_token' => 'AT', 'refresh_token' => 'RT', 'expire_in' => 14400, 'shop_id_list' => [999999],
        ])]);

        $r = publicMethods()->getTokenByResendCode('RESEND-123');

        expect($r['access_token'])->toBe('AT');
        publicMethodsAssertPublic('POST', '/api/v2/public/get_token_by_resend_code', [], ['"resend_code":"RESEND-123"']);
    });

    it('getShopeeIpRanges', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/public/get_shopee_ip_ranges*' => Http::response(['response' => ['ip_list' => ['1.2.3.0/24']]])]);

        expect(publicMethods()->getShopeeIpRanges()['response']['ip_list'])->toBe(['1.2.3.0/24']);
        publicMethodsAssertPublic('GET', '/api/v2/public/get_shopee_ip_ranges');
    });
});
