<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\PriceDetailResponseDTO;

/**
 * Decomposicao de preco do pedido. O endpoint NAO existia no SDK — e e' o unico
 * lugar, fora do extrato financeiro, onde o desconto bancado pela PLATAFORMA
 * aparece separado do bancado pelo VENDEDOR.
 */
function fixturePriceDetail(): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/get-price-detail.json'),
        true,
    );
}

it('separa o desconto da PLATAFORMA do desconto do VENDEDOR', function () {
    $dto = PriceDetailResponseDTO::fromArray(fixturePriceDetail());

    expect($dto->subtotalDeductionPlatform)->toBe('0.50')
        ->and($dto->subtotalDeductionSeller)->toBe('0.50')
        ->and($dto->payment)->toBe('101.00');
});

it('mantem dinheiro como STRING — inclusive a taxa, que e fracao', function () {
    $dto = PriceDetailResponseDTO::fromArray(fixturePriceDetail());

    // "0.021" = 2,1%. Virar float aqui perderia a precisao do rateio.
    expect($dto->taxRate)->toBe('0.021')
        ->and($dto->total)->toBe('122.00');
});

it('hidrata a quebra por item', function () {
    $dto = PriceDetailResponseDTO::fromArray(fixturePriceDetail());

    expect($dto->lineItems)->toHaveCount(1)
        ->and($dto->lineItems[0]->id)->toBe('576461413032342720')
        ->and($dto->lineItems[0]->subtotalDeductionPlatform)->toBe('0.50');
});

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
    $payload = fixturePriceDetail();

    $perdidos = array_diff(
        array_keys($payload),
        array_keys(PriceDetailResponseDTO::fromArray($payload)->toArray()),
    );

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});
