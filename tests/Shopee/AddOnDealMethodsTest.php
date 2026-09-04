<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\AddOnDeal\AddOnDealMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function addOnDealMethods(): AddOnDealMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new AddOnDealMethods(HttpClientFactory::make($integration), $integration);
}

function addOnDealAssertShopCall(string $verb, string $path, ?callable $extra = null): void
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

function addOnDealFake(string $name, array $response = []): void
{
    Http::fake(["partner.shopeemobile.com/api/v2/add_on_deal/{$name}*" => Http::response(['response' => $response])]);
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('AddOnDealMethods', function () {
    it('getAddOnDealList GET com promotion_status + paginacao', function () {
        addOnDealFake('get_add_on_deal_list', ['add_on_deal_list' => [['add_on_deal_id' => 1]], 'more' => false]);

        $resp = addOnDealMethods()->getAddOnDealList(promotionStatus: 'ongoing', pageNo: 2, pageSize: 50);

        expect($resp['add_on_deal_list'])->toHaveCount(1);
        addOnDealAssertShopCall('GET', '/api/v2/add_on_deal/get_add_on_deal_list', fn ($req, $q) => $q['promotion_status'] === 'ongoing'
            && $q['page_no'] === '2' && $q['page_size'] === '50');
    });

    it('getAddOnDeal / getAddOnDealMainItem / getAddOnDealSubItem GET com add_on_deal_id', function () {
        addOnDealFake('get_add_on_deal', ['add_on_deal_id' => 10]);
        addOnDealFake('get_add_on_deal_main_item', ['main_item_list' => []]);
        addOnDealFake('get_add_on_deal_sub_item', ['sub_item_list' => []]);

        $m = addOnDealMethods();
        expect($m->getAddOnDeal(10)['add_on_deal_id'])->toBe(10);
        $m->getAddOnDealMainItem(10);
        $m->getAddOnDealSubItem(10);

        foreach (['get_add_on_deal', 'get_add_on_deal_main_item', 'get_add_on_deal_sub_item'] as $name) {
            addOnDealAssertShopCall('GET', "/api/v2/add_on_deal/{$name}", fn ($req, $q) => $q['add_on_deal_id'] === '10');
        }
    });

    it('addAddOnDeal POST com payload', function () {
        addOnDealFake('add_add_on_deal', ['add_on_deal_id' => 11]);

        $resp = addOnDealMethods()->addAddOnDeal(['add_on_deal_name' => 'Leve junto', 'start_time' => 1, 'end_time' => 2, 'promotion_type' => 0]);

        expect($resp['add_on_deal_id'])->toBe(11);
        addOnDealAssertShopCall('POST', '/api/v2/add_on_deal/add_add_on_deal', fn ($req) => $req['add_on_deal_name'] === 'Leve junto' && $req['promotion_type'] === 0);
    });

    it('updateAddOnDeal mescla id + campos', function () {
        addOnDealFake('update_add_on_deal', ['add_on_deal_id' => 11]);

        addOnDealMethods()->updateAddOnDeal(11, ['end_time' => 99, 'sub_item_priority' => [5, 6]]);

        addOnDealAssertShopCall('POST', '/api/v2/add_on_deal/update_add_on_deal', fn ($req) => $req['add_on_deal_id'] === 11
            && $req['end_time'] === 99 && $req['sub_item_priority'] === [5, 6]);
    });

    it('deleteAddOnDeal / endAddOnDeal POST com add_on_deal_id', function () {
        addOnDealFake('delete_add_on_deal', ['add_on_deal_id' => 11]);
        addOnDealFake('end_add_on_deal', ['add_on_deal_id' => 11]);

        addOnDealMethods()->deleteAddOnDeal(11);
        addOnDealMethods()->endAddOnDeal(11);

        addOnDealAssertShopCall('POST', '/api/v2/add_on_deal/delete_add_on_deal', fn ($req) => $req['add_on_deal_id'] === 11);
        addOnDealAssertShopCall('POST', '/api/v2/add_on_deal/end_add_on_deal', fn ($req) => $req['add_on_deal_id'] === 11);
    });

    it('add/update main item POST com main_item_list de objetos', function () {
        addOnDealFake('add_add_on_deal_main_item');
        addOnDealFake('update_add_on_deal_main_item');

        addOnDealMethods()->addAddOnDealMainItem(11, [['item_id' => 1, 'status' => 1]]);
        addOnDealMethods()->updateAddOnDealMainItem(11, [['item_id' => 1, 'status' => 2]]);

        addOnDealAssertShopCall('POST', '/api/v2/add_on_deal/add_add_on_deal_main_item', fn ($req) => $req['main_item_list'][0]['status'] === 1);
        addOnDealAssertShopCall('POST', '/api/v2/add_on_deal/update_add_on_deal_main_item', fn ($req) => $req['main_item_list'][0]['status'] === 2);
    });

    it('deleteAddOnDealMainItem manda main_item_list como lista de ints', function () {
        addOnDealFake('delete_add_on_deal_main_item');

        addOnDealMethods()->deleteAddOnDealMainItem(11, [1, 2]);

        addOnDealAssertShopCall('POST', '/api/v2/add_on_deal/delete_add_on_deal_main_item', fn ($req) => $req['add_on_deal_id'] === 11 && $req['main_item_list'] === [1, 2]);
    });

    it('add/update/delete sub item POST com sub_item_list', function () {
        addOnDealFake('add_add_on_deal_sub_item');
        addOnDealFake('update_add_on_deal_sub_item');
        addOnDealFake('delete_add_on_deal_sub_item');

        $sub = [['item_id' => 3, 'model_id' => 0, 'status' => 1, 'sub_item_input_price' => 9.9, 'sub_item_limit' => 2]];
        addOnDealMethods()->addAddOnDealSubItem(11, $sub);
        addOnDealMethods()->updateAddOnDealSubItem(11, $sub);
        addOnDealMethods()->deleteAddOnDealSubItem(11, [['item_id' => 3, 'model_id' => 0]]);

        addOnDealAssertShopCall('POST', '/api/v2/add_on_deal/add_add_on_deal_sub_item', fn ($req) => $req['sub_item_list'][0]['sub_item_input_price'] === 9.9);
        addOnDealAssertShopCall('POST', '/api/v2/add_on_deal/update_add_on_deal_sub_item', fn ($req) => $req['sub_item_list'][0]['sub_item_limit'] === 2);
        addOnDealAssertShopCall('POST', '/api/v2/add_on_deal/delete_add_on_deal_sub_item', fn ($req) => $req['sub_item_list'][0]['item_id'] === 3);
    });
});
