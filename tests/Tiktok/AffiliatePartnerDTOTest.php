<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CampaignCreatorPerformanceResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CampaignDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CampaignListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CampaignProductListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CampaignProductPerformanceResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CapAffiliateOrderSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CreatedCampaignResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CreatorContentStatisticsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CreatorSampleStatusResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\MultiProductPromotionLinkResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\ProductPromotionLinkResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\TapAffiliateOrderSearchResponseDTO;

function apFixture(string $slug): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'),
        true,
    );
}

dataset('ap_fixtures', [
    'create campaign' => ['ap-create-campaign', CreatedCampaignResponseDTO::class],
    'campaign detail' => ['ap-campaign-detail', CampaignDetailResponseDTO::class],
    'campaign list' => ['ap-campaign-list', CampaignListResponseDTO::class],
    'campaign product list' => ['ap-campaign-product-list', CampaignProductListResponseDTO::class],
    'generate product link' => ['ap-generate-product-link', ProductPromotionLinkResponseDTO::class],
    'generate product links batch' => ['ap-generate-product-links-batch', MultiProductPromotionLinkResponseDTO::class],
    'creator fulfillment status info' => ['ap-creator-fulfillment-status-info', CampaignCreatorPerformanceResponseDTO::class],
    'creator fulfillment status list' => ['ap-creator-fulfillment-status-list', CampaignProductPerformanceResponseDTO::class],
    'creator content statistics' => ['ap-creator-content-statistics', CreatorContentStatisticsResponseDTO::class],
    'creator sample status' => ['ap-creator-sample-status', CreatorSampleStatusResponseDTO::class],
    'search CAP orders' => ['ap-search-cap-orders', CapAffiliateOrderSearchResponseDTO::class],
    'search TAP orders' => ['ap-search-tap-orders', TapAffiliateOrderSearchResponseDTO::class],
]);

/**
 * Os pedidos de afiliado tem ~50 campos cada, quase todos objetos {amount,
 * currency} aninhados. E' exatamente a shape em que campo some sem ninguem ver:
 * a comparacao aqui desce ate' o `currency` de cada valor.
 */
it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $payload = apFixture($slug);
    $perdidos = RecursiveFieldCoverage::missing($payload, $dto::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
})->with('ap_fixtures');

it('faz roundtrip lossless do payload inteiro', function (string $slug, string $dto) {
    $payload = apFixture($slug);

    expect($dto::fromArray($payload)->toArray())->toEqual($payload);
})->with('ap_fixtures');

// ─────────────────────────────────────────────────────────────────────────
// Comportamento real
// ─────────────────────────────────────────────────────────────────────────

it('comissao e CENTESIMO de ponto percentual, e a total se reparte em criador + parceiro', function () {
    $produto = CampaignProductListResponseDTO::fromArray(apFixture('ap-campaign-product-list'))->products[0];

    // 6600 = 66,00%, nao 6600% nem 66 pontos. E 1000 + 5600 = 6600: a total
    // que o vendedor banca ja' EMBUTE as duas fatias. Somar as tres da' o dobro.
    expect($produto->totalCommissionRate)->toBe(6600)
        ->and($produto->creatorCommissionRate + $produto->partnerCommissionRate)
        ->toBe($produto->totalCommissionRate);

    // Esta e' de outro bolso — colaboracao aberta, fora da soma acima.
    expect($produto->openCollaborationCommissionRate)->toBe(1000);
});

it('a apuracao fecha por actual_*, nunca por estimated_*', function () {
    $pedido = CapAffiliateOrderSearchResponseDTO::fromArray(apFixture('ap-search-cap-orders'))->skuOrders[0];

    // Base estimada 99 (no momento do pedido) x base real 100 (depois de
    // devolucao/reembolso). Sao numeros DIFERENTES no mesmo registro; repasse
    // calculado pelo estimated paga errado.
    expect($pedido->estimatedCommissionBase->amount)->toBe('99')
        ->and($pedido->actualCommissionBase->amount)->toBe('100');

    // Dinheiro segue STRING — "99" sem casa decimal inclusive.
    expect($pedido->price->amount)->toBeString()->toBe('99');
});

it('fully_return e a STRING "Yes", nao um booleano', function () {
    $pedido = CapAffiliateOrderSearchResponseDTO::fromArray(apFixture('ap-search-cap-orders'))->skuOrders[0];

    // `if ($pedido->fullyReturn)` seria true pra "Yes" E pra "No" — os dois sao
    // string nao-vazia. Tem que comparar o valor.
    expect($pedido->fullyReturn)->toBeString()->toBe('Yes')
        ->and($pedido->fullyReturn)->not->toBeBool();
});

