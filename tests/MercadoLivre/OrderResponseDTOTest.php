<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order\OrderItem;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order\OrderPayment;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order\OrderResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order\OrderSearchResponseDTO;

/**
 * Payload SINTETICO (mesma shape da API ML /orders/{id}, dados fake) — cobre os
 * casos-armadilha reais: data escalar vinda como `[]`, `collector` objeto,
 * `variation_attributes` como lista de strings.
 */
function fakeMlOrderPayload(): array
{
    return [
        'id' => 2000000000001,
        'status' => 'paid',
        'status_detail' => null,
        'pack_id' => 2000000000009,
        'buying_mode' => 'buy_it_now',
        'currency_id' => 'BRL',
        'total_amount' => 199.9,
        'paid_amount' => 199.9,
        'shipping_cost' => 0,
        'date_created' => '2026-07-16T10:00:00.000-03:00',
        'date_closed' => '2026-07-16T10:05:00.000-03:00',
        'last_updated' => '2026-07-16T10:10:00.000-03:00',
        'comment' => null,
        'pickup_id' => null,
        'coupon' => ['id' => 123, 'amount' => 10.0],
        'buyer' => ['id' => 555, 'nickname' => 'FAKEBUYER', 'first_name' => 'Fake', 'last_name' => 'Buyer'],
        'seller' => ['id' => 64196652, 'nickname' => 'FAKESELLER'],
        'order_items' => [[
            'item' => [
                'id' => 'MLB123',
                'title' => 'Produto Fake',
                'category_id' => 'MLB1234',
                'seller_sku' => 'SKU-1',
                'seller_custom_field' => 'SKU-1',
                'variation_id' => 987,
                // ML às vezes manda variation_attributes como lista de strings.
                'variation_attributes' => ['cor:azul', 'tamanho:M'],
                'attributes' => [['id' => 'BRAND', 'value_name' => 'Fake']],
            ],
            'quantity' => 2,
            'requested_quantity' => ['value' => 2, 'measure' => 'unit'], // objeto
            'picked_quantity' => null,
            'unit_price' => 99.95,
            'full_unit_price' => 109.95,
            'sale_fee' => 15.5,
            'currency_id' => 'BRL',
            'listing_type_id' => 'gold_special',
            'stock' => [], // marcador vazio
        ]],
        'payments' => [[
            'id' => 111222333,
            'collector' => ['id' => 64196652], // objeto
            'status' => 'approved',
            'transaction_amount' => 199.9,
            'total_paid_amount' => 199.9,
            'marketplace_fee' => 20.0,
            'installments' => 3,
            'date_approved' => [], // ML manda [] quando vazio
            'date_created' => '2026-07-16T10:00:00.000-03:00',
            'atm_transfer_reference' => ['company_id' => null, 'transaction_id' => null],
        ]],
        'shipping' => ['id' => 47476563359],
        'taxes' => ['id' => null, 'amount' => 0, 'currency_id' => 'BRL'],
        'context' => ['site' => 'MLB', 'channel' => 'marketplace', 'flows' => []],
        'tags' => ['paid', 'not_delivered'],
    ];
}

it('hidrata a árvore do pedido com acesso tipado', function () {
    $o = OrderResponseDTO::fromArray(fakeMlOrderPayload());

    expect($o->id)->toBe(2000000000001)
        ->and($o->status)->toBe('paid')
        ->and($o->packId)->toBe(2000000000009)
        ->and($o->totalAmount)->toBe(199.9)
        ->and($o->buyer->id)->toBe(555)
        ->and($o->buyer->firstName)->toBe('Fake')
        ->and($o->seller->id)->toBe(64196652)
        ->and($o->coupon->amount)->toBe(10.0)
        ->and($o->shipping->id)->toBe(47476563359)
        ->and($o->taxes->currencyId)->toBe('BRL')
        ->and($o->context->channel)->toBe('marketplace');
});

it('tipa order_items e o produto (SKU, preço, fee)', function () {
    $o = OrderResponseDTO::fromArray(fakeMlOrderPayload());
    $it = $o->orderItems[0];

    expect($it)->toBeInstanceOf(OrderItem::class)
        ->and($it->quantity)->toBe(2)
        ->and($it->unitPrice)->toBe(99.95)
        ->and($it->fullUnitPrice)->toBe(109.95)
        ->and($it->saleFee)->toBe(15.5)
        ->and($it->item->sellerSku)->toBe('SKU-1')
        ->and($it->item->id)->toBe('MLB123')
        // variation_attributes lista-de-strings preservada como array cru
        ->and($it->item->variationAttributes)->toBe(['cor:azul', 'tamanho:M'])
        // stock/requested_quantity objeto-ou-vazio preservados (mixed)
        ->and($it->stock)->toBe([])
        ->and($it->requestedQuantity)->toBe(['value' => 2, 'measure' => 'unit']);
});

it('tipa payments e trata `[]` de data como null (marcador vazio do ML)', function () {
    $o = OrderResponseDTO::fromArray(fakeMlOrderPayload());
    $p = $o->payments[0];

    expect($p)->toBeInstanceOf(OrderPayment::class)
        ->and($p->marketplaceFee)->toBe(20.0)
        ->and($p->installments)->toBe(3)
        ->and($p->collector)->toBe(['id' => 64196652]) // objeto preservado (mixed)
        ->and($p->dateApproved)->toBeNull()            // [] → null (não estoura)
        ->and($p->dateCreated)->toBe('2026-07-16T10:00:00.000-03:00');
});

it('OrderSearchResponseDTO tipa results[] como OrderResponseDTO + paging', function () {
    $resp = OrderSearchResponseDTO::fromArray([
        'paging' => ['total' => 137, 'offset' => 50, 'limit' => 50],
        'results' => [fakeMlOrderPayload(), fakeMlOrderPayload()],
    ]);

    expect($resp->paging->total)->toBe(137)
        ->and($resp->paging->offset)->toBe(50)
        ->and($resp->results)->toHaveCount(2)
        ->and($resp->results[0])->toBeInstanceOf(OrderResponseDTO::class)
        ->and($resp->results[0]->id)->toBe(2000000000001)
        ->and($resp->results[0]->orderItems[0]->item->sellerSku)->toBe('SKU-1');
});

it('OrderSearchResponseDTO aguenta busca vazia', function () {
    $resp = OrderSearchResponseDTO::fromArray(['paging' => ['total' => 0], 'results' => []]);

    expect($resp->results)->toBe([])->and($resp->paging->total)->toBe(0);
});

it('roundtrip fromArray→toArray preserva os campos escalares principais', function () {
    $payload = fakeMlOrderPayload();
    $back = OrderResponseDTO::fromArray($payload)->toArray();

    expect($back['id'])->toBe($payload['id'])
        ->and($back['status'])->toBe($payload['status'])
        ->and($back['total_amount'])->toBe($payload['total_amount'])
        ->and($back['pack_id'])->toBe($payload['pack_id']);
});
