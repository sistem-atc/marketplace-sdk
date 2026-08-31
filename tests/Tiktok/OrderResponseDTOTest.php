<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\OrderResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\OrderSearchResponseDTO;

/**
 * Payload SINTETICO na shape de /order/{v}/orders (dados fake), cobrindo os
 * 3 desvios do TikTok em relacao aos outros MPs:
 *   1. dinheiro e STRING ("45.90")
 *   2. NAO ha quantidade — cada line_item e 1 unidade
 *   3. cidade/estado so existem em district_info[] por nivel
 */
function fakeTiktokOrderPayload(): array
{
    return [
        'id' => '577750646121497000',
        'status' => 'AWAITING_SHIPMENT',
        'order_type' => 'NORMAL',
        'user_id' => '7494900000000000000',
        'buyer_email' => 'fake@tiktok.com',
        'buyer_message' => '',
        'cpf' => '12345678901',
        'cpf_name' => 'Comprador Fake',
        'create_time' => 1752710400,
        'update_time' => 1752796800,
        'paid_time' => 1752710500,
        'is_cod' => false,
        'need_upload_invoice' => 'yes',
        'fulfillment_type' => 'FULFILLMENT_BY_SELLER',
        'payment' => [
            'currency' => 'BRL',
            // STRING, nao numero
            'total_amount' => '45.90',
            'sub_total' => '49.90',
            'shipping_fee' => '0.00',
            'seller_discount' => '4.00',
            'platform_discount' => '0.00',
            'original_total_product_price' => '49.90',
            'original_shipping_fee' => '12.90',
        ],
        'recipient_address' => [
            'name' => 'Comprador Fake',
            'first_name' => 'Comprador',
            'last_name' => 'Fake',
            'phone_number' => '(+55)11999999999',
            'full_address' => 'Rua Fake, 100, Sao Paulo, SP, 01000-000',
            'address_detail' => 'Apto 1',
            // O camelToSnake automatico gerava address_line_1 e DROPAVA estes 4
            'address_line1' => 'Rua Fake, 100',
            'address_line2' => 'Apto 1',
            'address_line3' => 'Centro',
            'address_line4' => 'Sao Paulo',
            'postal_code' => '01000000',
            'region_code' => 'BR',
            'district_info' => [
                ['address_level' => 'L0', 'address_level_name' => 'Country', 'address_name' => 'Brazil', 'iso_code' => 'BR'],
                ['address_level' => 'L1', 'address_level_name' => 'State', 'address_name' => 'Sao Paulo', 'iso_code' => 'BR-SP'],
                ['address_level' => 'L2', 'address_level_name' => 'City', 'address_name' => 'Sao Paulo'],
            ],
        ],
        'line_items' => [
            [
                'id' => '577750646121498001', 'product_id' => '1729592094930765000',
                'product_name' => 'Creatina 300g', 'sku_id' => '1729592094930831000',
                'sku_name' => '300g', 'seller_sku' => 'SKU-1', 'currency' => 'BRL',
                'original_price' => '49.90', 'sale_price' => '45.90',
                'seller_discount' => '4.00', 'platform_discount' => '0.00',
                'is_gift' => false, 'display_status' => 'AWAITING_SHIPMENT',
            ],
            // MESMO sku: 2 unidades = 2 entradas (o TikTok nao manda quantity)
            [
                'id' => '577750646121498002', 'product_id' => '1729592094930765000',
                'product_name' => 'Creatina 300g', 'sku_id' => '1729592094930831000',
                'sku_name' => '300g', 'seller_sku' => 'SKU-1', 'currency' => 'BRL',
                'original_price' => '49.90', 'sale_price' => '45.90',
                'seller_discount' => '4.00', 'platform_discount' => '0.00',
                'is_gift' => false, 'display_status' => 'AWAITING_SHIPMENT',
            ],
        ],
        'packages' => [['id' => '1153475789603966000']],
    ];
}

it('hidrata o pedido inteiro', function () {
    $dto = OrderResponseDTO::fromArray(fakeTiktokOrderPayload());

    expect($dto->id)->toBe('577750646121497000')   // STRING: snowflake nao cabe em int
        ->and($dto->status)->toBe('AWAITING_SHIPMENT')
        ->and($dto->createTime)->toBe(1752710400)
        ->and($dto->cpf)->toBe('12345678901')       // PII na RAIZ, sem mascara no BR
        ->and($dto->cpfName)->toBe('Comprador Fake');
});

