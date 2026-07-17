<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Amazon\DTO\Response\Order\OrderItem;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Order\OrderResponseDTO;

/** Payload SINTETICO na shape PascalCase da SP-API. */
function fakeAmazonOrder(): array
{
    return [
        'AmazonOrderId' => '701-1234567-1234567',
        'SellerOrderId' => 'SOX-1',
        'OrderStatus' => 'Shipped',
        'OrderType' => 'StandardOrder',
        'PurchaseDate' => '2026-07-01T10:00:00Z',
        'LastUpdateDate' => '2026-07-02T10:00:00Z',
        'FulfillmentChannel' => 'MFN',
        'MarketplaceId' => 'A2Q3Y263D00KWC',
        'NumberOfItemsShipped' => 2,
        'NumberOfItemsUnshipped' => 0,
        'OrderTotal' => ['CurrencyCode' => 'BRL', 'Amount' => '159.90'],  // STRING
        'IsPrime' => false,
        'IsBusinessOrder' => false,
        'IsSoldByAB' => true,     // sigla -> JsonKey
        'IsISPU' => false,        // sigla -> JsonKey
        'ShippingAddress' => ['City' => 'Sao Paulo', 'StateOrRegion' => 'SP',
            'PostalCode' => '01000-000', 'CountryCode' => 'BR'],
        'BuyerInfo' => ['BuyerCounty' => 'Sao Paulo',
            'BuyerTaxInfo' => ['TaxClassifications' => [['Name' => 'CPF', 'Value' => '12345678901']]]],
    ];
}

it('hidrata o header PascalCase da Amazon', function () {
    $dto = OrderResponseDTO::fromArray(fakeAmazonOrder());

    expect($dto->amazonOrderId)->toBe('701-1234567-1234567')
        ->and($dto->orderStatus)->toBe('Shipped')
        ->and($dto->numberOfItemsShipped)->toBe(2)
        // dinheiro e STRING
        ->and($dto->orderTotal?->amount)->toBe('159.90')
        ->and($dto->orderTotal?->currencyCode)->toBe('BRL');
});

it('resolve as siglas via JsonKey (IsSoldByAB, IsISPU)', function () {
    $dto = OrderResponseDTO::fromArray(fakeAmazonOrder());

    // camelToPascal geraria IsSoldByAb/IsIspu — o JsonKey acerta.
    expect($dto->isSoldByAb)->toBeTrue()
        ->and($dto->isIspu)->toBeFalse();
});

it('expoe o CPF do comprador (BuyerTaxInfo)', function () {
    $dto = OrderResponseDTO::fromArray(fakeAmazonOrder());
    $tax = $dto->buyerInfo?->buyerTaxInfo?->taxClassifications[0] ?? null;

    expect($tax?->name)->toBe('CPF')
        ->and($tax?->value)->toBe('12345678901')
        ->and($dto->shippingAddress?->city)->toBe('Sao Paulo');
});

it('faz roundtrip lossless em PascalCase', function () {
    $payload = fakeAmazonOrder();

    expect(OrderResponseDTO::fromArray($payload)->toArray())->toEqual($payload);
});

it('hidrata os OrderItems com SellerSKU/ASIN (siglas)', function () {
    $item = OrderItem::fromArray([
        'ASIN' => 'B0ABCDEF12',
        'SellerSKU' => 'SKU-1',
        'OrderItemId' => '1234567890',
        'Title' => 'Creatina 300g',
        'QuantityOrdered' => 2,
        'QuantityShipped' => 2,
        'ItemPrice' => ['CurrencyCode' => 'BRL', 'Amount' => '129.90'],
        'IsGift' => 'false',   // STRING na SP-API
        'ProductInfo' => ['NumberOfItems' => '1'],
    ]);

    expect($item->asin)->toBe('B0ABCDEF12')
        ->and($item->sellerSku)->toBe('SKU-1')     // via JsonKey
        ->and($item->quantityOrdered)->toBe(2)
        ->and($item->itemPrice?->amount)->toBe('129.90')
        ->and($item->isGift)->toBe('false')        // string, nao bool
        ->and($item->productInfo?->numberOfItems)->toBe('1');
});

it('faz roundtrip lossless do OrderItem', function () {
    $payload = [
        'ASIN' => 'B0ABCDEF12', 'SellerSKU' => 'SKU-1', 'OrderItemId' => '123',
        'Title' => 'X', 'QuantityOrdered' => 1, 'QuantityShipped' => 1,
        'ItemPrice' => ['CurrencyCode' => 'BRL', 'Amount' => '10.00'],
        'IsGift' => 'false', 'IsTransparency' => false,
        'ProductInfo' => ['NumberOfItems' => '1'],
    ];

    expect(OrderItem::fromArray($payload)->toArray())->toEqual($payload);
});
