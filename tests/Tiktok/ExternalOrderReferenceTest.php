<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\AddExternalOrderReferencesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\ExternalOrderSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Order\OrderMethods;

/**
 * Referencia externa = gravar a NOSSA chave dentro do pedido do TikTok e
 * depois buscar por ela. E' o substituto deterministico da heuristica de
 * valor/data que hoje resolve o vinculo nota-fiscal <-> pedido — e que erra na
 * maioria dos casos de nota duplicada.
 */
beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function extRefIntegration(): FakeIntegration
{
    return new FakeIntegration(
        settings: ['app_key' => 'ak', 'app_secret' => 'as', 'shop_cipher' => 'cipher1'],
    );
}

function fixtureAddExternalOrderReferences(): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/add-external-order-references.json'),
        true,
    );
}

function fixtureExternalOrderSearch(): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/search-order-by-external-order-reference.json'),
        true,
    );
}

describe('addExternalOrderReferences', function () {
    it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
        $payload = fixtureAddExternalOrderReferences();

        $perdidos = array_diff(
            array_keys($payload),
            array_keys(AddExternalOrderReferencesResponseDTO::fromArray($payload)->toArray()),
        );

        expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
    });

    it('expoe a falha PARCIAL que o envelope esconde atras de code 0', function () {
        // A armadilha: o TikTok responde "Success" mesmo tendo recusado
        // entradas do lote. Quem so' olha o envelope marca tudo como gravado.
        Http::fake([
            '*/order/202406/orders/external_orders*' => Http::response([
                'code' => 0,
                'message' => 'Success',
                'data' => fixtureAddExternalOrderReferences(),
            ]),
        ]);

        $dto = MarketPlaces::Tiktok()->orders(extRefIntegration())->addExternalOrderReferences([
            [
                'id' => '576461413038785752',
                'external_order' => [
                    'id' => '676461413038785752',
                    'platform' => 'ERP_SYSTEM',
                    'line_items' => [['id' => '1', 'origin_id' => '2']],
                ],
            ],
        ]);

        expect($dto->errors)->toHaveCount(1)
            // code do ERRO e' string; o do envelope e' int 0 (sucesso)
            ->and($dto->errors[0]->code)->toBe('36020001')
            ->and($dto->errors[0]->detail->orderId)->toBe('576461413038785752')
            ->and($dto->errors[0]->detail->externalOrder->id)->toBe('676461413038785752');
    });

    it('recusa platform fora do vocabulario fechado ANTES de gastar a chamada', function () {
        Http::fake();

        MarketPlaces::Tiktok()->orders(extRefIntegration())->addExternalOrderReferences([
            ['id' => '1', 'external_order' => ['id' => '2', 'platform' => 'BUNKER']],
        ]);
    })->throws(InvalidArgumentException::class, "Platform 'BUNKER' invalida");

    it('recusa lote acima de 100 pedidos', function () {
        Http::fake();

        $orders = array_fill(0, 101, [
            'id' => '1',
            'external_order' => ['id' => '2', 'platform' => 'ERP_SYSTEM'],
        ]);

        MarketPlaces::Tiktok()->orders(extRefIntegration())->addExternalOrderReferences($orders);
    })->throws(InvalidArgumentException::class, 'Max 100 pedidos por chamada');

    it('ERP_SYSTEM esta no vocabulario aceito — e o valor do nosso caso', function () {
        expect(OrderMethods::EXTERNAL_ORDER_PLATFORMS)->toContain('ERP_SYSTEM');
    });
});

describe('searchOrderByExternalOrderReference', function () {
    it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
        $payload = fixtureExternalOrderSearch();

        $perdidos = array_diff(
            array_keys($payload),
            array_keys(ExternalOrderSearchResponseDTO::fromArray($payload)->toArray()),
        );

        expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
    });

    it('devolve o id do TikTok a partir da NOSSA referencia, sem heuristica', function () {
        Http::fake([
            '*/order/202406/orders/external_order_search*' => Http::response([
                'code' => 0,
                'message' => 'Success',
                'data' => fixtureExternalOrderSearch(),
            ]),
        ]);

        $dto = MarketPlaces::Tiktok()->orders(extRefIntegration())
            ->searchOrderByExternalOrderReference('SHOPIFY', '676461413038785752');

        expect($dto->orders)->toHaveCount(1)
            ->and($dto->orders[0]->id)->toBe('576461413038785752')
            ->and($dto->orders[0]->externalOrder->id)->toBe('676461413038785752');

        // Quirk: e' POST, mas os filtros viajam na QUERY STRING.
        Http::assertSent(function ($req) {
            $url = urldecode($req->url());

            return str_contains($url, 'platform=SHOPIFY')
                && str_contains($url, 'external_order_id=676461413038785752');
        });
    });

    it('mantem os ids de linha NA MAO CERTA: id=nosso, originId=TikTok', function () {
        $dto = ExternalOrderSearchResponseDTO::fromArray(fixtureExternalOrderSearch());
        $item = $dto->orders[0]->externalOrder->lineItems[0];

        // Inverter os dois grava espelhado e a busca devolve vazio sem erro.
        expect($item->id)->toBe('577086512123755123')
            ->and($item->originId)->toBe('677086512123755123');
    });

    it('lista vazia nao e erro — e o pedido sem referencia gravada', function () {
        $dto = ExternalOrderSearchResponseDTO::fromArray(['orders' => []]);

        expect($dto->orders)->toBe([]);
    });
});
