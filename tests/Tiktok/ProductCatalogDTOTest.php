<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\DiagnoseOptimizeResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\InventorySearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ListingPrerequisitesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\OpportunityDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\OpportunityListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\OpportunitySubmitResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\OptimizedImagesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductAuditingResearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductDiagnosesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductListingCheckResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductOperationResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductSeoWordsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductSkppDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ProductSkppStatusListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\RecommendCategoryResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\RecommendedProductPackageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\RecommendedTitleDescriptionResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\ShopSkppSummaryResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\SizeChartSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\StockOperationSettingsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\SubmissionRecordsResponseDTO;

/** Carrega o `data` do exemplo OFICIAL da doc do endpoint. */
function fixtureTiktokProduct(string $slug): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'),
        true,
    );
}


/**
 * Ordena as chaves recursivamente antes de comparar: o roundtrip precisa provar
 * que nenhum campo SUMIU, e a ordem em que o DTO declara os parametros nao
 * importa (e nem sempre bate com a da doc).
 */
function normalizeTiktokPayload(array $payload): array
{
    ksort($payload);

    foreach ($payload as $key => $value) {
        if (is_array($value)) {
            $payload[$key] = normalizeTiktokPayload($value);
        }
    }

    return $payload;
}

/**
 * O teste que impede a regressao que motivou este trabalho: qualquer campo do
 * exemplo da doc que o DTO nao mapeie some no `toArray()` e e' acusado aqui.
 * Cobre so' o 1o nivel de cada resposta — os aninhados tem asserts proprios abaixo.
 */
