<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Magalu\DTO\Response\Order\OrderResponseDTO;

/**
 * Payload SINTETICO na shape de /seller/v1/orders/{id}, cobrindo os quirks:
 *   - dinheiro e INT NORMALIZADO (objeto {total/value, normalizer}), nao decimal
 *   - itens moram em deliveries[].items[], nao num items[] de topo
 *   - NFe (chave SEFAZ) em deliveries[].invoices[].key
 *   - provider.extras.is_fulfillment marca Magalu Entregas
 */
function fakeMagaluOrder(): array
{
    return [
        'id' => 'ord-123',
        'code' => 'LU-456',
        'status' => 'delivered',
        'created_at' => '2026-07-01T10:00:00Z',
        'purchased_at' => '2026-07-01T09:55:00Z',
        'amounts' => [
            'currency' => 'BRL', 'normalizer' => 100, 'total' => 15990,   // R$ 159,90
            'commission' => ['currency' => 'BRL', 'normalizer' => 100, 'total' => 2400, 'type' => 'percent'],
            'discount' => ['currency' => 'BRL', 'normalizer' => 100, 'total' => 0],
            'freight' => ['currency' => 'BRL', 'normalizer' => 100, 'total' => 1990],
            'tax' => ['currency' => 'BRL', 'normalizer' => 100, 'total' => 0],
        ],
        'channel' => [
            'id' => 'magalu', 'extras' => ['alias' => 'magazineluiza'],
            'marketplace' => ['document' => '47960950000121'],
        ],
        'customer' => [
            'name' => 'Comprador Fake', 'document_number' => '12345678901',
            'customer_type' => 'PF', 'birth_date' => '1990-01-01',
        ],
        'payments' => [[
            'amount' => 15990, 'normalizer' => 100, 'currency' => 'BRL',
            'method' => 'credit_card', 'brand' => 'visa', 'installments' => 3,
            'gateway' => ['document' => '47960950000121'],
        ]],
        'deliveries' => [[
            'id' => 'del-1', 'code' => 'DEL-1', 'status' => 'delivered',
            'seller' => ['id' => 's1', 'name' => 'Soldiers'],
            'invoices' => [[
                'key' => '35260747960950000121550010001234561234567890',
                'issued_at' => '2026-07-02T08:00:00Z',
                'status' => ['id' => '1', 'description' => 'autorizada', 'status_date' => '2026-07-02'],
            ]],
            'items' => [[
                'info' => ['id' => 'p1', 'sku' => 'SKU-1', 'name' => 'Creatina', 'brand' => 'Soldiers',
                    'images' => [['url' => 'https://x/1.jpg']]],
                'quantity' => 2, 'measure_unit' => 'un', 'sequencial' => 1,
                'unit_price' => ['currency' => 'BRL', 'normalizer' => 100, 'value' => 7000],  // value, nao total
                'amounts' => ['currency' => 'BRL', 'normalizer' => 100, 'total' => 14000],
            ]],
            'shipping' => [
                'delivered_at' => '2026-07-05T14:00:00Z',
                'provider' => [
                    'id' => 'prov1', 'name' => 'Magalu Entregas', 'description' => 'MLE',
                    'extras' => ['is_fulfillment' => true, 'is_mle' => true, 'shipping_type' => 'FULL'],
                ],
                'recipient' => [
                    'name' => 'Comprador Fake', 'document_number' => '12345678901', 'customer_type' => 'PF',
                    'address' => ['street' => 'Rua Fake', 'number' => '100', 'city' => 'Sao Paulo',
                        'state' => 'SP', 'country' => 'BR', 'zipcode' => '01000000', 'district' => 'Centro'],
                ],
                'tracking' => ['url' => 'https://track/1'],
            ],
        ]],
    ];
}

it('hidrata o pedido inteiro', function () {
    $dto = OrderResponseDTO::fromArray(fakeMagaluOrder());

    expect($dto->id)->toBe('ord-123')
        ->and($dto->code)->toBe('LU-456')
        ->and($dto->customer?->documentNumber)->toBe('12345678901')  // PII sem mascara
        ->and($dto->channel?->extras?->alias)->toBe('magazineluiza');  // separa de Netshoes
});

it('dinheiro e INT normalizado — amount() da o valor em reais', function () {
    $dto = OrderResponseDTO::fromArray(fakeMagaluOrder());

    // total=15990, normalizer=100 -> R$ 159,90. Ler 15990 como reais estaria errado.
    expect($dto->amounts?->total)->toBe(15990)
        ->and($dto->amounts?->amount())->toBe(159.90)
        ->and($dto->amounts?->commission?->amount())->toBe(24.0)
        ->and($dto->amounts?->freight?->amount())->toBe(19.90);

    // unit_price usa `value` (nao `total`); amount() cobre os dois.
    $item = $dto->deliveries[0]->items[0];
    expect($item->unitPrice?->value)->toBe(7000)
        ->and($item->unitPrice?->amount())->toBe(70.0);
});

it('itens moram em deliveries[].items[], nao no topo', function () {
    $dto = OrderResponseDTO::fromArray(fakeMagaluOrder());

    expect($dto->deliveries)->toHaveCount(1)
        ->and($dto->deliveries[0]->items)->toHaveCount(1)
        ->and($dto->deliveries[0]->items[0]->info?->sku)->toBe('SKU-1')
        ->and($dto->deliveries[0]->items[0]->quantity)->toBe(2);
});

it('expoe a chave SEFAZ da NFe da entrega', function () {
    $dto = OrderResponseDTO::fromArray(fakeMagaluOrder());

    expect($dto->deliveries[0]->invoices[0]->key)->toHaveLength(44)
        ->and($dto->deliveries[0]->invoices[0]->status?->description)->toBe('autorizada');
});

it('marca Magalu Entregas (fulfillment) via provider.extras', function () {
    $dto = OrderResponseDTO::fromArray(fakeMagaluOrder());
    $extras = $dto->deliveries[0]->shipping?->provider?->extras;

    expect($extras?->isFulfillment)->toBeTrue()
        ->and($extras?->isMle)->toBeTrue()
        ->and($extras?->shippingType)->toBe('FULL');
});

it('faz roundtrip lossless do pedido', function () {
    $payload = fakeMagaluOrder();

    expect(OrderResponseDTO::fromArray($payload)->toArray())->toEqual($payload);
});
