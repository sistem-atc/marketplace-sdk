<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttFulfillmentIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: [
            'app_key' => 'ak',
            'app_secret' => 'as',
            'shop_id' => 'shop1',
            'shop_cipher' => 'cipher1',
        ],
        active: true,
        expired: false,
    );
}

describe('MarketPlaces::Tiktok()->fulfillment()', function () {
    it('sobe a nota em /fulfillment/202502/invoice/upload — NAO em /finance', function () {
        // Esta e' a descoberta que motivou o metodo: o caminho de nota do
        // TikTok mora sob FULFILLMENT. O `invoice()->getByOrderId()` aponta
        // pra /finance/{v}/orders/{id}/invoices, que devolve "Invalid path"
        // em toda versao — codigo morto.
        Http::fake([
            'open-api.tiktokglobalshop.com/fulfillment/202502/invoice/upload*' => Http::response([
                'code' => 0, 'data' => [],
            ]),
        ]);

        $resp = MarketPlaces::Tiktok()->fulfillment(ttFulfillmentIntegration())
            ->uploadInvoiceXml('PKG1', ['ORD1', 'ORD2'], '<nfeProc/>');

        expect($resp->errors)->toBeNull();

        Http::assertSent(function ($req) {
            $body = json_decode($req->body(), true);

            return $req->method() === 'POST'
                && str_contains($req->url(), '/fulfillment/202502/invoice/upload')
                && ! str_contains($req->url(), '/finance/')
                // XML vai em BASE64 no corpo JSON — nao e' multipart.
                && $body['invoices'][0]['file'] === base64_encode('<nfeProc/>')
                && $body['invoices'][0]['file_type'] === 'XML'
                && $body['invoices'][0]['package_id'] === 'PKG1'
                && $body['invoices'][0]['order_ids'] === ['ORD1', 'ORD2'];
        });
    });

    it('recusa nota acima de 1MB em base64 antes de gastar a chamada', function () {
        Http::fake();

        MarketPlaces::Tiktok()->fulfillment(ttFulfillmentIntegration())
            ->uploadInvoice([[
                'package_id' => 'PKG1',
                'order_ids' => ['ORD1'],
                'file_type' => 'XML',
                'file' => str_repeat('A', 1024 * 1024 + 1),
            ]]);
    })->throws(InvalidArgumentException::class, 'excede 1MB');

    it('getPackageDetail usa GET /fulfillment/202309/packages/{id}', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fulfillment/202309/packages/PKG1*' => Http::response([
                'code' => 0,
                'data' => json_decode(
                    file_get_contents(__DIR__.'/../Fixtures/Tiktok/get-package-detail.json'),
                    true,
                ),
            ]),
        ]);

        $dto = MarketPlaces::Tiktok()->fulfillment(ttFulfillmentIntegration())->getPackageDetail('PKG1');

        expect($dto?->packageStatus)->toBe('PROCESSING')
            ->and($dto?->weight?->value)->toBe('1.2');

        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_contains($req->url(), '/fulfillment/202309/packages/PKG1'));
    });

    it('searchPackages manda filtro de tempo no CORPO e paginacao na QUERY', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fulfillment/202309/packages/search*' => Http::response([
                'code' => 0, 'data' => ['packages' => [], 'total_count' => 0],
            ]),
        ]);

        MarketPlaces::Tiktok()->fulfillment(ttFulfillmentIntegration())
            ->searchPackages(createTimeGe: 1700000000, createTimeLt: 1700086400, pageSize: 500);

        Http::assertSent(function ($req) {
            $body = json_decode($req->body(), true);

            return str_contains($req->url(), 'page_size=50')      // teto de 50 aplicado
                && $body['create_time_ge'] === 1700000000
                // filtros nulos NAO viajam: o TikTok recusa null
                && ! array_key_exists('update_time_ge', $body)
                && ! array_key_exists('package_status', $body);
        });
    });

    it('shipPackage devolve bool porque a API responde data vazio', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fulfillment/202309/packages/PKG1/ship*' => Http::response([
                'code' => 0, 'data' => [],
            ]),
        ]);

        $ok = MarketPlaces::Tiktok()->fulfillment(ttFulfillmentIntegration())
            ->shipPackage('PKG1', selfShipment: [
                'tracking_number' => 'TN1',
                'shipping_provider_id' => 'SP1',
            ]);

        expect($ok)->toBeTrue();

        Http::assertSent(function ($req) {
            $body = json_decode($req->body(), true);

            return str_contains($req->url(), '/packages/PKG1/ship')
                && $body['self_shipment']['tracking_number'] === 'TN1'
                // handover_method/pickup_slot nao informados nao vao no corpo
                && ! array_key_exists('handover_method', $body);
        });
    });

    it('getOrderSplitAttributes manda os order_ids por VIRGULA na query', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fulfillment/202309/orders/split_attributes*' => Http::response([
                'code' => 0, 'data' => ['split_attributes' => []],
            ]),
        ]);

        MarketPlaces::Tiktok()->fulfillment(ttFulfillmentIntegration())
            ->getOrderSplitAttributes(['O1', 'O2']);

        // Mesma convencao do `ids` do Get Order Detail.
        Http::assertSent(fn ($req) => str_contains(urldecode($req->url()), 'order_ids=O1,O2'));
    });

    it('bundle DROP_OFF sem transportadora/rastreio falha antes da chamada', function () {
        Http::fake();

        MarketPlaces::Tiktok()->fulfillment(ttFulfillmentIntegration())
            ->createFirstMileBundle(['O1'], 'DROP_OFF');
    })->throws(InvalidArgumentException::class, 'DROP_OFF exige');

    it('as duas versoes do bundle batem em caminhos diferentes', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fulfillment/*' => Http::response([
                'code' => 0, 'data' => ['first_mile_bundle_id' => 'BA1'],
            ]),
        ]);

        $fulfillment = MarketPlaces::Tiktok()->fulfillment(ttFulfillmentIntegration());
        $fulfillment->createFirstMileBundle(['O1'], 'PICKUP');
        $fulfillment->createFirstMileBundleV2(['O1'], 'PICKUP');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/fulfillment/202407/bundles'));
        Http::assertSent(fn ($req) => str_contains($req->url(), '/fulfillment/202510/first_mile_bundle'));
    });

    it('getPodDetail do fulfillment nao colide com o da Order API', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fulfillment/202606/pod_details/get*' => Http::response([
                'code' => 0,
                'data' => ['pod_details' => [['pod_info_json' => '{}', 'order_line_id' => 'L1']]],
            ]),
        ]);

        $dto = MarketPlaces::Tiktok()->fulfillment(ttFulfillmentIntegration())->getPodDetail('ORD1', [[
            'order_line_id' => 'L1',
            'product_id' => 'P1',
            'sku_id' => 'S1',
            'pod_order_data_id' => 'D1',
        ]]);

        expect($dto->podDetails[0]->podInfoJson)->toBe('{}');

        // O outro POD vive em /order/202606/pod_details/search.
        Http::assertSent(fn ($req) => str_contains($req->url(), '/fulfillment/202606/pod_details/get'));
    });

    it('upload de comprovante vai POST multipart, mesmo a doc dizendo GET', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fulfillment/202309/files/upload*' => Http::response([
                'code' => 0, 'data' => ['url' => 'https://x/y', 'name' => 'pod.pdf'],
            ]),
        ]);

        $dto = MarketPlaces::Tiktok()->fulfillment(ttFulfillmentIntegration())
            ->uploadDeliveryFile('%PDF-1.4', 'pod.pdf');

        expect($dto->url)->toBe('https://x/y');

        Http::assertSent(function ($req) {
            return $req->method() === 'POST'
                && str_contains($req->url(), '/fulfillment/202309/files/upload')
                // assinatura continua na query mesmo sem corpo JSON
                && str_contains($req->url(), 'sign=')
                && str_contains((string) $req->header('Content-Type')[0], 'multipart/form-data');
        });
    });

    it('operacoes de lote recusam lista vazia', function () {
        Http::fake();
        $fulfillment = MarketPlaces::Tiktok()->fulfillment(ttFulfillmentIntegration());

        expect(fn () => $fulfillment->batchShipPackages([]))->toThrow(InvalidArgumentException::class)
            ->and(fn () => $fulfillment->combinePackages([]))->toThrow(InvalidArgumentException::class)
            ->and(fn () => $fulfillment->uploadInvoice([]))->toThrow(InvalidArgumentException::class)
            ->and(fn () => $fulfillment->updatePackageDeliveryStatus([]))->toThrow(InvalidArgumentException::class)
            ->and(fn () => $fulfillment->uploadCustomsInformation([]))->toThrow(InvalidArgumentException::class)
            ->and(fn () => $fulfillment->getOrderCustomsRequirements([]))->toThrow(InvalidArgumentException::class)
            ->and(fn () => $fulfillment->getOrderSplitAttributes([]))->toThrow(InvalidArgumentException::class)
            ->and(fn () => $fulfillment->getPodDetail('O1', []))->toThrow(InvalidArgumentException::class)
            ->and(fn () => $fulfillment->redeemInfoCallback('O1', []))->toThrow(InvalidArgumentException::class);

        Http::assertNothingSent();
    });
});
