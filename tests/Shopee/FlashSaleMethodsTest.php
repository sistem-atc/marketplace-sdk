<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\FlashSale\FlashSaleMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function flashSaleMethods(): FlashSaleMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new FlashSaleMethods(HttpClientFactory::make($integration), $integration);
}

function flashSaleAssertShopCall(string $verb, string $path, ?callable $extra = null): void
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

function flashSaleFake(string $name, array $response = []): void
{
    Http::fake(["partner.shopeemobile.com/api/v2/shop_flash_sale/{$name}*" => Http::response(['response' => $response])]);
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('FlashSaleMethods', function () {
    it('getTimeSlotId GET com start_time/end_time', function () {
        flashSaleFake('get_time_slot_id', [['timeslot_id' => 1, 'start_time' => 10, 'end_time' => 20]]);

        flashSaleMethods()->getTimeSlotId(startTime: 10, endTime: 20);

        flashSaleAssertShopCall('GET', '/api/v2/shop_flash_sale/get_time_slot_id', fn ($req, $q) => $q['start_time'] === '10' && $q['end_time'] === '20');
    });

    it('getItemCriteria GET sem parametros de negocio', function () {
        flashSaleFake('get_item_criteria', ['criteria' => []]);

        flashSaleMethods()->getItemCriteria();

        flashSaleAssertShopCall('GET', '/api/v2/shop_flash_sale/get_item_criteria');
    });

    it('getShopFlashSaleList GET com type/offset/limit; datas so quando informadas', function () {
        flashSaleFake('get_shop_flash_sale_list', ['flash_sale_list' => [], 'total_count' => 0]);

        flashSaleMethods()->getShopFlashSaleList();
        flashSaleAssertShopCall('GET', '/api/v2/shop_flash_sale/get_shop_flash_sale_list', fn ($req, $q) => $q['type'] === '0' && $q['offset'] === '0'
            && $q['limit'] === '100' && ! isset($q['start_time']));

        flashSaleMethods()->getShopFlashSaleList(type: 2, offset: 100, limit: 50, startTime: 1, endTime: 2);
        flashSaleAssertShopCall('GET', '/api/v2/shop_flash_sale/get_shop_flash_sale_list', fn ($req, $q) => $q['type'] === '2' && $q['start_time'] === '1' && $q['end_time'] === '2');
    });

    it('getShopFlashSale / getShopFlashSaleItems GET com flash_sale_id', function () {
        flashSaleFake('get_shop_flash_sale', ['flash_sale_id' => 30, 'status' => 1]);
        flashSaleFake('get_shop_flash_sale_items', ['item_info' => [], 'models' => []]);

        expect(flashSaleMethods()->getShopFlashSale(30)['status'])->toBe(1);
        flashSaleMethods()->getShopFlashSaleItems(30, offset: 10, limit: 20);

        flashSaleAssertShopCall('GET', '/api/v2/shop_flash_sale/get_shop_flash_sale', fn ($req, $q) => $q['flash_sale_id'] === '30');
        flashSaleAssertShopCall('GET', '/api/v2/shop_flash_sale/get_shop_flash_sale_items', fn ($req, $q) => $q['flash_sale_id'] === '30' && $q['offset'] === '10' && $q['limit'] === '20');
    });

    it('createShopFlashSale POST com timeslot_id', function () {
        flashSaleFake('create_shop_flash_sale', ['flash_sale_id' => 31]);

        expect(flashSaleMethods()->createShopFlashSale(5)['flash_sale_id'])->toBe(31);

        flashSaleAssertShopCall('POST', '/api/v2/shop_flash_sale/create_shop_flash_sale', fn ($req) => $req['timeslot_id'] === 5);
    });

    it('updateShopFlashSale POST com flash_sale_id + status', function () {
        flashSaleFake('update_shop_flash_sale', ['flash_sale_id' => 31, 'status' => 2]);

        flashSaleMethods()->updateShopFlashSale(31, status: 2);

        flashSaleAssertShopCall('POST', '/api/v2/shop_flash_sale/update_shop_flash_sale', fn ($req) => $req['flash_sale_id'] === 31 && $req['status'] === 2);
    });

    it('deleteShopFlashSale POST com flash_sale_id', function () {
        flashSaleFake('delete_shop_flash_sale', ['flash_sale_id' => 31]);

        flashSaleMethods()->deleteShopFlashSale(31);

        flashSaleAssertShopCall('POST', '/api/v2/shop_flash_sale/delete_shop_flash_sale', fn ($req) => $req['flash_sale_id'] === 31);
    });

    it('add/update items POST com items', function () {
        flashSaleFake('add_shop_flash_sale_items', ['failed_items' => []]);
        flashSaleFake('update_shop_flash_sale_items', ['failed_items' => []]);

        $items = [['item_id' => 7, 'purchase_limit' => 0, 'models' => [['model_id' => 70, 'input_promo_price' => 9.9, 'stock' => 5]]]];
        flashSaleMethods()->addShopFlashSaleItems(31, $items);
        flashSaleMethods()->updateShopFlashSaleItems(31, [['item_id' => 7, 'models' => [['model_id' => 70, 'status' => 0]]]]);

        flashSaleAssertShopCall('POST', '/api/v2/shop_flash_sale/add_shop_flash_sale_items', fn ($req) => $req['flash_sale_id'] === 31 && $req['items'][0]['models'][0]['stock'] === 5);
        flashSaleAssertShopCall('POST', '/api/v2/shop_flash_sale/update_shop_flash_sale_items', fn ($req) => $req['items'][0]['models'][0]['status'] === 0);
    });

    it('deleteShopFlashSaleItems POST com item_ids lista de ints', function () {
        flashSaleFake('delete_shop_flash_sale_items', ['failed_items' => []]);

        flashSaleMethods()->deleteShopFlashSaleItems(31, [7, 8]);

        flashSaleAssertShopCall('POST', '/api/v2/shop_flash_sale/delete_shop_flash_sale_items', fn ($req) => $req['item_ids'] === [7, 8]);
    });
});