it('CAP e TAP nao sao o mesmo pedido com outro nome', function () {
    $cap = CapAffiliateOrderSearchResponseDTO::fromArray(apFixture('ap-search-cap-orders'))->skuOrders[0];
    $tap = TapAffiliateOrderSearchResponseDTO::fromArray(apFixture('ap-search-tap-orders'))->skuOrders[0];

    // O rateio muda de eixo: no CAP a contraparte e' a AGENCIA (agency_*), no
    // TAP e' o PARCEIRO (partner_*). Reaproveitar o parser de um no outro
    // simplesmente perde os campos de comissao.
    expect($cap->toArray())->toHaveKey('estimated_total_agency_earnings')
        ->and($tap->toArray())->not->toHaveKey('estimated_total_agency_earnings')
        ->and($tap->toArray())->toHaveKey('estimated_partner_standard_commission');

    // E so' o TAP amarra o pedido a uma campanha.
    expect($tap->campaignId)->toBe('7324371012170024705')
        ->and($cap->openCollaborationId)->toBe('7324371012170024705');
});

it('o lote de links pode falhar em silencio', function () {
    $dto = MultiProductPromotionLinkResponseDTO::fromArray(apFixture('ap-generate-product-links-batch'));

    // 1 gerado + 1 falhado, com `code: 0` no envelope. Quem contar so'
    // productPromotionLinks acha que gerou tudo.
    expect($dto->productPromotionLinks)->toHaveCount(1)
        ->and($dto->failedProductIds)->toHaveCount(1);
});

it('performance troca o tipo dos MESMOS numeros entre endpoints', function () {
    $daLista = CampaignProductListResponseDTO::fromArray(apFixture('ap-campaign-product-list'))->products[0];
    $daPerformance = CampaignProductPerformanceResponseDTO::fromArray(apFixture('ap-creator-fulfillment-status-list'))
        ->campaignProductStatistics[0]->campaignProductDetail;

    // Mesma unidade (centesimo de %), nome e TIPO diferentes: `*_rate` int no
    // Product List, `*_percent` string na Performance. Por isso sao DTOs
    // distintos — um cast unico quebraria o roundtrip de um dos dois.
    expect($daLista->creatorCommissionRate)->toBeInt()
        ->and($daPerformance->creatorCommissionPercent)->toBeString()->toBe('1000');
});

it('tempos do affiliate vem em MILISSEGUNDOS e como string', function () {
    $criador = CampaignCreatorPerformanceResponseDTO::fromArray(apFixture('ap-creator-fulfillment-status-info'))
        ->promotionCreators[0];

    // 1731019880391 = 13 digitos = ms. O resto da API TikTok usa int em
    // segundos; tratar isso como segundos joga a colaboracao pro ano 56000.
    expect($criador->effectiveStartTime)->toBeString()->toBe('1731019880391')
        ->and(strlen($criador->effectiveStartTime))->toBe(13);
});

it('estatistica de conteudo muda de forma conforme VIDEO ou LIVE_ROOM', function () {
    $stat = CreatorContentStatisticsResponseDTO::fromArray(apFixture('ap-creator-content-statistics'))
        ->creatorContentStatistics[0];

    // Datas aqui sao STRING YYYY-MM-DD, nao epoch — unico canto da familia.
    expect($stat->contentType)->toBe('VIDEO')
        ->and($stat->publishedDate)->toBe('2024-11-13')
        ->and($stat->viewCount)->toBeString();
});

it('a lista de campanhas nao serve pra ler comissao', function () {
    $daLista = CampaignListResponseDTO::fromArray(apFixture('ap-campaign-list'))->campaigns[0];
    $doDetalhe = CampaignDetailResponseDTO::fromArray(apFixture('ap-campaign-detail'));

    // A listagem e' magra de proposito: comissao, contato e lojas alvo so'
    // existem no detalhe. Iterar a lista pra montar relatorio de comissao
    // devolve null em todas as linhas.
    expect($daLista->toArray())->not->toHaveKey('commission_rate')
        ->and($doDetalhe->commissionRate)->toBe(1000)
        ->and($doDetalhe->contactInfo->email)->toBe('123456@gmail.com')
        ->and($doDetalhe->targetShops[0]->code)->toBe('THCBELNL4Y');
});

it('estoque do SKU vem string mesmo o do produto sendo int', function () {
    $produto = CampaignProductListResponseDTO::fromArray(apFixture('ap-campaign-product-list'))->products[0];

    expect($produto->inventory)->toBeInt()->toBe(89)
        ->and($produto->skuInformationList[0]->inventory->availableQuantity)->toBeString()->toBe('80');
});

it('rastreio da amostra devolve CHAVE de enum, nao texto pro usuario', function () {
    $status = CreatorSampleStatusResponseDTO::fromArray(apFixture('ap-creator-sample-status'))->sampleStatus;

    // Jogar isso na tela mostra "THE_PACKAGE_HAS_BEEN_DELIVERED" pro operador.
    expect($status->trackingResults[0]->trackingEventDescription)->toBe('THE_PACKAGE_HAS_BEEN_DELIVERED')
        ->and($status->deliveryOption)->toBe('PREMIUM_SHIPPING');
});
