<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Amazon\DTO\Response\Order\OrderListResponseDTO;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Order\OrderResponseDTO;

it('hidrata a lista de pedidos + NextToken', function () {
    $dto = OrderListResponseDTO::fromArray([
        'Orders' => [
            ['AmazonOrderId' => '701-1', 'OrderStatus' => 'Shipped'],
            ['AmazonOrderId' => '701-2', 'OrderStatus' => 'Pending'],
        ],
        'NextToken' => 'abc123',
        'LastUpdatedBefore' => '2026-07-17T00:00:00Z',
    ]);

    expect($dto->orders)->toHaveCount(2)
        ->and($dto->orders[0])->toBeInstanceOf(OrderResponseDTO::class)
        ->and($dto->orders[0]->amazonOrderId)->toBe('701-1')
        ->and($dto->nextToken)->toBe('abc123');
});

it('faz roundtrip lossless da lista', function () {
    $payload = [
        'Orders' => [['AmazonOrderId' => '701-1', 'OrderStatus' => 'Shipped']],
        'NextToken' => 'tok',
    ];

    expect(OrderListResponseDTO::fromArray($payload)->toArray())->toEqual($payload);
});
