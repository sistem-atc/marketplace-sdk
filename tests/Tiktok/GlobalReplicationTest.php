<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalReplicateProductResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalReplicatedProductsResponseDTO;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttReplFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'), true);
}

function ttReplProducts()
{
    return MarketPlaces::Tiktok()->products(new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: ['app_key' => 'ak', 'app_secret' => 'as', 'shop_cipher' => 'cipher1'],
        active: true,
        expired: false,
    ));
}

dataset('tt_replication_fixtures', [
    'replicated products' => ['get-global-replicated-products', GlobalReplicatedProductsResponseDTO::class],
    'replicate product' => ['replicate-product', GlobalReplicateProductResponseDTO::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $payload = ttReplFixture($slug);
    $perdidos = RecursiveFieldCoverage::missing($payload, $dto::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
})->with('tt_replication_fixtures');

it('faz roundtrip lossless do payload inteiro', function (string $slug, string $dto) {
    $payload = ttReplFixture($slug);

    expect($dto::fromArray($payload)->toArray())->toEqual($payload);
})->with('tt_replication_fixtures');

// ─────────────────────────────────────────────────────────────────────────
// Comportamento real
// ─────────────────────────────────────────────────────────────────────────

it('a replica tem product_id e status PROPRIOS por mercado', function () {
    $repl = GlobalReplicatedProductsResponseDTO::fromArray(
        ttReplFixture('get-global-replicated-products')
    )->replicatedProducts[0];

    // O id devolvido e' o da replica NAQUELE mercado, nao o id consultado.
    expect($repl->region)->toBe('US')
        ->and($repl->shopId)->toBe('1732057700058170433')
        ->and($repl->productId)->toBe('1732057700058170433')
        ->and($repl->productStatus)->toBe('ACTIVE');
});

it('replicateProduct devolve code 0 no envelope e a falha real dentro de errors[]', function () {
    Http::fake([
        '*/product/202507/products/1729592969712207008/global_replicate*' => Http::response([
            'code' => 0,
            'message' => 'Success',
            'data' => ttReplFixture('replicate-product'),
        ]),
    ]);

    // Nao estoura excecao: o BaseMethods so' reage a code != 0.
    $dto = ttReplProducts()->replicateProduct('1729592969712207008', [[
        'region' => 'US',
        'skus' => [[
            'source_sku_id' => '1732057700058170433',
            'price' => ['currency' => 'EUR', 'sale_price' => '100'],
            'inventory' => [['warehouse_id' => '1732057700058170433', 'quantity' => 100]],
        ]],
        'inventory_mode' => 'SHARED',
    ]], inventoryMode: 'SHARED');

    expect($dto->errors)->toHaveCount(1)
        ->and($dto->errors[0]->code)->toBe(12052223)
        ->and($dto->errors[0]->detail->region)->toBe('US');

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && str_contains($r->url(), '/product/202507/products/1729592969712207008/global_replicate')
        && str_contains($r->body(), '"sale_price":"100"')
        && str_contains($r->body(), '"inventory_mode":"SHARED"'));
});

it('inventory_mode de topo some do corpo quando nao informado', function () {
    Http::fake(['*global_replicate*' => Http::response(['code' => 0, 'data' => ttReplFixture('replicate-product')])]);

    ttReplProducts()->replicateProduct('1', [['region' => 'MX']]);

    Http::assertSent(fn ($r) => str_contains($r->body(), '"replicate_target"')
        && ! str_contains($r->body(), '"inventory_mode"'));
});

it('getGlobalReplicatedProducts usa GET na 202507 sob /products/ (id LOCAL)', function () {
    Http::fake([
        '*/product/202507/products/1732064081124754497/replicated_products*' => Http::response([
            'code' => 0,
            'data' => ttReplFixture('get-global-replicated-products'),
        ]),
    ]);

    $dto = ttReplProducts()->getGlobalReplicatedProducts('1732064081124754497');

    expect($dto->replicatedProducts)->toHaveCount(1);
    Http::assertSent(fn ($r) => $r->method() === 'GET'
        && str_contains($r->url(), '/product/202507/products/1732064081124754497/replicated_products'));
});
