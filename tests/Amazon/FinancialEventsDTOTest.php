<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Amazon\DTO\Response\Finance\FinancialEvents;

function fakeFinancialEvents(): array
{
    return [
        'ShipmentEventList' => [[
            'ShipmentItemList' => [[
                'ItemChargeList' => [['ChargeType' => 'Principal', 'ChargeAmount' => ['CurrencyAmount' => 99.9, 'CurrencyCode' => 'BRL']]],
                'ItemFeeList' => [['FeeType' => 'Commission', 'FeeAmount' => ['CurrencyAmount' => -15.0, 'CurrencyCode' => 'BRL']]],
                'ItemPromotionList' => [['PromotionType' => 'Shipping', 'PromotionAmount' => ['CurrencyAmount' => -5.0, 'CurrencyCode' => 'BRL']]],
            ]],
        ]],
        'RefundEventList' => [['ShipmentItemAdjustmentList' => [['ItemFeeAdjustmentList' => [['FeeType' => 'Commission', 'FeeAmount' => ['CurrencyAmount' => 15.0, 'CurrencyCode' => 'BRL']]]]]]],
    ];
}

it('FinancialEvents preserva a árvore profunda PascalCase no roundtrip (parser depende disso)', function () {
    $payload = fakeFinancialEvents();
    $back = FinancialEvents::fromArray($payload)->toArray();

    // o parser faz data_get($events, 'ShipmentEventList...') — a shape TEM que voltar idêntica
    expect($back)->toEqual($payload)
        ->and(data_get($back, 'ShipmentEventList.0.ShipmentItemList.0.ItemFeeList.0.FeeType'))->toBe('Commission')
        ->and(data_get($back, 'ShipmentEventList.0.ShipmentItemList.0.ItemFeeList.0.FeeAmount.CurrencyAmount'))->toBe(-15.0)
        ->and(data_get($back, 'RefundEventList.0.ShipmentItemAdjustmentList.0.ItemFeeAdjustmentList.0.FeeAmount.CurrencyAmount'))->toBe(15.0);
});

it('acessa as event lists tipadas por propriedade', function () {
    $fe = FinancialEvents::fromArray(fakeFinancialEvents());
    expect($fe->shipmentEventList)->toBeArray()->toHaveCount(1)
        ->and($fe->refundEventList)->toBeArray()->toHaveCount(1)
        ->and($fe->serviceFeeEventList)->toBeNull();
});
