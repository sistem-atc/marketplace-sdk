<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\PodDetailsResponseDTO;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function podIntegration(): FakeIntegration
{
    return new FakeIntegration(
        settings: ['app_key' => 'ak', 'app_secret' => 'as', 'shop_cipher' => 'cipher1'],
    );
}

function fixturePodDetails(): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/pod-details.json'),
        true,
    );
}

describe('getPodDetails', function () {
    it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
        $payload = fixturePodDetails();

        $perdidos = array_diff(
            array_keys($payload),
            array_keys(PodDetailsResponseDTO::fromArray($payload)->toArray()),
        );

        expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
    });

    it('preserva status_code 0 — segundo codigo, DENTRO do data', function () {
        // 0 nao pode sumir na serializacao: o envelope pode vir code 0 e este
        // aqui diferente de 0. Sao dois codigos independentes.
        $dto = PodDetailsResponseDTO::fromArray(fixturePodDetails());

        expect($dto->statusCode)->toBe(0)
            ->and($dto->toArray())->toHaveKey('status_code');
    });

    it('entrega o pod_detail como STRING crua, nao decodificada', function () {
        // O schema de dentro varia por produto; decodificar aqui seria chutar.
        $dto = PodDetailsResponseDTO::fromArray(fixturePodDetails());

        expect($dto->podDetails)->toHaveCount(1)
            ->and($dto->podDetails[0]->orderLineId)->toBe('577457287176491132')
            ->and($dto->podDetails[0]->podDetail)->toBeString()
            ->and($dto->podDetails[0]->podDetail)->toBe('pod_info_json');
    });

    it('manda main_order_id e as linhas no CORPO da 202606', function () {
        Http::fake([
            '*/order/202606/pod_details/search*' => Http::response([
                'code' => 0, 'message' => 'Success', 'data' => fixturePodDetails(),
            ]),
        ]);

        MarketPlaces::Tiktok()->orders(podIntegration())->getPodDetails('577449529742299260', [
            [
                'order_line_id' => '577449529742430332',
                'product_id' => '1732453831920750867',
                'sku_id' => '1732453830297817363',
                'pod_order_data_id' => '122343',
            ],
        ]);

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/order/202606/pod_details/search')
                && $req['main_order_id'] === '577449529742299260'
                && $req['query_order_lines'][0]['pod_order_data_id'] === '122343';
        });
    });

    it('recusa consulta sem nenhuma linha', function () {
        Http::fake();

        MarketPlaces::Tiktok()->orders(podIntegration())->getPodDetails('1', []);
    })->throws(InvalidArgumentException::class, 'ao menos uma linha');
});

describe('updateBlindBoxOpeningResults', function () {
    it('devolve o envelope cru porque data vem VAZIO por contrato', function () {
        Http::fake([
            '*/order/202605/orders/blind_box_result/callback*' => Http::response([
                'code' => 0,
                'message' => 'Success',
                'data' => [],
                'request_id' => '202203070749000101890810281E8C70B7',
            ]),
        ]);

        $envelope = MarketPlaces::Tiktok()->orders(podIntegration())->updateBlindBoxOpeningResults(
            '576461413038785752',
            [[
                'order_line_id' => '577086512123755123',
                'blind_result_product_id' => '1729592969712207008',
                'blind_result_sku_id' => '1729592969712207009',
                'blind_open_time' => 1623812664,
            ]],
        );

        // Sem DTO: o unico dado util e' o request_id, pra rastreio no suporte.
        expect($envelope['request_id'])->toBe('202203070749000101890810281E8C70B7')
            ->and($envelope['data'])->toBe([]);
    });

    it('nao envia a chave do formato que ficou vazio', function () {
        Http::fake([
            '*/blind_box_result/callback*' => Http::response(['code' => 0, 'data' => []]),
        ]);

        // Caixa em lote: varios sorteados na MESMA linha, brinde como item proprio.
        MarketPlaces::Tiktok()->orders(podIntegration())->updateBlindBoxOpeningResults(
            '576461413038785752',
            blindBatchBoxResults: [[
                'order_line_id' => '577086512123755123',
                'blind_box_order_line_list' => [
                    [
                        'blind_result_product_id' => '1729592969712207008',
                        'blind_result_sku_id' => '1729592969712207009',
                        'blind_open_time' => 1623812664,
                        'product_type' => 'GIFT',
                    ],
                ],
            ]],
        );

        Http::assertSent(function ($req) {
            $body = $req->data();

            return ! array_key_exists('blind_box_results', $body)
                && $body['blind_batch_box_results'][0]['blind_box_order_line_list'][0]['product_type'] === 'GIFT';
        });
    });

    it('recusa chamada com os dois formatos vazios — seria no-op', function () {
        Http::fake();

        MarketPlaces::Tiktok()->orders(podIntegration())->updateBlindBoxOpeningResults('1');
    })->throws(InvalidArgumentException::class, 'nao atualiza nada');
});
