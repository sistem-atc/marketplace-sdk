<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Shopee\DTO\Response\Product\ItemBaseInfoResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Product\ItemListResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Product\ItemResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Product\ModelListResponseDTO;

/**
 * Payloads SINTETICOS na shape de /api/v2/product/* (dados fake), cobrindo os
 * casos reais: item COM variacao vem sem price_info, weight e string, imagens
 * sao 2 listas paralelas, tier_index e posicional.
 */
function fakeShopeeItemPayload(bool $hasModel = false): array
{
    return [
        'item_id' => 111222333,
        'item_name' => 'Creatina Fake 300g',
        'item_sku' => 'SKU-PAI',
        'item_status' => 'NORMAL',
        'category_id' => 100637,
        'description_type' => 'extended',
        'description_info' => [
            'extended_description' => [
                'field_list' => [
                    ['field_type' => 'text', 'text' => 'Descricao fake'],
                    ['field_type' => 'image', 'image_info' => ['image_id' => 'img1', 'image_url' => 'https://cf.shopee.com.br/file/img1']],
                ],
            ],
        ],
        'condition' => 'NEW',
        'create_time' => 1752710400,
        'update_time' => 1752796800,
        'weight' => '0.35',            // STRING, nao numero
        'dimension' => ['package_length' => 10, 'package_width' => 8, 'package_height' => 15],
        'brand' => ['brand_id' => 0, 'original_brand_name' => 'NoBrand'],
        'authorised_brand_id' => 0,
        'image' => [
            // 2 listas PARALELAS (id e url no mesmo indice)
            'image_id_list' => ['img1', 'img2'],
            'image_url_list' => ['https://cf.shopee.com.br/file/img1', 'https://cf.shopee.com.br/file/img2'],
            'image_ratio' => '1:1',
        ],
        'has_model' => $hasModel,
        'has_promotion' => false,
        'is_fulfillment_by_shopee' => false,
        'pre_order' => ['is_pre_order' => false, 'days_to_ship' => 2],
        'purchase_limit_info' => ['min_purchase_limit' => 1],
        'tag' => ['kit' => false],
        'item_dangerous' => 0,
        'logistic_info' => [
            ['logistic_id' => 90001, 'logistic_name' => 'Shopee Xpress', 'enabled' => true, 'is_free' => false, 'size_id' => 0],
        ],
        'attribute_list' => [[
            'attribute_id' => 100150,
            'original_attribute_name' => 'Sabor',
            'is_mandatory' => true,
            'attribute_value_list' => [
                ['value_id' => 1, 'original_value_name' => 'Natural', 'value_unit' => ''],
            ],
        ]],
        // Item COM variacao NAO traz price_info/stock_info_v2 — moram no model.
        ...($hasModel ? [] : [
            'price_info' => [['currency' => 'BRL', 'original_price' => 99.9, 'current_price' => 89.9]],
            'stock_info_v2' => [
                'summary_info' => ['total_available_stock' => 10, 'total_reserved_stock' => 0],
                'seller_stock' => [['location_id' => '', 'stock' => 10, 'if_saleable' => true]],
                'shopee_stock' => [],
                'advance_stock' => ['sellable_advance_stock' => 0, 'in_transit_advance_stock' => 0],
            ],
        ]),
    ];
}

it('hidrata o anuncio inteiro', function () {
    $dto = ItemResponseDTO::fromArray(fakeShopeeItemPayload());

    expect($dto->itemId)->toBe(111222333)
        ->and($dto->itemSku)->toBe('SKU-PAI')
        ->and($dto->itemStatus)->toBe('NORMAL')
        ->and($dto->weight)->toBe('0.35')   // string, nao float
        ->and($dto->brand?->originalBrandName)->toBe('NoBrand')
        ->and($dto->dimension?->packageHeight)->toBe(15);
});

it('anuncio SEM variacao traz o preco no proprio item', function () {
    $dto = ItemResponseDTO::fromArray(fakeShopeeItemPayload(hasModel: false));

    expect($dto->hasModel)->toBeFalse()
        ->and($dto->priceInfo[0]->currentPrice)->toBe(89.9)
        ->and($dto->priceInfo[0]->currency)->toBe('BRL')
        ->and($dto->stockInfoV2?->summaryInfo?->totalAvailableStock)->toBe(10);
});

it('anuncio COM variacao vem sem price_info — preco mora no getModelList', function () {
    $dto = ItemResponseDTO::fromArray(fakeShopeeItemPayload(hasModel: true));

    // priceInfo null aqui NAO significa "sem preco": significa "pergunte ao model".
    expect($dto->hasModel)->toBeTrue()
        ->and($dto->priceInfo)->toBeNull()
        ->and($dto->stockInfoV2)->toBeNull();
});

