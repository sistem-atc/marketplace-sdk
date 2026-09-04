<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\FirstMile\FirstMileMethods;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function firstMileIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function firstMileClient(): FirstMileMethods
{
    $integration = firstMileIntegration();

    return new FirstMileMethods(HttpClientFactory::make($integration), $integration);
}

function firstMileQuery(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $q);

    return $q;
}

function firstMileAssertShop(string $method, string $path): void
{
    Http::assertSent(function ($req) use ($method, $path) {
        $q = firstMileQuery($req->url());

        return $req->method() === $method
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

describe('FirstMileMethods — listas GET', function () use ($ok) {
    it('getChannelList / getCourierDeliveryChannelList mandam region quando informada', function () use ($ok) {
        Http::fake(['*' => $ok(['logistics_channel_list' => []])]);
        $c = firstMileClient();
        $c->getChannelList('CN');
        $c->getCourierDeliveryChannelList();
        firstMileAssertShop('GET', '/api/v2/first_mile/get_channel_list');
        firstMileAssertShop('GET', '/api/v2/first_mile/get_courier_delivery_channel_list');
        Http::assertSent(fn ($req) => ! str_contains($req->url(), 'get_channel_list') || firstMileQuery($req->url())['region'] === 'CN');
        Http::assertSent(fn ($req) => ! str_contains($req->url(), 'courier_delivery_channel_list') || ! isset(firstMileQuery($req->url())['region']));
    });

    it('getTransitWarehouseList manda region + shipment_method', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/get_transit_warehouse_list*' => $ok()]);
        firstMileClient()->getTransitWarehouseList('CN', 'dropoff');
        firstMileAssertShop('GET', '/api/v2/first_mile/get_transit_warehouse_list');
        Http::assertSent(fn ($req) => firstMileQuery($req->url())['shipment_method'] === 'dropoff');
    });

    it('getDetail manda first_mile_tracking_number + cursor', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/get_detail*' => $ok(['order_list' => []])]);
        firstMileClient()->getDetail('CNF1', 'c2');
        firstMileAssertShop('GET', '/api/v2/first_mile/get_detail');
        Http::assertSent(fn ($req) => firstMileQuery($req->url())['first_mile_tracking_number'] === 'CNF1' && firstMileQuery($req->url())['cursor'] === 'c2');
    });

    it('getTrackingNumberList manda janela de datas + page_size, omite cursor vazio', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/get_tracking_number_list*' => $ok(['more' => false])]);
        firstMileClient()->getTrackingNumberList('2026-09-01', '2026-09-04', 20);
        firstMileAssertShop('GET', '/api/v2/first_mile/get_tracking_number_list');
        Http::assertSent(fn ($req) => firstMileQuery($req->url())['from_date'] === '2026-09-01' && firstMileQuery($req->url())['page_size'] === '20' && ! isset(firstMileQuery($req->url())['cursor']));
    });

    it('getUnbindOrderList manda optional fields', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/get_unbind_order_list*' => $ok(['order_list' => []])]);
        firstMileClient()->getUnbindOrderList(10, 'c1', ['logistics_status']);
        firstMileAssertShop('GET', '/api/v2/first_mile/get_unbind_order_list');
        Http::assertSent(fn ($req) => firstMileQuery($req->url())['response_optional_fields'] === 'logistics_status' && firstMileQuery($req->url())['cursor'] === 'c1');
    });

    it('getCourierDeliveryDetail manda binding_id + page_size', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/get_courier_delivery_detail*' => $ok()]);
        firstMileClient()->getCourierDeliveryDetail('BID1', 30);
        firstMileAssertShop('GET', '/api/v2/first_mile/get_courier_delivery_detail');
        Http::assertSent(fn ($req) => firstMileQuery($req->url())['binding_id'] === 'BID1' && firstMileQuery($req->url())['page_size'] === '30');
    });
});

