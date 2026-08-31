<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalCategoryListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalCategoryRecommendResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalListingRulesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductDeleteResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductEditResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductPublishResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\GlobalProductSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ImageTranslationTaskCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ImageTranslationTaskListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ManufacturerCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ManufacturerSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ResponsiblePersonCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ResponsiblePersonSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\UploadedProductFileResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\UploadedProductImageResponseDTO;

/** Conteudo de `data` do exemplo OFICIAL da doc do endpoint. */
function ttGlobalFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'), true);
}

/**
 * Diferenca RECURSIVA original -> roundtrip.
 *
 * O teste do padrao compara so' as chaves de 1o nivel — e' justamente no fundo
 * (sku > sales_attributes > sku_img) que campo some sem ninguem notar. Aqui
 * qualquer chave ou valor perdido em qualquer profundidade aparece.
 *
 * @return list<string> caminhos perdidos
 */
function ttGlobalLostPaths(mixed $original, mixed $roundtrip, string $path = ''): array
{
    if (! is_array($original)) {
        return $original === $roundtrip ? [] : [$path.' (valor mudou)'];
    }

    if (! is_array($roundtrip)) {
        return [$path.' (deixou de ser array)'];
    }

    $lost = [];
    foreach ($original as $key => $value) {
        if (! array_key_exists($key, $roundtrip)) {
            $lost[] = $path.'.'.$key;

            continue;
        }
        $lost = array_merge($lost, ttGlobalLostPaths($value, $roundtrip[$key], $path.'.'.$key));
    }

    return $lost;
}

dataset('exemplos oficiais do produto global', [
    'get global product' => ['get-global-product', GlobalProductResponseDTO::class],
    'search global products' => ['search-global-products', GlobalProductSearchResponseDTO::class],
    'get global categories' => ['get-global-categories', GlobalCategoryListResponseDTO::class],
    'get global listing rules' => ['get-global-listing-rules', GlobalListingRulesResponseDTO::class],
    'recommend global categories' => ['recommend-global-categories', GlobalCategoryRecommendResponseDTO::class],
    'create global product' => ['create-global-product', GlobalProductCreateResponseDTO::class],
    'edit global product' => ['edit-global-product', GlobalProductEditResponseDTO::class],
    'partial edit global product' => ['partial-edit-global-product', GlobalProductEditResponseDTO::class],
    'publish global product' => ['publish-global-product', GlobalProductPublishResponseDTO::class],
    'delete global products' => ['delete-global-products', GlobalProductDeleteResponseDTO::class],
    'create image translation tasks' => ['create-image-translation-tasks', ImageTranslationTaskCreateResponseDTO::class],
    'get image translation tasks' => ['get-image-translation-tasks', ImageTranslationTaskListResponseDTO::class],
    'create manufacturer' => ['create-manufacturer', ManufacturerCreateResponseDTO::class],
    'search manufacturers' => ['search-manufacturers', ManufacturerSearchResponseDTO::class],
    'create responsible person' => ['create-responsible-person', ResponsiblePersonCreateResponseDTO::class],
    'search responsible persons' => ['search-responsible-persons', ResponsiblePersonSearchResponseDTO::class],
    'upload product file' => ['upload-product-file', UploadedProductFileResponseDTO::class],
    'upload product image' => ['upload-product-image', UploadedProductImageResponseDTO::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $payload = ttGlobalFixture($slug);

    $perdidos = ttGlobalLostPaths($payload, $dto::fromArray($payload)->toArray(), $slug);

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
})->with('exemplos oficiais do produto global');

it('faz roundtrip lossless do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $payload = ttGlobalFixture($slug);

    expect($dto::fromArray($payload)->toArray())->toEqual($payload);
})->with('exemplos oficiais do produto global');