it('trata imagens como 2 listas paralelas', function () {
    $dto = ItemResponseDTO::fromArray(fakeShopeeItemPayload());

    expect($dto->image?->imageIdList)->toBe(['img1', 'img2'])
        ->and($dto->image?->imageUrlList[0])->toContain('img1')
        ->and($dto->image?->imageRatio)->toBe('1:1');
});

it('tipa a descricao estendida como lista ordenada de blocos', function () {
    $dto = ItemResponseDTO::fromArray(fakeShopeeItemPayload());
    $fields = $dto->descriptionInfo?->extendedDescription?->fieldList;

    expect($fields)->toHaveCount(2)
        ->and($fields[0]->fieldType)->toBe('text')
        ->and($fields[0]->text)->toBe('Descricao fake')
        ->and($fields[1]->fieldType)->toBe('image')
        ->and($fields[1]->imageInfo?->imageId)->toBe('img1');
});

it('tipa os atributos que alimentam o PIM', function () {
    $dto = ItemResponseDTO::fromArray(fakeShopeeItemPayload());

    expect($dto->attributeList)->toHaveCount(1)
        ->and($dto->attributeList[0]->originalAttributeName)->toBe('Sabor')
        ->and($dto->attributeList[0]->isMandatory)->toBeTrue()
        ->and($dto->attributeList[0]->attributeValueList[0]->originalValueName)->toBe('Natural');
});

it('faz roundtrip lossless do anuncio', function () {
    $payload = fakeShopeeItemPayload();

    expect(ItemResponseDTO::fromArray($payload)->toArray())->toEqual($payload);
});

it('hidrata a listagem paginada por offset', function () {
    $dto = ItemListResponseDTO::fromArray([
        'item' => [
            ['item_id' => 111, 'item_status' => 'NORMAL', 'update_time' => 1752710400, 'tag' => ['kit' => false]],
            ['item_id' => 222, 'item_status' => 'NORMAL', 'update_time' => 1752710500, 'tag' => ['kit' => true]],
        ],
        'total_count' => 2,
        'has_next_page' => false,
        'next_offset' => 2,
    ]);

    expect($dto->item)->toHaveCount(2)
        ->and($dto->item[0]->itemId)->toBe(111)
        ->and($dto->item[1]->tag?->kit)->toBeTrue()
        ->and($dto->hasNextPage)->toBeFalse();
});

it('hidrata o lote do base_info e expoe o warning nao-fatal', function () {
    $dto = ItemBaseInfoResponseDTO::fromArray([
        'item_list' => [fakeShopeeItemPayload()],
        'warning' => 'item_id 999 not found',
    ]);

    expect($dto->itemList)->toHaveCount(1)
        ->and($dto->itemList[0])->toBeInstanceOf(ItemResponseDTO::class)
        ->and($dto->warning)->toContain('999');
});

it('hidrata variacoes com preco e tier_index posicional', function () {
    $dto = ModelListResponseDTO::fromArray([
        'model' => [[
            'model_id' => 444555,
            'model_name' => '500g,Chocolate',
            'model_sku' => 'SKU-VAR-1',
            'model_status' => 'MODEL_NORMAL',
            'tier_index' => [1, 0],   // 2a opcao do eixo 0 + 1a do eixo 1
            'price_info' => [['currency' => 'BRL', 'original_price' => 120.0, 'current_price' => 99.9]],
            'weight' => '0.5',
        ]],
        'tier_variation' => [
            ['name' => 'Peso', 'option_list' => [['option' => '250g'], ['option' => '500g']]],
            ['name' => 'Sabor', 'option_list' => [['option' => 'Chocolate']]],
        ],
    ]);

    expect($dto->model)->toHaveCount(1)
        ->and($dto->model[0]->modelSku)->toBe('SKU-VAR-1')
        ->and($dto->model[0]->priceInfo[0]->currentPrice)->toBe(99.9)
        ->and($dto->model[0]->tierIndex)->toBe([1, 0])
        // tier_index[0]=1 → 2a opcao do 1o eixo → "500g"
        ->and($dto->tierVariation[0]->optionList[$dto->model[0]->tierIndex[0]]->option)->toBe('500g')
        ->and($dto->tierVariation[1]->name)->toBe('Sabor');
});

it('anuncio sem variacao responde model vazio, nao erro', function () {
    $dto = ModelListResponseDTO::fromArray(['model' => [], 'tier_variation' => []]);

    expect($dto->model)->toBe([])
        ->and($dto->tierVariation)->toBe([]);
});
