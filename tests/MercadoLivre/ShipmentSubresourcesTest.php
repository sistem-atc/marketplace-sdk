<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Shipment\ShipmentMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function shipmentSubresourcesIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function shipmentSubresourcesMethods(): ShipmentMethods
{
    $integration = shipmentSubresourcesIntegration();

    return new ShipmentMethods(HttpClientFactory::make($integration), $integration);
}

beforeEach(function () {
    config([
        'marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.access_token_ttl_seconds' => 21600,
        'mercadolivre.default_site_id' => 'MLB',
    ]);
    Http::preventStrayRequests();
});

describe('ShipmentMethods sub-recursos GET com x-format-new', function () {
    it('items: GET /shipments/{id}/items', function () {
        Http::fake(['api.mercadolibre.com/shipments/123/items' => Http::response([['item_id' => 'MLB1', 'quantity' => 2]])]);

        $out = shipmentSubresourcesMethods()->items(123);

        expect($out[0]['item_id'])->toBe('MLB1');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipments/123/items'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && $req->hasHeader('x-format-new', 'true'));
    });

    it('costs: GET /shipments/{id}/costs', function () {
        Http::fake(['api.mercadolibre.com/shipments/123/costs' => Http::response(['gross_amount' => 24.55])]);

        expect(shipmentSubresourcesMethods()->costs(123)['gross_amount'])->toBe(24.55);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipments/123/costs'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && $req->hasHeader('x-format-new', 'true'));
    });

    it('payments: GET /shipments/{id}/payments', function () {
        Http::fake(['api.mercadolibre.com/shipments/123/payments' => Http::response([['payment_id' => 1, 'status' => 'approved']])]);

        expect(shipmentSubresourcesMethods()->payments(123)[0]['status'])->toBe('approved');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipments/123/payments'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && $req->hasHeader('x-format-new', 'true'));
    });

    it('sla: GET /shipments/{id}/sla sem header especial', function () {
        Http::fake(['api.mercadolibre.com/shipments/123/sla' => Http::response(['status' => 'on_time', 'service' => 'instant_gm'])]);

        expect(shipmentSubresourcesMethods()->sla(123)['service'])->toBe('instant_gm');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipments/123/sla'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && ! $req->hasHeader('x-format-new'));
    });

    it('delays: GET /shipments/{id}/delays', function () {
        Http::fake(['api.mercadolibre.com/shipments/123/delays' => Http::response(['shipment_id' => 123, 'delays' => [['type' => 'sla_delayed']]])]);

        expect(shipmentSubresourcesMethods()->delays(123)['delays'][0]['type'])->toBe('sla_delayed');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipments/123/delays'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && $req->hasHeader('x-format-new', 'true'));
    });

    it('delays: 404 (sem atraso) vira lista vazia', function () {
        Http::fake(['api.mercadolibre.com/shipments/123/delays' => Http::response(['message' => 'Delays Not Found for shipment'], 404)]);

        expect(shipmentSubresourcesMethods()->delays(123))->toBe(['shipment_id' => 123, 'delays' => []]);
    });

    it('leadTime: GET /shipments/{id}/lead_time', function () {
        Http::fake(['api.mercadolibre.com/shipments/123/lead_time' => Http::response(['cost' => 10])]);

        shipmentSubresourcesMethods()->leadTime(123);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipments/123/lead_time'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && $req->hasHeader('x-format-new', 'true'));
    });

    it('carrier: GET /shipments/{id}/carrier', function () {
        Http::fake(['api.mercadolibre.com/shipments/123/carrier' => Http::response(['name' => 'Total Express', 'url' => 'http://x'])]);

        expect(shipmentSubresourcesMethods()->carrier(123)['name'])->toBe('Total Express');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipments/123/carrier'
            && $req->hasHeader('x-format-new', 'true'));
    });

    it('orders: GET /shipments/{id}/orders com X-New-Domain', function () {
        Http::fake(['api.mercadolibre.com/shipments/123/orders' => Http::response([['id' => 2000001]])]);

        expect(shipmentSubresourcesMethods()->orders(123)[0]['id'])->toBe(2000001);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipments/123/orders'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && $req->hasHeader('X-New-Domain', 'true'));
    });

    it('statuses: GET /shipment_statuses', function () {
        Http::fake(['api.mercadolibre.com/shipment_statuses' => Http::response([['id' => 'pending', 'substatuses' => []]])]);

        expect(shipmentSubresourcesMethods()->statuses()[0]['id'])->toBe('pending');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipment_statuses'
            && $req->hasHeader('x-format-new', 'true'));
    });
});

describe('ShipmentMethods acoes POST', function () {
    it('split: POST /shipments/{id}/split com reason + packs', function () {
        Http::fake(['api.mercadolibre.com/shipments/123/split' => Http::response(['status' => 'ok'])]);

        $packs = [['orders' => [['id' => 20000001, 'quantity' => 2]]], ['orders' => [['id' => 20000002, 'quantity' => 1]]]];
        shipmentSubresourcesMethods()->split(123, 'DIMENSIONS_EXCEEDED', $packs);

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/shipments/123/split'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && $req->hasHeader('x-format-new', 'true')
            && $req['reason'] === 'DIMENSIONS_EXCEEDED'
            && $req['packs'] === $packs);
    });

    it('markReadyToShip: POST /shipments/{id}/process/ready_to_ship sem body', function () {
        Http::fake(['api.mercadolibre.com/shipments/43664723386/process/ready_to_ship' => Http::response(['status' => 200])]);

        expect(shipmentSubresourcesMethods()->markReadyToShip(43664723386)['status'])->toBe(200);
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/shipments/43664723386/process/ready_to_ship'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('sellerNotification: POST /v2/shipments/{id}/seller_notifications com substatus null e tracking', function () {
        Http::fake(['api.mercadolibre.com/v2/shipments/123/seller_notifications' => Http::response(['status' => 'OK'])]);

        $out = shipmentSubresourcesMethods()->sellerNotification(
            shipmentId: 123,
            status: 'delivered',
            substatus: null,
            payload: ['service_id' => 11, 'date' => '2026-08-10T14:44:52.252Z', 'comment' => 'entregue'],
            trackingNumber: 'TEST123',
            trackingUrl: 'https://track/TEST123',
        );

        expect($out['status'])->toBe('OK');
        Http::assertSent(function ($req) {
            $data = $req->data();

            return $req->method() === 'POST'
                && $req->url() === 'https://api.mercadolibre.com/v2/shipments/123/seller_notifications'
                && $req->hasHeader('Authorization', 'Bearer ml-bearer')
                && $data['status'] === 'delivered'
                && array_key_exists('substatus', $data) && $data['substatus'] === null
                && $data['payload']['service_id'] === 11
                && $data['tracking_number'] === 'TEST123'
                && $data['tracking_url'] === 'https://track/TEST123';
        });
    });

    it('sellerNotification: sem tracking nao manda as chaves de tracking', function () {
        Http::fake(['api.mercadolibre.com/v2/shipments/123/seller_notifications' => Http::response(['status' => 'OK'])]);

        shipmentSubresourcesMethods()->sellerNotification(123, 'shipped', 'in_transit', ['service_id' => 11, 'date' => '2026-08-10T14:44:52Z']);

        Http::assertSent(fn ($req) => ! array_key_exists('tracking_number', $req->data())
            && $req['substatus'] === 'in_transit');
    });
});