describe('produto global — comportamento que surpreende', function () {
    it('cobra o preco em `amount` STRING, nao no tax_exclusive_price do produto local', function () {
        $sku = GlobalProductResponseDTO::fromArray(ttGlobalFixture('get-global-product'))->skus[0];

        // O global e' preco UNIFORME pre-imposto em USD/EUR; o local converte
        // na publicacao. Quem procurar taxExclusivePrice aqui nao acha.
        expect($sku->price?->amount)->toBe('1.01')
            ->and($sku->price?->amount)->toBeString()
            ->and($sku->price?->currency)->toBe('USD')
            ->and($sku->price?->unitPrice)->toBe('1.00');
    });

    it('usa global_warehouse_id no estoque — nao o warehouse_id do produto local', function () {
        $sku = GlobalProductResponseDTO::fromArray(ttGlobalFixture('get-global-product'))->skus[0];

        expect($sku->inventory[0]->globalWarehouseId)->toBe('6966568648651605766')
            ->and($sku->globalQuantity)->toBe(100);
    });

    it('entrega o de/para global->local, que e como se casa pedido de shop local com o catalogo', function () {
        $local = GlobalProductResponseDTO::fromArray(ttGlobalFixture('get-global-product'))->products[0];

        expect($local->region)->toBe('GB')
            ->and($local->id)->toBe('1729592969712207922')
            ->and($local->skuMappings[0]->globalSkuId)->toBe('1729592969712207234')
            ->and($local->skuMappings[0]->localSkuId)->toBe('1729592969712207230')
            // ids de atributo tambem sao por-mercado:
            ->and($local->skuMappings[0]->salesAttributeMappings[0]->localValueId)->toBe('1001064');
    });

    it('mantem o `manufacturer` DEPRECIADO, que e o unico contato de fabricante em produto antigo', function () {
        $dto = GlobalProductResponseDTO::fromArray(ttGlobalFixture('get-global-product'));

        expect($dto->manufacturer?->name)->toBe('Sample Manufacturer Name')
            ->and($dto->manufacturer?->email)->toContain('samplemanufacturer101')
            // a fonte nova, que so' traz ids:
            ->and($dto->manufacturerIds)->toBe(['67726293bf1eb1e92401a']);
    });

    it('so traz o id do video e da marca — o produto local traz url e nome', function () {
        $dto = GlobalProductResponseDTO::fromArray(ttGlobalFixture('get-global-product'));

        expect($dto->video?->id)->toBe('v09ea0g40000cj91373c77u3mid3g1s0')
            ->and($dto->brand?->id)->toBe('7082427311584347905')
            ->and($dto->category?->id)->toBe('600002');
    });

    it('separa arquivos e imagens da certificacao em listas distintas', function () {
        $cert = GlobalProductResponseDTO::fromArray(ttGlobalFixture('get-global-product'))->certifications[0];

        expect($cert->files[0]->format)->toBe('PDF')
            ->and($cert->files[0]->name)->toBe('SNI.PDF')
            ->and($cert->images[0]->uri)->toContain('tos-maliva');
    });

    it('devolve a lista do search em `global_products`, nao em `products`', function () {
        $dto = GlobalProductSearchResponseDTO::fromArray(ttGlobalFixture('search-global-products'));

        expect($dto->globalProducts)->toHaveCount(1)
            ->and($dto->globalProducts[0])->toBeInstanceOf(GlobalProductResponseDTO::class)
            ->and($dto->globalProducts[0]->status)->toBe('PUBLISHED')
            // o search nao traz detalhe — null aqui e' "nao veio", nao "nao existe":
            ->and($dto->globalProducts[0]->skus[0]->price)->toBeNull()
            ->and($dto->nextPageToken)->toBe('b2Zmc2V0PTAK');
    });
});

