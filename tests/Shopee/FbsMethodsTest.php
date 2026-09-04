<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Fbs\FbsMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function fbsMethodsClient(): FbsMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new FbsMethods(HttpClientFactory::make($integration), $integration);
}

function fbsMethodsQuery(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $q);

    return $q;
}

function fbsMethodsAssertShop(string $path): void
{
    Http::assertSent(function ($req) use ($path) {
        $q = fbsMethodsQuery($req->url());

        return $req->method() === 'GET'
            && str_contains($req->url(), $path)
            && ($q['partner_id'] ?? null) === '2030136'
            && ($q['shop_id'] ?? null) === '999999'
            && isset($q['sign'], $q['timestamp'], $q['access_token']);
    });
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

$ok = fn (array $response = []) => Http::response(['error' => '', 'message' => '', 'request_id' => 'x', 'response' => $response]);

describe('FbsMethods', function () use ($ok) {
    it('queryBrShopEnrollmentStatus e queryBrShopBlockStatus sao GET sem parametros', function () use ($ok) {
        Http::fake(['*' => $ok(['enrollment_status' => 'ENROLLED'])]);
        $c = fbsMethodsClient();
        expect($c->queryBrShopEnrollmentStatus()['response']['enrollment_status'])->toBe('ENROLLED');
        $c->queryBrShopBlockStatus();
        fbsMethodsAssertShop('/api/v2/fbs/query_br_shop_enrollment_status');
        fbsMethodsAssertShop('/api/v2/fbs/query_br_shop_block_status');
        Http::assertSentCount(2);
    });

    it('queryBrShopInvoiceError pagina por page_no/page_size (max 100)', function () use ($ok) {
        Http::fake(['*/api/v2/fbs/query_br_shop_invoice_error*' => $ok(['list' => []])]);
        fbsMethodsClient()->queryBrShopInvoiceError(2, 500);
        fbsMethodsAssertShop('/api/v2/fbs/query_br_shop_invoice_error');
        Http::assertSent(fn ($req) => fbsMethodsQuery($req->url())['page_no'] === '2' && fbsMethodsQuery($req->url())['page_size'] === '100');
    });

    it('queryBrSkuBlockStatus manda shop_sku_id', function () use ($ok) {
        Http::fake(['*/api/v2/fbs/query_br_sku_block_status*' => $ok(['is_blocked' => false])]);
        fbsMethodsClient()->queryBrSkuBlockStatus('SKU-1');
        fbsMethodsAssertShop('/api/v2/fbs/query_br_sku_block_status');
        Http::assertSent(fn ($req) => fbsMethodsQuery($req->url())['shop_sku_id'] === 'SKU-1');
    });
});
