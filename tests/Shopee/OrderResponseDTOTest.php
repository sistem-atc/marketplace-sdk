<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\InvoiceData;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\OrderItem;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\OrderListResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\OrderPackage;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\OrderResponseDTO;

/**
 * Payload SINTETICO na shape de /api/v2/order/get_order_detail (dados fake).
 * Reproduz os casos-armadilha achados no corpus real:
 *   - product_location_id e LISTA em item_list[] e STRING em package_list[].item_list[]
 *   - dinheiro vem ora int ora float
 *   - pay_time pode vir null
 *   - PII mascarada pela Shopee (`****`)
 */
function fakeShopeeOrderPayload(): array
{
    return [
        'order_sn' => '2607170AB1CDEF',
        'order_status' => 'COMPLETED',
        'booking_sn' => '',
        'region' => 'BR',
        'currency' => 'BRL',
        'total_amount' => 189,           // int (a Shopee alterna int/float)
        'cod' => false,
        'create_time' => 1752710400,     // epoch em SEGUNDOS
        'update_time' => 1752796800,
        'pay_time' => null,              // pode vir null
        'ship_by_date' => 1752883200,
        'days_to_ship' => 2,
        'buyer_user_id' => 987654321,
        'buyer_username' => 'comprador_fake',
        'buyer_cpf_id' => '***.456.789-**',
        'message_to_seller' => '',
        'recipient_address' => [
            'name' => '****',            // PII mascarada pela Shopee
            'phone' => '****',
            'town' => 'Centro',
            'district' => 'Centro',
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'region' => 'BR',
            'zipcode' => '01000000',
            'full_address' => '****',
        ],
        'actual_shipping_fee' => 12.5,   // float
        'reverse_shipping_fee' => 0,
        'fulfillment_flag' => 'fulfilled_by_shopee',
        'can_full_cancel_order' => false,
        'can_partial_cancel_order' => false,
        'buyer_preference_for_partial_cancellation' => 0,
        'advance_package' => false,
        'is_buyer_shop_collection' => false,
        'hot_listing_order' => false,
        'invoice_data' => [
            'number' => '123456',
            'series_number' => '2',
            'access_key' => '35260712345678000199550020001234561234567890',
            'issue_date' => 1752710500,
            'total_value' => 189,
            'products_total_value' => 176.5,
            'tax_code' => '5102',
        ],
        'item_list' => [[
            'item_id' => 111222333,
            'item_name' => 'Produto Fake 900g',
            'item_sku' => 'SKU-PAI',
            'model_id' => 444555666,
            'model_name' => 'Chocolate',
            'model_sku' => 'SKU-VAR-1',
            'model_quantity_purchased' => 2,
            'model_original_price' => 109.9,
            'model_discounted_price' => 94.5,
            'wholesale' => false,
            'weight' => 0.9,
            'add_on_deal' => false,
            'main_item' => true,
            'add_on_deal_id' => 0,
            'promotion_type' => 'flash_sale',
            'promotion_id' => 777888,
            'order_item_id' => 111222333,
            'line_item_id' => 1,
            'promotion_group_id' => 0,
            'image_info' => ['image_url' => 'https://cf.shopee.com.br/file/fake.jpg'],
            'product_location_id' => ['BR'],   // LISTA aqui
            'is_prescription_item' => false,
            'error_in_fetching_is_prescription_item' => false,
            'consultation_id' => '',
            'is_b2c_owned_item' => false,
            'hot_listing_item' => false,
            'active_qty' => 2,
            'cancelled_qty' => 0,
            'cancel_requested_qty' => 0,
            'returned_qty' => 0,
            'return_requested_qty' => 0,
            'promotion_list' => [
                ['promotion_id' => 777888, 'promotion_type' => 'flash_sale'],
            ],
        ]],
        'package_list' => [[
            'package_number' => 'PKG0001',
            'logistics_status' => 'LOGISTICS_DELIVERY_DONE',
            'logistics_channel_id' => 90001,
            'shipping_carrier' => 'Shopee Xpress',
            'allow_self_design_awb' => false,
            'parcel_chargeable_weight_gram' => 1800,
            'sorting_group' => 'SP-01',
            'group_shipment_id' => null,
            'item_list' => [[
                'item_id' => 111222333,
                'model_id' => 444555666,
                'model_quantity' => 2,
                'order_item_id' => 111222333,
                'promotion_group_id' => 0,
                'product_location_id' => 'BR',   // STRING aqui (assimetria da Shopee)
            ]],
        ]],
    ];
}