it('mantem dinheiro como STRING — casa decimal preservada', function () {
    $dto = OrderResponseDTO::fromArray(fakeTiktokOrderPayload());

    // Tipar float faria "45.90" -> 45.9 e "0.00" -> 0: o raw perderia a forma.
    expect($dto->payment?->totalAmount)->toBe('45.90')
        ->and($dto->payment?->shippingFee)->toBe('0.00')
        ->and($dto->lineItems[0]->salePrice)->toBe('45.90');

    // Consumidor converte quando precisa de conta:
    expect((float) $dto->payment->totalAmount)->toBe(45.9);
});

it('nao tem quantidade: cada line_item e UMA unidade', function () {
    $dto = OrderResponseDTO::fromArray(fakeTiktokOrderPayload());

    // 2 unidades do MESMO sku = 2 entradas, com ids distintos.
    expect($dto->lineItems)->toHaveCount(2)
        ->and($dto->lineItems[0]->skuId)->toBe($dto->lineItems[1]->skuId)
        ->and($dto->lineItems[0]->id)->not->toBe($dto->lineItems[1]->id);

    // Quantidade = agrupar por skuId.
    $qtdPorSku = array_count_values(array_map(fn ($i) => $i->skuId, $dto->lineItems));
    expect($qtdPorSku['1729592094930831000'])->toBe(2);
});

it('cidade/estado so existem em district_info por NIVEL', function () {
    $dto = OrderResponseDTO::fromArray(fakeTiktokOrderPayload());
    $niveis = $dto->recipientAddress?->districtInfo;

    expect($niveis)->toHaveCount(3);

    // Achar pelo NOME do nivel, nunca pela posicao.
    $cidade = collect($niveis)->firstWhere('addressLevelName', 'City');
    $estado = collect($niveis)->firstWhere('addressLevelName', 'State');

    expect($cidade->addressName)->toBe('Sao Paulo')
        ->and($estado->addressName)->toBe('Sao Paulo')
        ->and($estado->isoCode)->toBe('BR-SP');
});

it('preserva os address_line1..4 (o digito colado que quebrou o camelToSnake)', function () {
    $dto = OrderResponseDTO::fromArray(fakeTiktokOrderPayload());

    expect($dto->recipientAddress?->addressLine1)->toBe('Rua Fake, 100')
        ->and($dto->recipientAddress?->addressLine4)->toBe('Sao Paulo');
});

it('faz roundtrip lossless do pedido', function () {
    $payload = fakeTiktokOrderPayload();

    expect(OrderResponseDTO::fromArray($payload)->toArray())->toEqual($payload);
});

it('search devolve o pedido COMPLETO + page token opaco', function () {
    $dto = OrderSearchResponseDTO::fromArray([
        'orders' => [fakeTiktokOrderPayload()],
        'next_page_token' => 'b2Zmc2V0PTUw',
        'total_count' => 120,
    ]);

    // Diferente de Shopee/ML: a listagem ja traz o pedido inteiro.
    expect($dto->orders)->toHaveCount(1)
        ->and($dto->orders[0])->toBeInstanceOf(OrderResponseDTO::class)
        ->and($dto->orders[0]->payment?->totalAmount)->toBe('45.90')
        ->and($dto->nextPageToken)->toBe('b2Zmc2V0PTUw')
        ->and($dto->totalCount)->toBe(120);
});

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
    // O roundtrip acima compara contra um fixture escrito A MAO: campo que
    // nunca entrou no fixture jamais era cobrado, e o teste passava verde
    // enquanto a API mandava dado que morria na desserializacao. Foram 24
    // campos assim — entre eles `is_refundable_sample`, o unico sinal que
    // distingue amostra reembolsavel de venda normal.
    //
    // Este teste usa o exemplo OFICIAL da doc como fonte. Quando o TikTok
    // adicionar campo novo, basta atualizar o fixture: o teste quebra e a
    // gente fica sabendo, em vez de perder dado em silencio.
    $payload = json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/get-order-detail.json'),
        true,
    );

    $reconstruido = OrderResponseDTO::fromArray($payload)->toArray();

    $perdidos = array_diff(array_keys($payload), array_keys($reconstruido));

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});
