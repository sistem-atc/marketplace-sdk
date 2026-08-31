<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttFbtIntegration(): FakeIntegration
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

function ttFbt()
{
    return MarketPlaces::Tiktok()->fbt(ttFbtIntegration());
}

describe('MarketPlaces::Tiktok()->fbt()', function () {
    it('remove o prefixo IBR do id da ordem de inbound', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fbt/*' => Http::response([
                'code' => 0, 'message' => 'Success', 'data' => ['inbound_orders' => []],
            ]),
        ]);

        // O painel e o `order_id_string` mostram "IBR<numero>", mas as APIs de
        // inbound so' engolem o numerico — mandar o prefixo derruba a chamada.
        ttFbt()->getInboundOrderDetails(['IBR123456', '789']);

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/fbt/202602/inbound_orders')
                && str_contains(urldecode($req->url()), 'order_ids=123456,789')
                && ! str_contains(urldecode($req->url()), 'IBR')
                && $req->method() === 'GET';
        });
    });

    it('recusa goods_ids e sku_ids juntos no search de inventario', function () {
        // A API aceita um OU outro; mandar os dois devolve resultado errado em
        // silencio, entao falhamos antes de gastar a chamada.
        expect(fn () => ttFbt()->searchInventory(goodsIds: ['1'], skuIds: ['2']))
            ->toThrow(InvalidArgumentException::class);
    });

    it('respeita os tetos de lote documentados', function () {
        expect(fn () => ttFbt()->getInboundOrderDetails(array_fill(0, 11, '1')))
            ->toThrow(InvalidArgumentException::class)
            ->and(fn () => ttFbt()->searchGoods(goodsIds: array_fill(0, 101, '1')))
            ->toThrow(InvalidArgumentException::class)
            ->and(fn () => ttFbt()->queryGoodsInventoryForMcf(array_fill(0, 51, '1')))
            ->toThrow(InvalidArgumentException::class)
            // MCF: o limite e' a SOMA das quantidades, nao o numero de linhas.
            ->and(fn () => ttFbt()->createMcfOrder('ext-1', [['id' => 'g1', 'quantity' => 51]], ['name' => 'x']))
            ->toThrow(InvalidArgumentException::class);
    });

    it('nao manda filtro vazio no corpo — o TikTok nao trata como sem filtro', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fbt/*' => Http::response([
                'code' => 0, 'message' => 'Success', 'data' => ['goods' => []],
            ]),
        ]);

        ttFbt()->searchGoods(goodsIds: ['86533997569'], pageSize: 100);

        Http::assertSent(function ($req) {
            $body = json_decode($req->body(), true);

            return str_contains($req->url(), '/fbt/202607/goods/search')
                && $body === ['goods_ids' => ['86533997569']]
                && str_contains($req->url(), 'page_size=100');
        });
    });

    it('mantem cada endpoint na SUA versao — nao existe versao unica no FBT', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fbt/*' => Http::response(['code' => 0, 'message' => 'Success', 'data' => []]),
        ]);

        $fbt = ttFbt();
        $fbt->getWarehouses();                 // 202408
        $fbt->getOnboardedRegions();           // 202409
        $fbt->searchInventoryRecords();        // 202410
        $fbt->getMerchantMcfStatus();          // 202601
        $fbt->listAvailableInboundMethods('p1'); // 202602
        $fbt->confirmInboundMethod('p1', 'po1', 'D2FC', '1774162800', '1774767600'); // 202603

        $paths = [];
        Http::assertSentCount(6);
        Http::recorded(function ($req) use (&$paths) {
            $paths[] = parse_url($req->url(), PHP_URL_PATH);

            return true;
        });

        expect($paths)->toBe([
            '/fbt/202408/warehouses',
            '/fbt/202409/merchants/onboarded_regions',
            '/fbt/202410/inventory_records/search',
            '/fbt/202601/merchants/mcf_status',
            '/fbt/202602/list_available_inbound_method',
            '/fbt/202603/confirm_inbound_method',
        ]);
    });

    it('devolve os timestamps da janela EXATAMENTE como string no corpo', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fbt/*' => Http::response(['code' => 0, 'message' => 'Success', 'data' => []]),
        ]);

        ttFbt()->getInboundMethodDetail('p1', 'ONE_HUB', '1774162800', '1774767600');

        Http::assertSent(function ($req) {
            // Int no JSON (1774162800 sem aspas) faz a API recusar: o valor tem
            // que voltar identico ao que o list devolveu.
            return str_contains($req->body(), '"start_timestamp":"1774162800"')
                && str_contains($req->body(), '"end_timestamp":"1774767600"');
        });
    });

    it('endpoints com data vazio devolvem true (o sucesso e o code 0)', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/fbt/*' => Http::response(['code' => 0, 'message' => 'Success', 'data' => []]),
        ]);

        expect(ttFbt()->cancelInboundOrder('IBR123', 'plano errado'))->toBeTrue()
            ->and(ttFbt()->updateInboundOrderTracking('123', [[
                'provider_name' => 'UPS', 'tracking_number' => 'TN1', 'carton_number' => 'C0001',
            ]]))->toBeTrue();
    });

    it('so aceita BIND ou UN_BIND no vinculo goods<->sku', function () {
        expect(fn () => ttFbt()->updateGoodsSkuRelation([['tts_goods_id' => 'g', 'tts_sku_ids' => ['s']]], 'bind'))
            ->toThrow(InvalidArgumentException::class);
    });
});
