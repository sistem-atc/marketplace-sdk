<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Sbs\SbsMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function sbsMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function sbsMethods(): SbsMethods
{
    $integration = sbsMethodsIntegration();

    return new SbsMethods(HttpClientFactory::make($integration), $integration);
}

function sbsMethodsAssertGet(string $path, ?callable $extra = null): void
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

describe('SbsMethods', function () {
    it('getBoundWhsInfo GET devolve a lista de armazéns', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/sbs/get_bound_whs_info*' => Http::response(['error' => '', 'response' => ['list' => [['whs_id' => 'BRA', 'whs_region' => 'BR']]]])]);

        expect(sbsMethods()->getBoundWhsInfo()[0]['whs_id'])->toBe('BRA');
        sbsMethodsAssertGet('/api/v2/sbs/get_bound_whs_info');
    });

    it('getCurrentInventory GET manda whs_region + paginação + filtros', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/sbs/get_current_inventory*' => Http::response(['error' => '', 'response' => ['item_list' => [['item_id' => 1]]]])]);

        $out = sbsMethods()->getCurrentInventory('BR', 2, 20, ['search_type' => 2, 'keyword' => 'SKU1', 'whs_ids' => 'A,B']);

        expect($out['item_list'])->toHaveCount(1);
        sbsMethodsAssertGet('/api/v2/sbs/get_current_inventory', fn ($req) => str_contains($req->url(), 'whs_region=BR')
            && str_contains($req->url(), 'page_no=2')
            && str_contains($req->url(), 'page_size=20')
            && str_contains($req->url(), 'search_type=2')
            && str_contains($req->url(), 'keyword=SKU1')
            && str_contains($req->url(), 'whs_ids=A%2CB'));
    });

    it('getExpiryReport GET com whs_region e expiry_status csv', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/sbs/get_expiry_report*' => Http::response(['error' => '', 'response' => ['item_list' => []]])]);

        sbsMethods()->getExpiryReport(filters: ['expiry_status' => '0,2']);
        sbsMethodsAssertGet('/api/v2/sbs/get_expiry_report', fn ($req) => str_contains($req->url(), 'whs_region=BR') && str_contains($req->url(), 'expiry_status=0%2C2'));
    });

    it('getStockAging GET com whs_region e aging tag', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/sbs/get_stock_aging*' => Http::response(['error' => '', 'response' => ['item_list' => []]])]);

        sbsMethods()->getStockAging(filters: ['aging_storage_tag' => 1]);
        sbsMethodsAssertGet('/api/v2/sbs/get_stock_aging', fn ($req) => str_contains($req->url(), 'whs_region=BR') && str_contains($req->url(), 'aging_storage_tag=1'));
    });

    it('getStockMovement GET com datas YYYY-MM-DD + whs_region', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/sbs/get_stock_movement*' => Http::response(['error' => '', 'response' => ['total' => 3, 'item_list' => []]])]);

        expect(sbsMethods()->getStockMovement('2026-06-01', '2026-08-29')['total'])->toBe(3);
        sbsMethodsAssertGet('/api/v2/sbs/get_stock_movement', fn ($req) => str_contains($req->url(), 'start_time=2026-06-01')
            && str_contains($req->url(), 'end_time=2026-08-29')
            && str_contains($req->url(), 'whs_region=BR'));
    });

    it('getFulfillmentMappingInventoryList GET com mtsku_ids csv, page_size e next_cursor', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/sbs/get_fulfillment_mapping_inventory_list*' => Http::response(['error' => '', 'response' => ['list' => [], 'total' => 0, 'next_cursor' => '']])]);

        sbsMethods()->getFulfillmentMappingInventoryList(['M1', 'M2'], 50, 'cur');
        sbsMethodsAssertGet('/api/v2/sbs/get_fulfillment_mapping_inventory_list', fn ($req) => str_contains($req->url(), 'mtsku_ids=M1%2CM2')
            && str_contains($req->url(), 'page_size=50')
            && str_contains($req->url(), 'next_cursor=cur'));
    });
});
