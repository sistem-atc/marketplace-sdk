<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalCategoryAttributesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalCategoryRulesResponseDTO;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttCatRuleFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'), true);
}

function ttCatRuleProducts()
{
    return MarketPlaces::Tiktok()->products(new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: ['app_key' => 'ak', 'app_secret' => 'as', 'shop_cipher' => 'cipher1'],
        active: true,
        expired: false,
    ));
}

dataset('tt_global_category_fixtures', [
    'global attributes' => ['get-global-attributes', GlobalCategoryAttributesResponseDTO::class],
    'global category rules' => ['get-global-category-rules', GlobalCategoryRulesResponseDTO::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $payload = ttCatRuleFixture($slug);
    $perdidos = RecursiveFieldCoverage::missing($payload, $dto::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
})->with('tt_global_category_fixtures');

it('faz roundtrip lossless do payload inteiro', function (string $slug, string $dto) {
    $payload = ttCatRuleFixture($slug);

    expect($dto::fromArray($payload)->toArray())->toEqual($payload);
})->with('tt_global_category_fixtures');

// ─────────────────────────────────────────────────────────────────────────
// Comportamento real
// ─────────────────────────────────────────────────────────────────────────

it('a chave de obrigatoriedade do atributo e o TYPO `is_requried` da API', function () {
    $payload = ttCatRuleFixture('get-global-attributes');
    $attr = GlobalCategoryAttributesResponseDTO::fromArray($payload)->attributes[0];

    // O DTO expoe `isRequired` (grafia certa) mas serializa de volta com a
    // grafia ERRADA que o TikTok publicou — via #[JsonKey].
    expect($attr->isRequired)->toBeFalse()
        ->and(GlobalCategoryAttributesResponseDTO::fromArray($payload)->toArray()['attributes'][0])
        ->toHaveKey('is_requried');
});

it('is_required=false NAO significa opcional — a exigencia e por mercado', function () {
    $attr = GlobalCategoryAttributesResponseDTO::fromArray(ttCatRuleFixture('get-global-attributes'))->attributes[0];

    expect($attr->isRequired)->toBeFalse()
        ->and($attr->requiredRegions)->toBe(['GB', 'MY', 'PH', 'SG', 'TH', 'VN'])
        ->and($attr->optionalRegions)->toBe(['US'])
        ->and($attr->requirementConditions[0]->region)->toBe('IE')
        ->and($attr->requirementConditions[0]->conditionType)->toBe('VALUE_ID_MATCH');
});

it('a condicao aponta atributo/valor de OUTRO atributo, nao do proprio', function () {
    $attr = GlobalCategoryAttributesResponseDTO::fromArray(ttCatRuleFixture('get-global-attributes'))->attributes[0];

    expect($attr->id)->toBe('100392')
        ->and($attr->requirementConditions[0]->attributeId)->toBe('101610')
        ->and($attr->requirementConditions[0]->attributeValueId)->toBe('1024358');
});

it('regras trazem GPSR (responsavel e fabricante) com o MESMO shape', function () {
    $dto = GlobalCategoryRulesResponseDTO::fromArray(ttCatRuleFixture('get-global-category-rules'));

    expect($dto->responsiblePerson->requiredRegions)->toBe(['DE', 'ES'])
        ->and($dto->manufacturer->optionalRegions)->toBe(['IE'])
        ->and($dto->sizeChart->isSupported)->toBeTrue()
        ->and($dto->productCertifications[0]->sampleImageUrl)->toContain('byted.org');
});

it('getGlobalAttributes usa GET na 202309 e repassa a query', function () {
    Http::fake([
        '*/product/202309/categories/600001/global_attributes*' => Http::response([
            'code' => 0,
            'data' => ttCatRuleFixture('get-global-attributes'),
        ]),
    ]);

    $dto = ttCatRuleProducts()->getGlobalAttributes('600001', ['locale' => 'en-US', 'category_version' => 'v2']);

    expect($dto->attributes)->toHaveCount(1);
    Http::assertSent(fn ($r) => $r->method() === 'GET'
        && str_contains($r->url(), '/product/202309/categories/600001/global_attributes')
        && str_contains($r->url(), 'locale=en-US')
        && str_contains($r->url(), 'category_version=v2'));
});

it('getGlobalCategoryRules usa GET em /global_rules', function () {
    Http::fake([
        '*/product/202309/categories/600001/global_rules*' => Http::response([
            'code' => 0,
            'data' => ttCatRuleFixture('get-global-category-rules'),
        ]),
    ]);

    $dto = ttCatRuleProducts()->getGlobalCategoryRules('600001');

    expect($dto->productCertifications[0]->id)->toBe('7267563252536723205');
    Http::assertSent(fn ($r) => $r->method() === 'GET'
        && str_contains($r->url(), '/product/202309/categories/600001/global_rules'));
});
