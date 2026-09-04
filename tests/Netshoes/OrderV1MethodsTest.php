<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Netshoes\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

const NETSHOES_ORDER_V1_BASE = 'https://api-marketplace.netshoes.com.br';

beforeEach(function () {
    Http::preventStrayRequests();

    config([
        'marketplaces.netshoes.api_base' => NETSHOES_ORDER_V1_BASE,
        'marketplaces.netshoes.sandbox_base' => 'http://api-sandbox.netshoes.com.br',
    ]);
});

function netshoesOrderV1Integration(): FakeIntegration
{
    return new FakeIntegration(accessToken: 'tok-v1', settings: ['client_id' => 'cli-v1']);
}

function netshoesOrderV1(): OrderMethods
{
    return MarketPlaces::Netshoes()->orders(netshoesOrderV1Integration());
}

/** Auth (client_id + access_token) + verbo + URL exata. */
function netshoesOrderV1Sent(string $method, string $url, ?array $body = null, array $headers = []): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $body, $headers) {
        if ($req->method() !== $method || urldecode($req->url()) !== $url) {
            return false;
        }
        if (! $req->hasHeader('client_id', 'cli-v1') || ! $req->hasHeader('access_token', 'tok-v1')) {
            return false;
        }
        foreach ($headers as $k => $v) {
            if (! $req->hasHeader($k, $v)) {
                return false;
            }
        }

        return $body === null || $req->data() === $body;
    });
}

