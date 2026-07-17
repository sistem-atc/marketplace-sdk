<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Shopee\DTO\Response\Logistics\ShippingDocumentParameterResult;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Logistics\ShippingDocumentResult;

it('tipa o result de get_shipping_document_parameter (selectable types)', function () {
    $item = ShippingDocumentParameterResult::fromArray([
        'order_sn' => '2506ABCDEF',
        'package_number' => 'PKG-1',
        'selectable_shipping_document_type' => ['NORMAL_AIR_WAYBILL', 'THERMAL_AIR_WAYBILL'],
        'suggest_shipping_document_type' => 'NORMAL_AIR_WAYBILL',
    ]);

    expect($item->orderSn)->toBe('2506ABCDEF')
        ->and($item->selectableShippingDocumentType)->toBe(['NORMAL_AIR_WAYBILL', 'THERMAL_AIR_WAYBILL'])
        ->and($item->suggestShippingDocumentType)->toBe('NORMAL_AIR_WAYBILL');
});

it('tipa o result de get_shipping_document_result (status + falha)', function () {
    $ready = ShippingDocumentResult::fromArray([
        'order_sn' => '2506ABCDEF',
        'package_number' => 'PKG-1',
        'status' => 'READY',
        'shipping_document_type' => 'NORMAL_AIR_WAYBILL',
    ]);
    $failed = ShippingDocumentResult::fromArray([
        'order_sn' => '2506XYZ',
        'status' => 'FAILED',
        'fail_error' => 'logistics_not_ready',
        'fail_message' => 'aguardando coleta',
    ]);

    expect($ready->status)->toBe('READY')
        ->and($ready->shippingDocumentType)->toBe('NORMAL_AIR_WAYBILL')
        ->and($failed->status)->toBe('FAILED')
        ->and($failed->failError)->toBe('logistics_not_ready')
        ->and($failed->failMessage)->toBe('aguardando coleta');
});

it('roundtrip fromArray→toArray preserva as chaves (snake_case)', function () {
    $payload = [
        'order_sn' => '2506ABCDEF',
        'package_number' => 'PKG-1',
        'status' => 'PROCESSING',
        'shipping_document_type' => 'NORMAL_AIR_WAYBILL',
        'fail_error' => '',
        'fail_message' => '',
    ];

    expect(ShippingDocumentResult::fromArray($payload)->toArray())->toBe($payload);
});