it('hidrata o pedido inteiro a partir do payload da Shopee', function () {
    $dto = OrderResponseDTO::fromArray(fakeShopeeOrderPayload());

    expect($dto->orderSn)->toBe('2607170AB1CDEF')
        ->and($dto->orderStatus)->toBe('COMPLETED')
        ->and($dto->totalAmount)->toBe(189.0)
        ->and($dto->createTime)->toBe(1752710400)
        ->and($dto->payTime)->toBeNull()
        ->and($dto->fulfillmentFlag)->toBe('fulfilled_by_shopee');
});

it('tipa invoice_data — a chave de 44 digitos que o pipeline fiscal consome', function () {
    $dto = OrderResponseDTO::fromArray(fakeShopeeOrderPayload());

    expect($dto->invoiceData)->toBeInstanceOf(InvoiceData::class)
        ->and($dto->invoiceData->accessKey)->toHaveLength(44)
        ->and($dto->invoiceData->number)->toBe('123456')
        ->and($dto->invoiceData->totalValue)->toBe(189.0);
});

it('tipa as linhas do pedido com o SKU do vendedor', function () {
    $dto = OrderResponseDTO::fromArray(fakeShopeeOrderPayload());

    expect($dto->itemList)->toHaveCount(1)
        ->and($dto->itemList[0])->toBeInstanceOf(OrderItem::class)
        ->and($dto->itemList[0]->modelSku)->toBe('SKU-VAR-1')
        ->and($dto->itemList[0]->modelQuantityPurchased)->toBe(2)
        ->and($dto->itemList[0]->modelDiscountedPrice)->toBe(94.5)
        ->and($dto->itemList[0]->imageInfo?->imageUrl)->toContain('fake.jpg')
        ->and($dto->itemList[0]->promotionList[0]->promotionType)->toBe('flash_sale');
});

it('aguenta product_location_id lista no item e string no pacote', function () {
    $dto = OrderResponseDTO::fromArray(fakeShopeeOrderPayload());

    expect($dto->itemList[0]->productLocationId)->toBe(['BR'])
        ->and($dto->packageList[0])->toBeInstanceOf(OrderPackage::class)
        ->and($dto->packageList[0]->itemList[0]->productLocationId)->toBe('BR');
});

it('preserva a PII mascarada pela Shopee sem confundir com campo vazio', function () {
    $dto = OrderResponseDTO::fromArray(fakeShopeeOrderPayload());

    // `****` e' mascaramento da Shopee (falta escopo unmask), nao bug de parse.
    expect($dto->recipientAddress?->name)->toBe('****')
        ->and($dto->recipientAddress?->city)->toBe('Sao Paulo')
        ->and($dto->recipientAddress?->zipcode)->toBe('01000000');
});

it('hidrata pedido PARCIAL quando so invoice_data foi pedido', function () {
    // getOrderDetail([...], ['invoice_data']) devolve so esse bloco — os demais
    // campos ficam null. null aqui = "nao foi pedido", nao "Shopee nao tem".
    $dto = OrderResponseDTO::fromArray([
        'order_sn' => '2607170AB1CDEF',
        'invoice_data' => ['access_key' => str_repeat('1', 44)],
    ]);

    expect($dto->orderSn)->toBe('2607170AB1CDEF')
        ->and($dto->invoiceData?->accessKey)->toHaveLength(44)
        ->and($dto->itemList)->toBeNull()
        ->and($dto->totalAmount)->toBeNull();
});

it('faz roundtrip lossless — toArray devolve o payload da Shopee', function () {
    $payload = fakeShopeeOrderPayload();
    $round = OrderResponseDTO::fromArray($payload)->toArray();

    // CastToArray omite chave null; pay_time e group_shipment_id sao null no
    // fixture. Fora isso, toArray() TEM que devolver o payload verbatim — e o
    // que garante trocar o raw pelo DTO sem perder dado (INSERT-first).
    $expected = $payload;
    unset($expected['pay_time'], $expected['package_list'][0]['group_shipment_id']);

    expect($round)->toEqual($expected);
});

it('hidrata a listagem paginada por cursor', function () {
    $dto = OrderListResponseDTO::fromArray([
        'order_list' => [
            ['order_sn' => 'AAA111', 'order_status' => 'READY_TO_SHIP'],
            ['order_sn' => 'BBB222', 'order_status' => 'SHIPPED'],
        ],
        'more' => true,
        'next_cursor' => '50',
    ]);

    expect($dto->orderList)->toHaveCount(2)
        ->and($dto->orderList[0]->orderSn)->toBe('AAA111')
        ->and($dto->more)->toBeTrue()
        ->and($dto->nextCursor)->toBe('50');
});
