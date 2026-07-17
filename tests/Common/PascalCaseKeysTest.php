<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * A Amazon SP-API usa PascalCase (AmazonOrderId, OrderStatus). DTOs que
 * implementam UsesPascalCaseKeys convertem camel<->Pascal; siglas que a
 * conversao automatica erra (SellerSKU, ASIN) usam #[JsonKey].
 */
final class FakePascalDTO implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $amazonOrderId = null,   // -> AmazonOrderId
        public readonly ?string $orderStatus = null,     // -> OrderStatus
        #[JsonKey('SellerSKU')]                           // sigla: automatico erraria
        public readonly ?string $sellerSku = null,
        #[JsonKey('ASIN')]
        public readonly ?string $asin = null,
    ) {}
}

it('hidrata chaves PascalCase da Amazon', function () {
    $dto = FakePascalDTO::fromArray([
        'AmazonOrderId' => '701-1234567-1234567',
        'OrderStatus' => 'Shipped',
        'SellerSKU' => 'SKU-1',
        'ASIN' => 'B0ABCDEF12',
    ]);

    expect($dto->amazonOrderId)->toBe('701-1234567-1234567')
        ->and($dto->orderStatus)->toBe('Shipped')
        ->and($dto->sellerSku)->toBe('SKU-1')
        ->and($dto->asin)->toBe('B0ABCDEF12');
});

it('serializa de volta em PascalCase — roundtrip lossless', function () {
    $payload = [
        'AmazonOrderId' => '701-1234567-1234567',
        'OrderStatus' => 'Shipped',
        'SellerSKU' => 'SKU-1',
        'ASIN' => 'B0ABCDEF12',
    ];

    expect(FakePascalDTO::fromArray($payload)->toArray())->toEqual($payload);
});