describe('FirstMileMethods — geracao e vinculo', function () use ($ok) {
    it('generateFirstMileTrackingNumber limita quantity a 20', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/generate_first_mile_tracking_number*' => $ok(['first_mile_tracking_number_list' => ['CNF1']])]);
        $r = firstMileClient()->generateFirstMileTrackingNumber('2026-09-04', 50);
        expect($r['response']['first_mile_tracking_number_list'])->toBe(['CNF1']);
        firstMileAssertShop('POST', '/api/v2/first_mile/generate_first_mile_tracking_number');
        Http::assertSent(fn ($req) => $req->data() === ['declare_date' => '2026-09-04', 'quantity' => 20]);
    });

    it('bindFirstMileTrackingNumber manda obrigatorios + extras', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/bind_first_mile_tracking_number*' => $ok()]);
        firstMileClient()->bindFirstMileTrackingNumber('CNF1', 'dropoff', 'cn', 1001, [['order_sn' => 'A']], ['weight' => 1.2, 'warehouse_id' => 'W1']);
        firstMileAssertShop('POST', '/api/v2/first_mile/bind_first_mile_tracking_number');
        Http::assertSent(fn ($req) => $req->data()['first_mile_tracking_number'] === 'CNF1'
            && $req->data()['logistics_channel_id'] === 1001
            && $req->data()['order_list'] === [['order_sn' => 'A']]
            && $req->data()['weight'] === 1.2 && $req->data()['warehouse_id'] === 'W1');
    });

    it('unbindFirstMileTrackingNumber e unbindFirstMileTrackingNumberAll', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/unbind_first_mile_tracking_number*' => $ok()]);
        $c = firstMileClient();
        $c->unbindFirstMileTrackingNumber('CNF1', [['order_sn' => 'A']]);
        $c->unbindFirstMileTrackingNumberAll([['order_sn' => 'B']]);
        firstMileAssertShop('POST', '/api/v2/first_mile/unbind_first_mile_tracking_number');
        firstMileAssertShop('POST', '/api/v2/first_mile/unbind_first_mile_tracking_number_all');
        Http::assertSent(fn ($req) => ! str_ends_with((string) parse_url($req->url(), PHP_URL_PATH), '_all')
            ? $req->data() === ['first_mile_tracking_number' => 'CNF1', 'order_list' => [['order_sn' => 'A']]]
            : $req->data() === ['order_list' => [['order_sn' => 'B']]]);
    });

    it('generateAndBindFirstMileTrackingNumber manda courier_delivery_info', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/generate_and_bind_first_mile_tracking_number*' => $ok(['binding_id' => 'BID1'])]);
        firstMileClient()->generateAndBindFirstMileTrackingNumber([['order_sn' => 'A']], ['address_id' => 1, 'warehouse_id' => 'W1'], region: 'CN');
        firstMileAssertShop('POST', '/api/v2/first_mile/generate_and_bind_first_mile_tracking_number');
        Http::assertSent(fn ($req) => $req->data()['shipment_method'] === 'courier_delivery' && $req->data()['region'] === 'CN' && $req->data()['courier_delivery_info']['warehouse_id'] === 'W1');
    });

    it('bindCourierDeliveryFirstMileTrackingNumber manda binding_id', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/bind_courier_delivery_first_mile_tracking_number*' => $ok()]);
        firstMileClient()->bindCourierDeliveryFirstMileTrackingNumber('BID1', [['order_sn' => 'A']]);
        firstMileAssertShop('POST', '/api/v2/first_mile/bind_courier_delivery_first_mile_tracking_number');
        Http::assertSent(fn ($req) => $req->data() === ['shipment_method' => 'courier_delivery', 'binding_id' => 'BID1', 'order_list' => [['order_sn' => 'A']]]);
    });

    it('getCourierDeliveryTrackingNumberList e POST com datas no body', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/get_courier_delivery_tracking_number_list*' => $ok()]);
        firstMileClient()->getCourierDeliveryTrackingNumberList('2026-09-01', '2026-09-04', 10, 'c1');
        firstMileAssertShop('POST', '/api/v2/first_mile/get_courier_delivery_tracking_number_list');
        Http::assertSent(fn ($req) => $req->data() === ['from_date' => '2026-09-01', 'to_date' => '2026-09-04', 'page_size' => 10, 'cursor' => 'c1']);
    });
});

describe('FirstMileMethods — waybills', function () use ($ok) {
    it('getWaybill devolve binario', function () {
        Http::fake(['*/api/v2/first_mile/get_waybill*' => Http::response('%PDF-fm', 200, ['Content-Type' => 'application/pdf'])]);
        expect(firstMileClient()->getWaybill(['CNF1', 'CNF2']))->toBe('%PDF-fm');
        firstMileAssertShop('POST', '/api/v2/first_mile/get_waybill');
        Http::assertSent(fn ($req) => $req->data() === ['first_mile_tracking_number_list' => ['CNF1', 'CNF2']]);
    });

    it('getWaybill com erro JSON em HTTP 200 lanca ShopeeRequestException', function () {
        Http::fake(['*/api/v2/first_mile/get_waybill*' => Http::response(['error' => 'error_param', 'message' => 'x'], 200)]);
        expect(fn () => firstMileClient()->getWaybill(['CNF1']))->toThrow(ShopeeRequestException::class);
    });

    it('getCourierDeliveryWaybill devolve JSON com waybill_list', function () use ($ok) {
        Http::fake(['*/api/v2/first_mile/get_courier_delivery_waybill*' => $ok(['waybill_list' => [['binding_id' => 'BID1', 'shipping_label_url' => 'http://x']]])]);
        $r = firstMileClient()->getCourierDeliveryWaybill(['BID1']);
        expect($r['response']['waybill_list'][0]['shipping_label_url'])->toBe('http://x');
        firstMileAssertShop('POST', '/api/v2/first_mile/get_courier_delivery_waybill');
        Http::assertSent(fn ($req) => $req->data() === ['binding_id_list' => ['BID1']]);
    });
});
