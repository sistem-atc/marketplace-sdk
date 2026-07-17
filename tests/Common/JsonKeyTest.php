<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * #[JsonKey] existe porque NENHUMA regra automatica de camel<->snake serve pros
 * dois MPs ao mesmo tempo:
 *
 *   Shopee : shipping_fee_discount_from_3pl  (COM  '_' antes do digito)
 *   TikTok : address_line1                   (SEM  '_' antes do digito)
 *
 * Os dois sao minuscula-seguida-de-digito. Regra que conserta um quebra o
 * outro — e o campo some em SILENCIO. Estes testes travam os dois lados.
 */
final class FakeDigitDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // Conversao automatica acerta: addressLine1 -> address_line1
        public readonly ?string $addressLine1 = null,
        // Conversao automatica erraria (..._from3pl) — JsonKey declara a real
        #[JsonKey('shipping_fee_discount_from_3pl')]
        public readonly ?float $shippingFeeDiscountFrom3pl = null,
    ) {}
}

it('hidrata a chave declarada em JsonKey', function () {
    $dto = FakeDigitDTO::fromArray([
        'address_line1' => 'Rua Fake, 100',
        'shipping_fee_discount_from_3pl' => 5.5,
    ]);

    expect($dto->addressLine1)->toBe('Rua Fake, 100')
        ->and($dto->shippingFeeDiscountFrom3pl)->toBe(5.5);
});

it('serializa de volta com a chave declarada — roundtrip lossless', function () {
    $payload = [
        'address_line1' => 'Rua Fake, 100',
        'shipping_fee_discount_from_3pl' => 5.5,
    ];

    expect(FakeDigitDTO::fromArray($payload)->toArray())->toEqual($payload);
});

it('sem JsonKey, a conversao automatica NAO quebra em digito', function () {
    // Trava a regra: addressLine1 -> address_line1 (e nao address_line_1).
    $out = FakeDigitDTO::fromArray(['address_line1' => 'x'])->toArray();

    expect(array_keys($out))->toBe(['address_line1']);
});

it('ignora a chave que a conversao automatica geraria pro campo com JsonKey', function () {
    // `..._from3pl` (o que o camelToSnake produz) NAO deve hidratar: a chave
    // real da Shopee e' `..._from_3pl`. Se hidratasse, mascararia o bug.
    $dto = FakeDigitDTO::fromArray(['shipping_fee_discount_from3pl' => 9.9]);

    expect($dto->shippingFeeDiscountFrom3pl)->toBeNull();
});
