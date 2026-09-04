<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\GlobalProduct\GlobalProductMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Principal\PrincipalMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\PublicApi\PublicMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Shopee\Support\SignatureGenerator;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function baseMerchantAuthIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: [
            'partner_id' => 2030136,
            'partner_key' => 'fake-key',
            'shop_id' => 999999,
            'merchant_id' => 777,
            'principal_id' => 4242,
        ],
        active: true,
        expired: false,
    );
}

function baseMerchantAuthQuery(): array
{
    $q = [];
    Http::assertSent(function ($req) use (&$q) {
        parse_str((string) parse_url($req->url(), PHP_URL_QUERY), $q);

        return true;
    });

    return $q;
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('BaseMethods auth por entidade', function () {
    it('Merchant: assina com merchant_id e NAO manda shop_id', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/global_product/get_category*' => Http::response(['error' => '', 'response' => []])]);

        $integration = baseMerchantAuthIntegration();
        (new GlobalProductMethods(HttpClientFactory::make($integration), $integration))->getCategory();

        $q = baseMerchantAuthQuery();
        expect($q['merchant_id'])->toBe('777')
            ->and($q)->not->toHaveKey('shop_id')
            ->and($q['access_token'])->toBe('shopee-token')
            ->and($q['partner_id'])->toBe('2030136');

        $expected = SignatureGenerator::merchantSign(2030136, '/api/v2/global_product/get_category', (int) $q['timestamp'], 'shopee-token', 777, 'fake-key');
        expect($q['sign'])->toBe($expected)
            ->and($expected)->toBe(hash_hmac('sha256', '2030136/api/v2/global_product/get_category'.$q['timestamp'].'shopee-token777', 'fake-key'));
    });

    it('Shop (default): callers existentes continuam assinando com shop_id', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/product/get_model_list*' => Http::response(['error' => '', 'response' => ['model' => []]])]);

        $integration = baseMerchantAuthIntegration();
        (new ProductMethods(HttpClientFactory::make($integration), $integration))->getModelList(1);

        $q = baseMerchantAuthQuery();
        expect($q['shop_id'])->toBe('999999')
            ->and($q)->not->toHaveKey('merchant_id')
            ->and($q['sign'])->toBe(SignatureGenerator::shopSign(2030136, '/api/v2/product/get_model_list', (int) $q['timestamp'], 'shopee-token', 999999, 'fake-key'));
    });

    it('Principal: subclasse com authEntity=principal_id assina com principal_id', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/principal/get_shop_sales_performance_detail*' => Http::response(['error' => '', 'response' => []])]);

        $integration = baseMerchantAuthIntegration();
        (new PrincipalMethods(HttpClientFactory::make($integration), $integration))
            ->getShopSalesPerformanceDetail('2026-01-01', '2026-01-01');

        $q = baseMerchantAuthQuery();
        expect($q['principal_id'])->toBe('4242')
            ->and($q)->not->toHaveKey('shop_id')
            ->and($q)->not->toHaveKey('merchant_id')
            ->and($q['sign'])->toBe(SignatureGenerator::entitySign(2030136, '/api/v2/principal/get_shop_sales_performance_detail', (int) $q['timestamp'], 'shopee-token', 4242, 'fake-key'));
    });

    it('Public: sem access_token nem id de entidade', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/public/get_shopee_ip_ranges*' => Http::response(['error' => '', 'response' => []])]);

        $integration = baseMerchantAuthIntegration();
        (new PublicMethods(HttpClientFactory::make($integration), $integration))->getShopeeIpRanges();

        $q = baseMerchantAuthQuery();
        expect($q)->toHaveKeys(['partner_id', 'timestamp', 'sign'])
            ->and($q)->not->toHaveKey('access_token')
            ->and($q)->not->toHaveKey('shop_id')
            ->and($q)->not->toHaveKey('merchant_id')
            ->and($q['sign'])->toBe(SignatureGenerator::publicSign(2030136, '/api/v2/public/get_shopee_ip_ranges', (int) $q['timestamp'], 'fake-key'));
    });

    it('merchant_id ausente nos settings vira 0 (nao quebra, a Shopee devolve error)', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/merchant/get_merchant_info*' => Http::response(['error' => '', 'response' => []])]);

        $integration = new FakeIntegration(accessToken: 'shopee-token', refreshToken: 'rt', settings: [
            'partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999,
        ], active: true, expired: false);

        (new \SistemAtc\Marketplaces\Shopee\Endpoints\Merchant\MerchantMethods(HttpClientFactory::make($integration), $integration))->getMerchantInfo();

        expect(baseMerchantAuthQuery()['merchant_id'])->toBe('0');
    });

    it('retry em 5xx preserva o modo merchant', function () {
        Http::fake([
            'partner.shopeemobile.com/api/v2/merchant/get_merchant_info*' => Http::sequence()
                ->push(['error' => 'server'], 500, ['Retry-After' => '1'])
                ->push(['error' => '', 'response' => ['merchant_name' => 'ok']], 200),
        ]);

        $integration = baseMerchantAuthIntegration();
        $r = (new \SistemAtc\Marketplaces\Shopee\Endpoints\Merchant\MerchantMethods(HttpClientFactory::make($integration), $integration))->getMerchantInfo();

        expect($r['response']['merchant_name'])->toBe('ok');
        Http::assertSentCount(2);
        Http::assertSent(fn ($req) => str_contains($req->url(), 'merchant_id=777') && ! str_contains($req->url(), 'shop_id='));
    });
});
