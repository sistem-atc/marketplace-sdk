<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductSearchResponseDTO;

/** Item do search — enxuto (id, title, skus com preco). */
function fakeTiktokSearchProduct(): array
{
    return [
        'id' => '1734733366028306218',
        'title' => 'Creatina 300g',
        'status' => 'ACTIVATE',
        'create_time' => 1752710400,
        'update_time' => 1752796800,
        'sales_regions' => ['BR'],
        'recommended_categories' => ['824328'],
        'skus' => [[
            'id' => '1734733366028306219',
            'seller_sku' => 'ECTN2',
            'price' => ['currency' => 'BRL', 'tax_exclusive_price' => '120'],  // STRING
            'inventory' => [['warehouse_id' => '7579136547666036498', 'quantity' => 0]],
        ]],
    ];
}

/** Detalhe do get — COMPLETO. */
function fakeTiktokProductDetail(): array
{
    return [
        'id' => '1734733366028306218',
        'title' => 'Creatina 300g',
        'status' => 'ACTIVATE',
        'product_status' => 'LIVE',
        'description' => '<p>Creatina</p>',
        'create_time' => 1752710400,
        'has_draft' => false,
        'is_cod_allowed' => false,
        'is_not_for_sale' => false,
        'brand' => ['id' => '7078', 'name' => 'Soldiers'],
        'audit' => ['status' => 'AUDIT_SUCCESS', 'pre_approved_reasons' => []],
        'video' => ['id' => 'v1', 'url' => 'https://x/v.mp4', 'cover_url' => 'https://x/c.jpg',
            'format' => 'mp4', 'width' => 720, 'height' => 1280, 'size' => 1048576],
        'package_dimensions' => ['length' => '10', 'width' => '8', 'height' => '15', 'unit' => 'CENTIMETER'],
        'package_weight' => ['value' => '0.35', 'unit' => 'KILOGRAM'],
        'category_chains' => [
            ['id' => '600001', 'parent_id' => '0', 'local_name' => 'Saude', 'is_leaf' => false],
            ['id' => '824328', 'parent_id' => '600001', 'local_name' => 'Suplementos', 'is_leaf' => true],
        ],
        'certifications' => [[
            'id' => 'c1', 'title' => 'Registro',
            'images' => [['uri' => 'img/1', 'width' => 800, 'height' => 600,
                'urls' => ['https://x/1.jpg'], 'thumb_urls' => ['https://x/1t.jpg']]],
        ]],
        'main_images' => [[
            'uri' => 'img/main', 'width' => 1000, 'height' => 1000,
            'urls' => ['https://x/m.jpg'], 'thumb_urls' => ['https://x/mt.jpg'],
        ]],
        'subscribe_info' => [
            'subscribe_status' => 'ON', 'support_subscribe' => true,
            'subscribe_promotion_config' => [['discount_level' => 'L1', 'min_discount' => 5, 'max_discount' => 10]],
        ],
        'product_attributes' => [[
            'id' => '101596', 'name' => 'Lista de Ingredientes',
            'values' => [['id' => '7550424795100366613', 'name' => 'Creatina Monohidratada']],
        ]],
        'skus' => [[
            'id' => '1734733366028306219',
            'seller_sku' => 'ECTN2',
            'price' => ['currency' => 'BRL', 'tax_exclusive_price' => '120', 'sale_price' => '99.90'],
            'inventory' => [['warehouse_id' => '7579136547666036498', 'quantity' => 50]],
            'identifier_code' => ['code' => '7890000000123', 'type' => 'GTIN'],
            'sku_dimensions' => ['length' => '10', 'width' => '8', 'height' => '15', 'unit' => 'CENTIMETER'],
            'sku_weight' => ['value' => '0.35', 'unit' => 'KILOGRAM'],
            'status_info' => ['status' => 'ACTIVATE'],
            'global_listing_policy' => ['inventory_type' => 'WAREHOUSE', 'price_sync' => false],
        ]],
    ];
}

it('hidrata o produto do search com preco STRING', function () {
    $dto = ProductResponseDTO::fromArray(fakeTiktokSearchProduct());

    expect($dto->id)->toBe('1734733366028306218')
        ->and($dto->skus[0]->sellerSku)->toBe('ECTN2')
        ->and($dto->skus[0]->price->taxExclusivePrice)->toBe('120')   // string
        ->and($dto->skus[0]->inventory[0]->quantity)->toBe(0)
        // detalhe nao vem no search:
        ->and($dto->brand)->toBeNull()
        ->and($dto->productAttributes)->toBeNull();
});

it('mapeia o detalhe INTEIRO do get — inclusive campos que o app nao usa hoje', function () {
    $dto = ProductResponseDTO::fromArray(fakeTiktokProductDetail());

    // dados que hoje ninguem le, mas a API entrega:
    expect($dto->brand?->name)->toBe('Soldiers')
        ->and($dto->video?->url)->toContain('.mp4')
        ->and($dto->packageWeight?->value)->toBe('0.35')             // string
        ->and($dto->categoryChains)->toHaveCount(2)
        ->and($dto->categoryChains[1]->isLeaf)->toBeTrue()
        ->and($dto->certifications[0]->images[0]->uri)->toBe('img/1')
        ->and($dto->subscribeInfo?->supportSubscribe)->toBeTrue()
        // o GTIN/EAN do SKU — util pra fiscal, hoje suprimido:
        ->and($dto->skus[0]->identifierCode?->code)->toBe('7890000000123')
        ->and($dto->skus[0]->identifierCode?->type)->toBe('GTIN');
});

it('mantem product_attributes (o que o PIM consome hoje)', function () {
    $dto = ProductResponseDTO::fromArray(fakeTiktokProductDetail());

    expect($dto->productAttributes)->toHaveCount(1)
        ->and($dto->productAttributes[0]->name)->toBe('Lista de Ingredientes')
        ->and($dto->productAttributes[0]->values[0]->name)->toBe('Creatina Monohidratada');
});

it('faz roundtrip lossless do search', function () {
    $p = fakeTiktokSearchProduct();
    expect(ProductResponseDTO::fromArray($p)->toArray())->toEqual($p);
});

it('faz roundtrip lossless do detalhe completo', function () {
    $p = fakeTiktokProductDetail();
    expect(ProductResponseDTO::fromArray($p)->toArray())->toEqual($p);
});

it('hidrata o envelope do search com page token', function () {
    $dto = ProductSearchResponseDTO::fromArray([
        'products' => [fakeTiktokSearchProduct()],
        'next_page_token' => 'tok123',
        'total_count' => 42,
    ]);

    expect($dto->products)->toHaveCount(1)
        ->and($dto->products[0])->toBeInstanceOf(ProductResponseDTO::class)
        ->and($dto->nextPageToken)->toBe('tok123');
});