describe('publicacao e exclusao — sucesso PARCIAL', function () {
    it('reporta falha por mercado dentro de uma resposta bem-sucedida', function () {
        $dto = GlobalProductPublishResponseDTO::fromArray(ttGlobalFixture('publish-global-product'));

        // code 0 na chamada, mas o status real e' por regiao:
        expect($dto->publishResult[0]->region)->toBe('MY')
            ->and($dto->publishResult[0]->status)->toBe('SUCCESS')
            ->and($dto->publishResult[0]->failReasons[0]->message)->toBe('The publish region is inactive.');
    });

    it('nomeia o atributo do SKU local como `sale_attributes` (singular), ao contrario do global', function () {
        $sku = GlobalProductPublishResponseDTO::fromArray(ttGlobalFixture('publish-global-product'))->products[0]->skus[0];

        expect($sku->saleAttributes[0]->id)->toBe('100001')
            ->and($sku->relatedGlobalSkuId)->toBe('1729696746528737924');
    });

    it('lista em `errors` SO os produtos que falharam ao apagar', function () {
        $dto = GlobalProductDeleteResponseDTO::fromArray(ttGlobalFixture('delete-global-products'));

        // `code` aqui e' INT (codigo de erro), diferente dos ids que sao string:
        expect($dto->errors[0]->code)->toBe(12019114)
            ->and($dto->errors[0]->detail?->globalProductId)->toBe('1729715829872102020');

        // lista ausente = tudo apagado
        expect(GlobalProductDeleteResponseDTO::fromArray([])->errors)->toBeNull();
    });

    it('devolve `publish_results` no PLURAL na edicao (o publish usa singular)', function () {
        $dto = GlobalProductEditResponseDTO::fromArray(ttGlobalFixture('edit-global-product'));

        expect($dto->publishResults[0]->status)->toBe('SUCCESS')
            ->and($dto->globalSkus[0]->sellerSku)->toBe('Color-Red-XM01');
    });

    it('ecoa no create os ids gerados pro sales attribute customizado', function () {
        $dto = GlobalProductCreateResponseDTO::fromArray(ttGlobalFixture('create-global-product'));

        expect($dto->globalProductId)->toBe('1729592969712207008')
            ->and($dto->globalSkus[0]->salesAttributes[0]->valueId)->toBe('1729592969712207123');
    });
});

describe('categorias e regras de listagem', function () {
    it('marca a permissao da LOJA na categoria — nao basta a categoria existir', function () {
        $cat = GlobalCategoryListResponseDTO::fromArray(ttGlobalFixture('get-global-categories'))->categories[0];

        expect($cat->permissionStatuses)->toBe(['AVAILABLE'])
            ->and($cat->isLeaf)->toBeFalse()   // so' folha aceita produto
            ->and($cat->parentId)->toBe('0');  // raiz
    });

    it('ja entrega a folha recomendada pronta em leaf_category_id', function () {
        $dto = GlobalCategoryRecommendResponseDTO::fromArray(ttGlobalFixture('recommend-global-categories'));

        expect($dto->leafCategoryId)->toBe('605254')
            ->and($dto->categories[0]->level)->toBe(3)
            ->and($dto->categories[0]->isLeaf)->toBeTrue();
    });

    it('aceita listing_methods como STRING, que e o que a API manda apesar da doc dizer lista', function () {
        $dto = GlobalListingRulesResponseDTO::fromArray(ttGlobalFixture('get-global-listing-rules'));

        expect($dto->listingMethods)->toBe('GLOBAL_PUBLISHING');

        // e continua funcionando se um dia vier lista, como a doc promete:
        expect(GlobalListingRulesResponseDTO::fromArray(['listing_methods' => ['GLOBAL_PUBLISHING']])->listingMethods)
            ->toBe(['GLOBAL_PUBLISHING']);
    });

    it('so lista armazens associados nos modos SHARED/DYNAMIC', function () {
        $rule = GlobalListingRulesResponseDTO::fromArray(ttGlobalFixture('get-global-listing-rules'))->inventoryRules[0];

        expect($rule->allocationMode)->toBe('SHARED')
            ->and($rule->associatedWarehouses[0]->region)->toBe('DE');
    });
});

