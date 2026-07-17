<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing\DiscountListResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing\DiscountResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing\Voucher;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing\VoucherListResponseDTO;

it('hidrata a listagem de descontos (page_no 1-based + more)', function () {
    $dto = DiscountListResponseDTO::fromArray([
        'discount_list' => [
            ['discount_id' => 111, 'discount_name' => 'Promo Julho', 'status' => 'ongoing',
                'start_time' => 1752710400, 'end_time' => 1753315200, 'source' => 0],
        ],
        'more' => true,
    ]);

    expect($dto->discountList)->toHaveCount(1)
        ->and($dto->discountList[0]->discountName)->toBe('Promo Julho')
        ->and($dto->discountList[0]->status)->toBe('ongoing')
        ->and($dto->more)->toBeTrue();
});

it('hidrata o detalhe do desconto com itens e variacoes', function () {
    $dto = DiscountResponseDTO::fromArray([
        'discount_id' => 111,
        'discount_name' => 'Promo Julho',
        'status' => 'ongoing',
        'start_time' => 1752710400,
        'end_time' => 1753315200,
        'more' => false,
        'item_list' => [[
            'item_id' => 999,
            'item_name' => 'Creatina',
            'item_original_price' => 120,      // int
            'item_promotion_price' => 99.9,    // float — a Shopee alterna
            'item_promotion_stock' => 50,
            'normal_stock' => 200,
            'purchase_limit' => 2,
            'model_list' => [[
                'model_id' => 888, 'model_name' => '300g',
                'model_original_price' => 120, 'model_promotion_price' => 99.9,
                'model_promotion_stock' => 50, 'model_normal_stock' => 200,
            ]],
        ]],
    ]);

    expect($dto->itemList)->toHaveCount(1)
        ->and($dto->itemList[0]->itemPromotionPrice)->toBe(99.9)
        ->and($dto->itemList[0]->itemOriginalPrice)->toBe(120.0)
        ->and($dto->itemList[0]->modelList[0]->modelPromotionPrice)->toBe(99.9);
});

it('voucher de VALOR FIXO usa discount_amount', function () {
    // reward_type 1 = valor fixo: percentage/max_price nao vem.
    $v = Voucher::fromArray([
        'voucher_id' => 1, 'voucher_code' => 'SOLD10', 'voucher_name' => 'R$10 OFF',
        'reward_type' => 1, 'discount_amount' => 10.0, 'min_basket_price' => 50.0,
        'usage_quantity' => 100, 'current_usage' => 3, 'is_admin' => false,
        'start_time' => 1752710400, 'end_time' => 1753315200,
    ]);

    expect($v->rewardType)->toBe(1)
        ->and($v->discountAmount)->toBe(10.0)
        ->and($v->percentage)->toBeNull()   // ler percentage aqui daria 0 em silencio
        ->and($v->isAdmin)->toBeFalse();
});

it('voucher PERCENTUAL usa percentage + max_price como teto', function () {
    $v = Voucher::fromArray([
        'voucher_id' => 2, 'voucher_code' => 'SOLD15', 'reward_type' => 2,
        'percentage' => 15, 'max_price' => 30.0, 'min_basket_price' => 100.0,
    ]);

    expect($v->percentage)->toBe(15)
        ->and($v->maxPrice)->toBe(30.0)
        ->and($v->discountAmount)->toBeNull();
});

it('distingue cupom da SHOPEE (is_admin) do cupom do vendedor', function () {
    expect(Voucher::fromArray(['voucher_id' => 3, 'is_admin' => true])->isAdmin)->toBeTrue()
        ->and(Voucher::fromArray(['voucher_id' => 4, 'is_admin' => false])->isAdmin)->toBeFalse();
});

it('trata display_channel_list e item_id_list como listas de inteiros', function () {
    $v = Voucher::fromArray([
        'voucher_id' => 5,
        'display_channel_list' => [1, 2],
        'item_id_list' => [999, 888],
    ]);

    expect($v->displayChannelList)->toBe([1, 2])
        ->and($v->itemIdList)->toBe([999, 888]);
});

it('faz roundtrip lossless do voucher', function () {
    $payload = [
        'voucher_id' => 1, 'voucher_code' => 'SOLD10', 'voucher_name' => 'R$10 OFF',
        'voucher_type' => 1, 'voucher_purpose' => 0, 'reward_type' => 1,
        'target_voucher' => 0, 'usecase' => 1, 'is_admin' => false,
        'discount_amount' => 10.0, 'min_basket_price' => 50.0,
        'usage_quantity' => 100, 'current_usage' => 3,
        'start_time' => 1752710400, 'end_time' => 1753315200, 'display_start_time' => 1752700000,
        'display_channel_list' => [1, 2], 'item_id_list' => [999],
    ];

    expect(Voucher::fromArray($payload)->toArray())->toEqual($payload);
});

it('hidrata a listagem de vouchers', function () {
    $dto = VoucherListResponseDTO::fromArray([
        'voucher_list' => [['voucher_id' => 1, 'voucher_code' => 'A'], ['voucher_id' => 2, 'voucher_code' => 'B']],
        'more' => false,
    ]);

    expect($dto->voucherList)->toHaveCount(2)
        ->and($dto->voucherList[1]->voucherCode)->toBe('B')
        ->and($dto->more)->toBeFalse();
});
