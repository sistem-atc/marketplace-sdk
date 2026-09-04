<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function orderExtrasClient(): OrderMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new OrderMethods(HttpClientFactory::make($integration), $integration);
}

/** Conteudo de uma parte multipart pelo name. */
function orderExtrasPart(\Illuminate\Http\Client\Request $req, string $name): ?string
{
    foreach ($req->data() as $part) {
        if (($part['name'] ?? null) === $name) return $part['contents'];
    }

    return null;
}

function orderExtrasQuery(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $q);

    return $q;
}

function orderExtrasAssertShop(string $method, string $path): void
{
    Http::assertSent(function ($req) use ($method, $path) {
        $q = orderExtrasQuery($req->url());

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

describe('OrderMethods extras — acoes no pedido', function () use ($ok) {
    it('setNote', function () use ($ok) {
        Http::fake(['*/api/v2/order/set_note*' => $ok()]);
        orderExtrasClient()->setNote('ORD1', 'separar');
        orderExtrasAssertShop('POST', '/api/v2/order/set_note');
        Http::assertSent(fn ($req) => $req->data() === ['order_sn' => 'ORD1', 'note' => 'separar']);
    });

    it('cancelOrder total (OUT_OF_STOCK exige item_list)', function () use ($ok) {
        Http::fake(['*/api/v2/order/cancel_order*' => $ok(['update_time' => 1])]);
        orderExtrasClient()->cancelOrder('ORD1', 'OUT_OF_STOCK', [['item_id' => 1, 'model_id' => 2]]);
        orderExtrasAssertShop('POST', '/api/v2/order/cancel_order');
        Http::assertSent(fn ($req) => $req->data() === ['order_sn' => 'ORD1', 'cancel_reason' => 'OUT_OF_STOCK', 'item_list' => [['item_id' => 1, 'model_id' => 2]]]);
    });

    it('cancelOrder parcial manda partial_cancel_item_list e omite item_list', function () use ($ok) {
        Http::fake(['*/api/v2/order/cancel_order*' => $ok()]);
        orderExtrasClient()->cancelOrder('ORD1', 'CUSTOMER_REQUEST', partialCancelItemList: [['item_id' => 1, 'model_id' => 2, 'model_quantity' => 1]]);
        Http::assertSent(fn ($req) => ! isset($req->data()['item_list']) && $req->data()['partial_cancel_item_list'][0]['model_quantity'] === 1);
    });

    it('getEstimateCancelValue', function () use ($ok) {
        Http::fake(['*/api/v2/order/get_estimate_cancel_value*' => $ok(['refund_amount' => 10])]);
        $r = orderExtrasClient()->getEstimateCancelValue('ORD1', [['item_id' => 1, 'model_id' => 2, 'model_quantity' => 1]]);
        expect($r['response']['refund_amount'])->toBe(10);
        orderExtrasAssertShop('POST', '/api/v2/order/get_estimate_cancel_value');
        Http::assertSent(fn ($req) => $req->data()['order_sn'] === 'ORD1' && count($req->data()['partial_cancel_item_list']) === 1);
    });

    it('handleBuyerCancellation normaliza operation em maiusculo', function () use ($ok) {
        Http::fake(['*/api/v2/order/handle_buyer_cancellation*' => $ok()]);
        orderExtrasClient()->handleBuyerCancellation('ORD1', 'accept');
        orderExtrasAssertShop('POST', '/api/v2/order/handle_buyer_cancellation');
        Http::assertSent(fn ($req) => $req->data() === ['order_sn' => 'ORD1', 'operation' => 'ACCEPT']);
    });

    it('splitOrder e unsplitOrder', function () use ($ok) {
        Http::fake(['*/api/v2/order/*split_order*' => $ok()]);
        $c = orderExtrasClient();
        $c->splitOrder('ORD1', [['item_list' => [['item_id' => 1, 'model_id' => 2, 'model_quantity' => 1]]]]);
        $c->unsplitOrder('ORD1');
        orderExtrasAssertShop('POST', '/api/v2/order/split_order');
        orderExtrasAssertShop('POST', '/api/v2/order/unsplit_order');
        Http::assertSent(fn ($req) => ! str_contains($req->url(), '/order/split_order') || $req->data()['package_list'][0]['item_list'][0]['item_id'] === 1);
        Http::assertSent(fn ($req) => ! str_contains($req->url(), 'unsplit_order') || $req->data() === ['order_sn' => 'ORD1']);
    });

    it('handlePrescriptionCheck manda is_approved + extras', function () use ($ok) {
        Http::fake(['*/api/v2/order/handle_prescription_check*' => $ok()]);
        orderExtrasClient()->handlePrescriptionCheck('ORD1', false, ['reject_reason_code' => 5, 'free_text' => 'ilegivel']);
        orderExtrasAssertShop('POST', '/api/v2/order/handle_prescription_check');
        Http::assertSent(fn ($req) => $req->data() === ['order_sn' => 'ORD1', 'is_approved' => false, 'reject_reason_code' => 5, 'free_text' => 'ilegivel']);
    });
});

describe('OrderMethods extras — listagens', function () use ($ok) {
    it('getShipmentList limita page_size a 100 e omite cursor vazio', function () use ($ok) {
        Http::fake(['*/api/v2/order/get_shipment_list*' => $ok(['order_list' => [], 'more' => false])]);
        orderExtrasClient()->getShipmentList(500);
        orderExtrasAssertShop('GET', '/api/v2/order/get_shipment_list');
        Http::assertSent(fn ($req) => orderExtrasQuery($req->url())['page_size'] === '100' && ! isset(orderExtrasQuery($req->url())['cursor']));
    });

    it('getPendingBuyerInvoiceOrderList manda cursor', function () use ($ok) {
        Http::fake(['*/api/v2/order/get_pending_buyer_invoice_order_list*' => $ok(['order_sn_list' => []])]);
        orderExtrasClient()->getPendingBuyerInvoiceOrderList(50, 'c9');
        orderExtrasAssertShop('GET', '/api/v2/order/get_pending_buyer_invoice_order_list');
        Http::assertSent(fn ($req) => orderExtrasQuery($req->url())['cursor'] === 'c9' && orderExtrasQuery($req->url())['page_size'] === '50');
    });

    it('getBuyerInvoiceInfo manda queries[] e devolve envelope na raiz', function () {
        Http::fake(['*/api/v2/order/get_buyer_invoice_info*' => Http::response(['error' => '', 'message' => '', 'invoice_info_list' => [['order_sn' => 'A']]])]);
        $r = orderExtrasClient()->getBuyerInvoiceInfo(['A', 'B']);
        expect($r['invoice_info_list'][0]['order_sn'])->toBe('A');
        orderExtrasAssertShop('POST', '/api/v2/order/get_buyer_invoice_info');
        Http::assertSent(fn ($req) => $req->data() === ['queries' => [['order_sn' => 'A'], ['order_sn' => 'B']]]);
    });

    it('getBookingList manda janela, status e page_size', function () use ($ok) {
        Http::fake(['*/api/v2/order/get_booking_list*' => $ok(['booking_list' => [], 'more' => false, 'next_cursor' => ''])]);
        orderExtrasClient()->getBookingList(1000, 2000, 'update_time', 'SHIPPED', 20, 'c1');
        orderExtrasAssertShop('GET', '/api/v2/order/get_booking_list');
        Http::assertSent(function ($req) {
            $q = orderExtrasQuery($req->url());

            return $q['time_range_field'] === 'update_time' && $q['time_from'] === '1000' && $q['time_to'] === '2000'
                && $q['booking_status'] === 'SHIPPED' && $q['page_size'] === '20' && $q['cursor'] === 'c1';
        });
    });

    it('searchPackageList monta filter/pagination/sort', function () use ($ok) {
        Http::fake(['*/api/v2/order/search_package_list*' => $ok(['package_list' => []])]);
        orderExtrasClient()->searchPackageList(['package_status' => 1], 30, 'c2', ['sort_type' => 1, 'ascending' => false]);
        orderExtrasAssertShop('POST', '/api/v2/order/search_package_list');
        Http::assertSent(fn ($req) => $req->data() === [
            'pagination' => ['page_size' => 30, 'cursor' => 'c2'],
            'filter' => ['package_status' => 1],
            'sort' => ['sort_type' => 1, 'ascending' => false],
        ]);
    });

    it('getWarehouseFilterConfig e GET sem parametros', function () use ($ok) {
        Http::fake(['*/api/v2/order/get_warehouse_filter_config*' => $ok()]);
        orderExtrasClient()->getWarehouseFilterConfig();
        orderExtrasAssertShop('GET', '/api/v2/order/get_warehouse_filter_config');
    });

    it('getPackageDetail junta package_number_list por virgula e devolve [] com lista vazia', function () use ($ok) {
        Http::fake(['*/api/v2/order/get_package_detail*' => $ok(['package_list' => [['package_number' => 'P1']]])]);
        $c = orderExtrasClient();
        expect($c->getPackageDetail([]))->toBe([]);
        $r = $c->getPackageDetail(['P1', 'P2']);
        expect($r['response']['package_list'][0]['package_number'])->toBe('P1');
        orderExtrasAssertShop('GET', '/api/v2/order/get_package_detail');
        Http::assertSent(fn ($req) => orderExtrasQuery($req->url())['package_number_list'] === 'P1,P2');
        Http::assertSentCount(1);
    });
});

describe('OrderMethods extras — upload de NF', function () {
    it('uploadInvoiceDoc sobe multipart com order_sn, file_type e file', function () {
        Http::fake(['*/api/v2/order/upload_invoice_doc*' => Http::response(['error' => '', 'message' => '', 'request_id' => 'r'])]);
        $r = orderExtrasClient()->uploadInvoiceDoc('ORD1', 4, '<nfe/>', 'nfe.xml');
        expect($r['error'])->toBe('');
        orderExtrasAssertShop('POST', '/api/v2/order/upload_invoice_doc');
        Http::assertSent(fn ($req) => $req->isMultipart()
            && $req->hasFile('file', '<nfe/>', 'nfe.xml')
            && orderExtrasPart($req, 'order_sn') === 'ORD1'
            && orderExtrasPart($req, 'file_type') === '4');
    });

    it('uploadInvoiceDoc com erro de negocio lanca ShopeeRequestException', function () {
        Http::fake(['*/api/v2/order/upload_invoice_doc*' => Http::response(['error' => 'error_param', 'message' => 'too big'])]);
        expect(fn () => orderExtrasClient()->uploadInvoiceDoc('ORD1', 1, 'x', 'a.pdf'))->toThrow(ShopeeRequestException::class);
    });

    it('DETAIL_FIELDS continua intocado (guarda de regressao do incidente 2026-06-08)', function () {
        $const = (new ReflectionClass(OrderMethods::class))->getConstant('DETAIL_FIELDS');
        expect($const)->toContain('total_amount', 'fulfillment_flag', 'payment_method', 'shipping_carrier', 'package_list')
            ->and(count($const))->toBe(30);
    });
});