describe('Orders V1 — /api/v1/orders (Swagger)', function () {
    it('listOrdersV1: GET com filtros e header idSeller', function () {
        Http::fake(['*' => Http::response(['items' => [], 'links' => []])]);

        $r = netshoesOrderV1()->listOrdersV1([
            'page' => 1, 'size' => 20, 'orderStartDate' => '2026-09-01T00:00:00Z', 'orderStatus' => 'approved',
        ], idSeller: '77');

        expect($r)->toHaveKey('items');
        netshoesOrderV1Sent(
            'GET',
            NETSHOES_ORDER_V1_BASE.'/api/v1/orders?page=1&size=20&orderStartDate=2026-09-01T00:00:00Z&orderStatus=approved',
            headers: ['idSeller' => '77'],
        );
    });

    it('listOrdersV1: sem idSeller nao manda o header', function () {
        Http::fake(['*' => Http::response(['items' => []])]);

        netshoesOrderV1()->listOrdersV1();

        Http::assertSent(fn (Request $req) => ! $req->hasHeader('idSeller')
            && urldecode($req->url()) === NETSHOES_ORDER_V1_BASE.'/api/v1/orders');
    });

    it('header idSeller nao vaza pra request seguinte (clone do client)', function () {
        Http::fake(['*' => Http::response(['items' => []])]);

        $m = netshoesOrderV1();
        $m->listOrdersV1(idSeller: '77');
        $m->listOrdersV1();

        Http::assertSentCount(2);
        Http::assertSent(fn (Request $req) => ! $req->hasHeader('idSeller'));
    });

    it('getOrderV1: GET /orders/{n} com expand e rawurlencode', function () {
        Http::fake(['*' => Http::response(['orderNumber' => 'A/1'])]);

        netshoesOrderV1()->getOrderV1('A/1', ['shippings', 'items']);

        Http::assertSent(fn (Request $req) => $req->method() === 'GET'
            && $req->url() === NETSHOES_ORDER_V1_BASE.'/api/v1/orders/A%2F1?expand=shippings%2Citems');
    });

    it('getOrderV1: sem expand nao manda query', function () {
        Http::fake(['*' => Http::response(['orderNumber' => '1'])]);

        netshoesOrderV1()->getOrderV1('1');

        netshoesOrderV1Sent('GET', NETSHOES_ORDER_V1_BASE.'/api/v1/orders/1');
    });

    it('saveOrder: POST /orders com OrderResource (sandbox)', function () {
        Http::fake(['*' => Http::response(['orderNumber' => 'SB1'], 201)]);

        $body = ['orderNumber' => 'SB1', 'orderStatus' => 'approved', 'totalGross' => 10.5];
        netshoesOrderV1()->saveOrder($body);

        netshoesOrderV1Sent('POST', NETSHOES_ORDER_V1_BASE.'/api/v1/orders', $body);
    });

    it('listShippings: GET /orders/{n}/shippings?expand', function () {
        Http::fake(['*' => Http::response(['items' => []])]);

        netshoesOrderV1()->listShippings('123', ['items']);

        netshoesOrderV1Sent('GET', NETSHOES_ORDER_V1_BASE.'/api/v1/orders/123/shippings?expand=items');
    });

    it('getShipping: GET /orders/{n}/shippings/{code}', function () {
        Http::fake(['*' => Http::response(['shippingCode' => 99])]);

        netshoesOrderV1()->getShipping('123', 99);

        netshoesOrderV1Sent('GET', NETSHOES_ORDER_V1_BASE.'/api/v1/orders/123/shippings/99');
    });

    it('updateShippingStatusToApproved: PUT status/approved com body default', function () {
        Http::fake(['*' => Http::response([])]);

        netshoesOrderV1()->updateShippingStatusToApproved('123', 99);

        netshoesOrderV1Sent('PUT', NETSHOES_ORDER_V1_BASE.'/api/v1/orders/123/shippings/99/status/approved', ['status' => 'approved']);
    });

    it('updateShippingStatusToCanceled: PUT status/canceled com reasonCancellationCode + idSeller', function () {
        Http::fake(['*' => Http::response([])]);

        $body = ['reasonCancellationCode' => 3, 'canceledBy' => 'seller'];
        netshoesOrderV1()->updateShippingStatusToCanceled('123', '99', $body, idSeller: '77');

        netshoesOrderV1Sent(
            'PUT',
            NETSHOES_ORDER_V1_BASE.'/api/v1/orders/123/shippings/99/status/canceled',
            $body,
            ['idSeller' => '77'],
        );
    });

    it('updateShippingStatusToDelivered: PUT status/delivered', function () {
        Http::fake(['*' => Http::response([])]);

        $body = ['deliveryDate' => '2026-09-03T10:00:00Z'];
        netshoesOrderV1()->updateShippingStatusToDelivered('123', 99, $body);

        netshoesOrderV1Sent('PUT', NETSHOES_ORDER_V1_BASE.'/api/v1/orders/123/shippings/99/status/delivered', $body);
    });

    it('updateShippingStatusToInvoiced: PUT status/invoiced com dados da NF-e', function () {
        Http::fake(['*' => Http::response([])]);

        $body = ['number' => '1234', 'line' => '2', 'key' => str_repeat('3', 44), 'issueDate' => '2026-09-03', 'volume' => 1];
        netshoesOrderV1()->updateShippingStatusToInvoiced('123', 99, $body);

        netshoesOrderV1Sent('PUT', NETSHOES_ORDER_V1_BASE.'/api/v1/orders/123/shippings/99/status/invoiced', $body);
    });

    it('updateShippingStatusToShipped: PUT status/shipped com rastreio', function () {
        Http::fake(['*' => Http::response([])]);

        $body = ['carrier' => 'Correios', 'trackingNumber' => 'BR123', 'trackingLink' => 'https://t/BR123'];
        netshoesOrderV1()->updateShippingStatusToShipped('123', 99, $body);

        netshoesOrderV1Sent('PUT', NETSHOES_ORDER_V1_BASE.'/api/v1/orders/123/shippings/99/status/shipped', $body);
    });

    it('createShippingTags: POST /orders/shipping-tags com PickupRequestResource', function () {
        Http::fake(['*' => Http::response(['trackingGroupNumber' => 'TG1'])]);

        netshoesOrderV1()->createShippingTags([99, 100]);

        netshoesOrderV1Sent('POST', NETSHOES_ORDER_V1_BASE.'/api/v1/orders/shipping-tags', [
            'documentType' => 'PDF', 'shippingCodes' => [99, 100],
        ]);
    });

    it('getCancellationReasons: GET /orders/cancellation-reasons', function () {
        Http::fake(['*' => Http::response([['code' => '1', 'description' => 'Sem estoque', 'canBePenalty' => true]])]);

        $r = netshoesOrderV1()->getCancellationReasons();

        expect($r[0]['code'])->toBe('1');
        netshoesOrderV1Sent('GET', NETSHOES_ORDER_V1_BASE.'/api/v1/orders/cancellation-reasons');
    });

    it('metodos V2 historicos continuam em /api/v2 (backward-compatible)', function () {
        Http::fake(['*' => Http::response(['orderNumber' => '1'])]);

        netshoesOrderV1()->getOrder('1', []);

        netshoesOrderV1Sent('GET', NETSHOES_ORDER_V1_BASE.'/api/v2/orders/1');
    });

    it('usa base sandbox http quando environment=sandbox', function () {
        Http::fake(['*' => Http::response([])]);

        MarketPlaces::Netshoes()
            ->orders(new FakeIntegration(accessToken: 'tok-v1', settings: ['client_id' => 'cli-v1', 'environment' => 'sandbox']))
            ->getCancellationReasons();

        Http::assertSent(fn (Request $req) => $req->url() === 'http://api-sandbox.netshoes.com.br/api/v1/orders/cancellation-reasons');
    });
});
