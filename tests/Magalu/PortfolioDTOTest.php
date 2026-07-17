<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Magalu\DTO\Response\Product\SkuListResponseDTO;
use SistemAtc\Marketplaces\Magalu\DTO\Response\Product\SkuPriceResponseDTO;

it('hidrata a listagem de SKUs com o detalhe rico', function () {
    $dto = SkuListResponseDTO::fromArray([
        'results' => [[
            'sku' => '5', 'title' => 'BCAA 250g', 'brand' => 'Soldiers', 'ncm' => '21069090',
            'active' => true, 'fulfillment' => false, 'has_ean' => true,
            'identifiers' => [['type' => 'GTIN', 'value' => '7890000000005']],
            'attributes' => [['name' => 'Sabor', 'value' => 'Natural']],
            'dimensions' => [[
                'name' => 'default',
                'height' => ['unit' => 'cm', 'value' => 15.5],
                'weight' => ['unit' => 'g', 'value' => 250],   // int (peso)
            ]],
            'group' => ['id' => 'g1', 'main_variation' => true],
        ]],
        'meta' => ['page' => ['limit' => 50, 'offset' => 0, 'count' => 1, 'max_limit' => 100]],
    ]);

    $sku = $dto->results[0];
    expect($sku->sku)->toBe('5')
        ->and($sku->ncm)->toBe('21069090')
        // o EAN/GTIN — dado fiscal que o app hoje nao le mas a API entrega
        ->and($sku->identifiers[0]->type)->toBe('GTIN')
        ->and($sku->identifiers[0]->value)->toBe('7890000000005')
        ->and($sku->attributes[0]->name)->toBe('Sabor')
        // value mixed: 15.5 (double) e 250 (int) preservam o tipo
        ->and($sku->dimensions[0]->height->value)->toBe(15.5)
        ->and($sku->dimensions[0]->weight->value)->toBe(250)
        ->and($dto->meta?->page?->count)->toBe(1);
});

it('preço é INT normalizado — priceValue() converte', function () {
    $dto = SkuPriceResponseDTO::fromArray([
        'results' => [[
            'price' => 5990, 'list_price' => 6990, 'normalizer' => 100, 'currency' => null,
            'channel' => ['id' => 'ch1'], 'created_at' => '2022-09-06T14:42:29Z',
        ]],
        'meta' => ['page' => ['limit' => 1, 'offset' => 0, 'count' => 1]],
    ]);

    $p = $dto->first();
    expect($p->price)->toBe(5990)              // int cru
        ->and($p->priceValue())->toBe(59.90)   // PARA em reais
        ->and($p->listPriceValue())->toBe(69.90); // DE em reais
});

it('faz roundtrip lossless da listagem e do preço', function () {
    $list = ['results' => [['sku' => '5', 'title' => 'X', 'active' => true]], 'meta' => ['page' => ['count' => 1]]];
    $price = ['results' => [['price' => 5990, 'normalizer' => 100]], 'meta' => ['page' => ['count' => 1]]];

    expect(SkuListResponseDTO::fromArray($list)->toArray())->toEqual($list)
        ->and(SkuPriceResponseDTO::fromArray($price)->toArray())->toEqual($price);
});