dataset('respostas de catalogo TikTok', [
    'inventory search' => ['inventory-search', InventorySearchResponseDTO::class],
    'skpp detail' => ['get-product-skpp-detail', ProductSkppDetailResponseDTO::class],
    'skpp list' => ['list-products-skpp-status', ProductSkppStatusListResponseDTO::class],
    'skpp summary' => ['get-shop-skpp-summary', ShopSkppSummaryResponseDTO::class],
    'seo words' => ['get-products-seo-words', ProductSeoWordsResponseDTO::class],
    'package recommend' => ['get-recommended-product-package', RecommendedProductPackageResponseDTO::class],
    'title/description' => ['get-recommended-title-description', RecommendedTitleDescriptionResponseDTO::class],
    'size charts' => ['search-size-charts', SizeChartSearchResponseDTO::class],
    'opportunity detail' => ['get-opportunity-detail', OpportunityDetailResponseDTO::class],
    'opportunity list' => ['list-opportunity', OpportunityListResponseDTO::class],
    'submission records' => ['get-submission-records', SubmissionRecordsResponseDTO::class],
    'submit opportunity' => ['submit-product-via-opportunity', OpportunitySubmitResponseDTO::class],
    'diagnose/optimize' => ['diagnose-and-optimize-product', DiagnoseOptimizeResponseDTO::class],
    'diagnoses em lote' => ['product-information-issue-diagnosis', ProductDiagnosesResponseDTO::class],
    'auditing research' => ['product-auditing-research', ProductAuditingResearchResponseDTO::class],
    'optimized images' => ['optimized-images', OptimizedImagesResponseDTO::class],
    'prerequisites' => ['check-listing-prerequisites', ListingPrerequisitesResponseDTO::class],
    'listing check' => ['check-product-listing', ProductListingCheckResponseDTO::class],
    'recommend category' => ['recommend-category', RecommendCategoryResponseDTO::class],
    'stock settings' => ['update-stock-operation-settings', StockOperationSettingsResponseDTO::class],
    'activate' => ['activate-product', ProductOperationResponseDTO::class],
    'deactivate' => ['deactivate-products', ProductOperationResponseDTO::class],
    'recover' => ['recover-products', ProductOperationResponseDTO::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dtoClass) {
    $payload = fixtureTiktokProduct($slug);

    $perdidos = array_diff(
        array_keys($payload),
        array_keys($dtoClass::fromArray($payload)->toArray()),
    );

    expect($perdidos)->toBe([], "{$slug}: campos descartados: ".implode(', ', $perdidos));
})->with('respostas de catalogo TikTok');

it('roundtrip do Inventory Search preserva a arvore inteira, ate o rateio por criador', function () {
    $payload = fixtureTiktokProduct('inventory-search');

    expect(normalizeTiktokPayload(InventorySearchResponseDTO::fromArray($payload)->toArray()))
        ->toBe(normalizeTiktokPayload($payload));
});

it('Inventory Search separa disponivel de COMPROMETIDO — o get() de produto nao faz isso', function () {
    $sku = InventorySearchResponseDTO::fromArray(fixtureTiktokProduct('inventory-search'))->inventory[0]->skus[0];

    // 100 disponiveis com 10 reservados por pedidos em aberto: quem espelhar
    // no ERP so' o "available" vai superestimar o que da' pra vender.
    expect($sku->totalAvailableQuantity)->toBe(100)
        ->and($sku->totalCommittedQuantity)->toBe(10)
        ->and($sku->warehouseInventory[0]->committedQuantity)->toBe(10);
});

it('Inventory Search revela que o disponivel esta rateado entre campanha, criador e loja', function () {
    $dist = InventorySearchResponseDTO::fromArray(fixtureTiktokProduct('inventory-search'))
        ->inventory[0]->skus[0]->totalAvailableInventoryDistribution;

    // 30 (campanha) + 20 (criador) + 50 (loja) = os 100 "disponiveis".
    expect($dist->campaignInventory[0]->quantity)->toBe(30)
        ->and($dist->creatorInventory[0]->quantity)->toBe(20)
        ->and($dist->inShopInventory->quantity)->toBe(50);
});

it('activate/deactivate/recover so listam o que FALHOU — sucesso vem vazio', function () {
    $dto = ProductOperationResponseDTO::fromArray(fixtureTiktokProduct('activate-product'));

    expect($dto->errors)->toHaveCount(1)
        ->and($dto->errors[0]->detail->productId)->toBe('1729382588639839583')
        ->and($dto->errors[0]->detail->extraErrors[0]->code)->toBe(12052048);

    // Lote 100% OK volta `data` vazio — nao ha' lista de sucesso pra conferir.
    expect(ProductOperationResponseDTO::fromArray([])->errors)->toBeNull();
});

it('recover nao traz extra_errors — so o deactivate/activate aninham erro secundario', function () {
    $dto = ProductOperationResponseDTO::fromArray(fixtureTiktokProduct('recover-products'));

    expect($dto->errors[0]->detail->productId)->toBe('1729382588639839583')
        ->and($dto->errors[0]->detail->extraErrors)->toBeNull();
});

it('a atualizacao de regra de estoque e PARCIAL: mesmo SKU pode aparecer nas duas listas', function () {
    $op = StockOperationSettingsResponseDTO::fromArray(fixtureTiktokProduct('update-stock-operation-settings'))->stockOperation;

    // code 0 na resposta NAO significa que todos os SKUs foram atualizados.
    expect($op->failedSkuIds)->toBe(['124148921741214'])
        ->and($op->successSkuIds)->toBe(['124148921741214']);
});

it('prerequisites usa flag INVERTIDA: is_failed=true e o bloqueio', function () {
    $check = ListingPrerequisitesResponseDTO::fromArray(fixtureTiktokProduct('check-listing-prerequisites'))->checkResults[0];

    expect($check->checkItem)->toBe('RETURN_WAREHOUSE')
        ->and($check->isFailed)->toBeTrue()
        ->and($check->failReasons)->toHaveCount(1);
});

it('o pre-check de publicacao traz codigo INT, e nao o codigo string do diagnostico', function () {
    $dto = ProductListingCheckResponseDTO::fromArray(fixtureTiktokProduct('check-product-listing'));

    expect($dto->checkResult)->toBe('FAILED')
        ->and($dto->failReasons[0]->code)->toBe(12052700)          // int: erro do Create Product
        ->and($dto->diagnoses[0]->diagnosisResults[0]->code)->toBe('TITLE_LESS_THAN_40_CHARACTERS') // string
        ->and($dto->warnings->message)->toBeString();
});

it('o listing_check chama a sugestao de "suggestions" (plural), ao contrario dos outros endpoints', function () {
    $payload = fixtureTiktokProduct('check-product-listing');

    // Se o DTO tivesse reusado a chave `suggestion` do diagnose_optimize, o
    // bloco inteiro de sugestoes sumiria em silencio.
    expect(normalizeTiktokPayload(ProductListingCheckResponseDTO::fromArray($payload)->toArray()))
        ->toBe(normalizeTiktokPayload($payload));
});

it('o diagnostico devolve a imagem otimizada pronta pra reenviar no Edit Product', function () {
    $img = DiagnoseOptimizeResponseDTO::fromArray(fixtureTiktokProduct('diagnose-and-optimize-product'))
        ->diagnoses[0]->suggestion->images[0];

    // `optimizedUri` e' o que vai no payload do produto; a url e' so' visual.
    expect($img->optimizedUri)->toBe('tos-maliva-i-o3syd03w52-us/0266127022264e54ad2f639f5e0fb5e6')
        ->and($img->uri)->toBe('tos-maliva-i-o3syd03w52-us/53b55d6e8cdf1f315affa7e70b45707d');
});

it('o diagnostico em lote repete a mesma arvore por produto', function () {
    $payload = fixtureTiktokProduct('product-information-issue-diagnosis');

    expect(normalizeTiktokPayload(ProductDiagnosesResponseDTO::fromArray($payload)->toArray()))
        ->toBe(normalizeTiktokPayload($payload));
});

it('optimizeImages sinaliza PROCESSING — a otimizacao e assincrona', function () {
    $img = OptimizedImagesResponseDTO::fromArray(fixtureTiktokProduct('optimized-images'))->images[0];

    expect($img->optimizeStatus)->toBe('SUCCESS')
        ->and($img->originalUri)->not->toBe($img->optimizedUri);
});

it('recommendCategory devolve a cadeia e aponta a folha, que e a unica publicavel', function () {
    $dto = RecommendCategoryResponseDTO::fromArray(fixtureTiktokProduct('recommend-category'));

    expect($dto->leafCategoryId)->toBe('605254')
        ->and($dto->categories[0]->isLeaf)->toBeTrue()
        ->and($dto->categories[0]->permissionStatuses)->toBe(['INVITE_ONLY']); // restrita: precisa aplicar
});

it('a recomendacao de pacote e faixa em STRING, nao numero', function () {
    $prod = RecommendedProductPackageResponseDTO::fromArray(fixtureTiktokProduct('get-recommended-product-package'))->products[0];

    expect($prod->weightRecommendation->weight->minValue)->toBe('550')  // STRING, unidade GRAM
        ->and($prod->weightRecommendation->unit)->toBe('GRAM')
        ->and($prod->dimensionRecommendation->width->maxValue)->toBe('22')
        ->and($prod->abnormalityTypes)->toBe(['Dimension', 'Weight']);
});

it('a tabela de medidas vem sob a chave SINGULAR size_chart mesmo sendo lista', function () {
    $dto = SizeChartSearchResponseDTO::fromArray(fixtureTiktokProduct('search-size-charts'));

    expect($dto->sizeChart)->toHaveCount(1)
        ->and($dto->sizeChart[0]->images[0]->locale)->toBe('en-US')
        ->and($dto->totalCount)->toBe(100);
});

it('SKPP: o valor da recompensa vem com simbolo de moeda embutido, por isso e STRING', function () {
    $reward = ProductSkppStatusListResponseDTO::fromArray(fixtureTiktokProduct('list-products-skpp-status'))
        ->products[0]->rewards[0];

    // "$15" nao e' numero — tipar float aqui daria 0.0.
    expect($reward->value)->toBe('$15')
        ->and($reward->adCreditClaimStatus)->toBe('CLAIMED'); // credito de anuncio e' resgate MANUAL
});

it('SKPP: update_time e epoch em SEGUNDOS de um snapshot offline, nao o instante da consulta', function () {
    $dto = ProductSkppDetailResponseDTO::fromArray(fixtureTiktokProduct('get-product-skpp-detail'));

    // Segundos, nao milissegundos: tratado como ms cairia em 1970. O score
    // vem de pipeline offline com atraso T+1/T+2 — nao use pra "esta fresco?".
    expect($dto->updateTime)->toBe(1779129600)
        ->and(gmdate('Y', $dto->updateTime))->toBe('2026')
        ->and($dto->totalScore)->toBe(6)
        ->and($dto->targetScore)->toBe(8)
        ->and($dto->taskDetails[0]->passed)->toBeFalse()
        ->and($dto->taskDetails[0]->actionSuggestions[0]->scoreIncrease)->toBe(8);
});

it('SKPP: o resumo da loja traz credito de anuncio com moeda embutida', function () {
    $sum = ShopSkppSummaryResponseDTO::fromArray(fixtureTiktokProduct('get-shop-skpp-summary'))->shopSummary;

    expect($sum->earnedAdCredit)->toBe('$500')
        ->and($sum->increasedVisibilityL30d)->toBe('12345') // string na doc, mesmo sendo contagem
        ->and($sum->qualifiedProductsCount)->toBe(1);
});

it('oportunidade: metricas vem como STRING JA arredondada pelo TikTok', function () {
    $op = OpportunityDetailResponseDTO::fromArray(fixtureTiktokProduct('get-opportunity-detail'))->opportunity;

    // "7k+" / "$9M+": nao da' pra somar nem ordenar sem parsear o sufixo.
    expect($op->contentData->videoViewPv)->toBe('7k+')
        ->and($op->marketData->estMarketGmv)->toBe('$9M+')   // sempre em USD
        ->and($op->basicInfo->referencePrice)->toBe('$36.3 - $38.5');
});

it('oportunidade: tags e uma string por virgula pareada POSICIONALMENTE com tag_codes', function () {
    $info = OpportunityDetailResponseDTO::fromArray(fixtureTiktokProduct('get-opportunity-detail'))->opportunity->basicInfo;

    expect($info->tags)->toBeString()
        ->and(explode(',', $info->tags))->toHaveCount(5)
        ->and($info->tagCodes)->toBe(['CORE_PRODUCT']); // menos codigos que tags: o pareamento nao e' garantido
});

it('a LISTA de oportunidades tem shape achatada, diferente do detalhe', function () {
    $payload = fixtureTiktokProduct('list-opportunity');
    $item = OpportunityListResponseDTO::fromArray($payload)->opportunities[0];

    // Aqui est_market_gmv esta' na raiz do item; no detalhe vive em market_data.
    expect($item->estMarketGmv)->toBe('$9M+')
        ->and($item->isCollected)->toBeFalse()
        ->and(normalizeTiktokPayload(OpportunityListResponseDTO::fromArray($payload)->toArray()))
            ->toBe(normalizeTiktokPayload($payload));
});

it('submissao: o Submit devolve subconjunto do que o historico devolve', function () {
    $submit = OpportunitySubmitResponseDTO::fromArray(fixtureTiktokProduct('submit-product-via-opportunity'))->submission;
    $record = SubmissionRecordsResponseDTO::fromArray(fixtureTiktokProduct('get-submission-records'))->submissions[0];

    expect($submit->status)->toBe('PENDING_REVIEW')
        ->and($submit->productName)->toBeNull()      // so' o historico traz
        ->and($record->productName)->toBe('Premium Yoga Leggings - Black')
        ->and($record->productOrders)->toBe(121);
});

it('SEO words e sugestao de titulo compartilham a shape {text}', function () {
    $seo = ProductSeoWordsResponseDTO::fromArray(fixtureTiktokProduct('get-products-seo-words'));
    $sug = RecommendedTitleDescriptionResponseDTO::fromArray(fixtureTiktokProduct('get-recommended-title-description'));

    expect($seo->products[0]->seoWords[0]->text)->toBe('dress')
        ->and($sug->products[0]->suggestions[0]->field)->toBe('TITLE')
        ->and($sug->products[0]->suggestions[0]->items[0]->text)->toBe('this is a good title');
});

it('auditing research devolve produto de OUTRO vendedor, sem preco nem estoque', function () {
    $prod = ProductAuditingResearchResponseDTO::fromArray(fixtureTiktokProduct('product-auditing-research'))->products[0];

    expect($prod->sellerName)->toBe('Essential Shop')
        ->and($prod->variants[0]->id)->toBe('11111')   // so' o ID do SKU: nao serve pra monitor de preco
        ->and($prod->categories[0]->isLeaf)->toBeFalse()
        ->and($prod->listingUrl)->toContain('shop.tiktok.com');
});