describe('traducao de imagem e uploads', function () {
    it('cria UMA tarefa por par (imagem, idioma)', function () {
        $dto = ImageTranslationTaskCreateResponseDTO::fromArray(ttGlobalFixture('create-image-translation-tasks'));

        expect($dto->translationTasks)->toHaveCount(1)
            ->and($dto->translationTasks[0]->targetLanguage)->toBe('it-IT')
            ->and($dto->translationTasks[0]->imageUri)->toContain('748e39ecff38453ab8396a36a53dbb92');
    });

    it('devolve uri E url da imagem traduzida — a uri e que volta pro produto', function () {
        $task = ImageTranslationTaskListResponseDTO::fromArray(ttGlobalFixture('get-image-translation-tasks'))->translationTasks[0];

        expect($task->status)->toBe('COMPLETED')
            ->and($task->translatedImage?->uri)->toContain('53b55d6e8cdf1f315affa7e70b45707d')
            ->and($task->originalImage?->url)->toStartWith('https://')
            // failReason e' texto livre — nao case logica nele:
            ->and($task->failReason)->toBeString();
    });

    it('identifica imagem por URI e arquivo por ID', function () {
        $img = UploadedProductImageResponseDTO::fromArray(ttGlobalFixture('upload-product-image'));
        $file = UploadedProductFileResponseDTO::fromArray(ttGlobalFixture('upload-product-file'));

        expect($img->uri)->toBe('tos-maliva-i-o3syd03w52-us/c668cdf70b7f483c94dbe')
            ->and($img->useCase)->toBe('MAIN_IMAGE')
            // dimensoes sao as APOS o ajuste automatico de proporcao do TikTok:
            ->and($img->height)->toBe(720)
            ->and($img->width)->toBe(720)
            ->and($file->id)->toBe('v09e40f40000cfu0ovhc77ub7fl97k4w')
            ->and($file->format)->toBe('PDF');
    });
});

describe('compliance UE — fabricante e pessoa responsavel', function () {
    it('guarda o fabricante por LOCALE, sem nome canonico', function () {
        $m = ManufacturerSearchResponseDTO::fromArray(ttGlobalFixture('search-manufacturers'))->manufacturers[0];

        expect($m->regionalProfiles[0]->locale)->toBe('en-IE')
            ->and($m->regionalProfiles[0]->registeredTradeName)->toBe('TikTok Shop')
            // endereco do fabricante e' STRING (o do responsavel e' objeto):
            ->and($m->regionalProfiles[0]->address)->toBeString();
    });

    it('trata telefone do fabricante como objeto com availability — UNAVAILABLE e valido', function () {
        $phone = ManufacturerSearchResponseDTO::fromArray(ttGlobalFixture('search-manufacturers'))
            ->manufacturers[0]->regionalProfiles[0]->phoneNumber;

        expect($phone?->availability)->toBe('AVAILABLE')
            ->and($phone?->countryCode)->toBe('+353');   // ja' vem com o "+"
    });

    it('mantem os campos DEPRECIADOS do endereco do responsavel, que a API continua mandando', function () {
        $addr = ResponsiblePersonSearchResponseDTO::fromArray(ttGlobalFixture('search-responsible-persons'))
            ->responsiblePersons[0]->regionalProfiles[0]->address;

        expect($addr?->streetAddressLine1)->toContain('Cardiff Ln')
            ->and($addr?->postalCode)->toBe('D02 HD23')
            ->and($addr?->country)->toBe('IE')
            // city/district/province vieram como "-": depreciados, mas presentes
            ->and($addr?->city)->toBe('-')
            ->and($addr?->district)->toBe('-')
            ->and($addr?->province)->toBe('-');
    });

    it('devolve so o id na criacao dos cadastros de compliance', function () {
        expect(ManufacturerCreateResponseDTO::fromArray(ttGlobalFixture('create-manufacturer'))->manufacturerId)
            ->toBe('66d3cbe4d9c8b09ddca932a7')
            ->and(ResponsiblePersonCreateResponseDTO::fromArray(ttGlobalFixture('create-responsible-person'))->responsiblePersonId)
            ->toBe('66d3cbe4d9c8b09ddca023f1');
    });

    it('pagina os dois cadastros por token opaco', function () {
        expect(ManufacturerSearchResponseDTO::fromArray(ttGlobalFixture('search-manufacturers'))->nextPageToken)
            ->toBe('66d3cbe3d9c8b09ddca932a1')
            ->and(ResponsiblePersonSearchResponseDTO::fromArray(ttGlobalFixture('search-responsible-persons'))->totalCount)
            ->toBe(26);
    });
});
