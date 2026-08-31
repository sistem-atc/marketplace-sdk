<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\AddShowcaseProductsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\AffiliateOrderSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\AffiliateTraceOrderSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\CreatorProfileResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\CreatorSampleApplicationDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\CreatorSampleLabelResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\MusicSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\OpenCollaborationProductListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\OpenCollaborationProductSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\PhotoFileUploadResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\SampleApplicationFulfillmentSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\SampleApplicationSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\SharingLinkBatchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShopProductSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShoppablePhotoPostResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShoppableVideoPostResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShoppableVideoPrecheckResultResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShoppableVideoStatusResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShowcaseOperationResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShowcaseProductSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\TargetCollaborationSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\TokoProductMapperResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\VideoFileUploadResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\VideoPrecheckTaskResponseDTO;

/** Le o `data` do exemplo OFICIAL da doc do endpoint. */
function acFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__."/../Fixtures/Tiktok/{$slug}.json"), true);
}

/**
 * Diff RECURSIVO de chaves entre o payload da API e o `toArray()` do DTO.
 *
 * Comparar so' as chaves de topo nao pega nada: o payload deste dominio e' raso
 * na raiz e fundo no meio (orders[].skus[].trace.id). Era exatamente ai' que
 * `is_refundable_sample` sumia sem ninguem ver.
 *
 * @return list<string> caminhos completos dos campos descartados
 */
function acLostFields(array $payload, array $result, string $path = ''): array
{
    $lost = [];

    foreach ($payload as $key => $value) {
        $here = $path === '' ? (string) $key : $path.'.'.$key;

        if (! array_key_exists($key, $result)) {
            $lost[] = $here;

            continue;
        }

        if (is_array($value) && is_array($result[$key])) {
            $lost = array_merge($lost, acLostFields($value, $result[$key], $here));
        }
    }

    return $lost;
}

dataset('affiliate_creator_endpoints', [
    'add showcase products' => ['ac-add-showcase-products', AddShowcaseProductsResponseDTO::class],
    'generate general link' => ['ac-generate-general-link', SharingLinkBatchResponseDTO::class],
    'generate publisher link' => ['ac-generate-publisher-link', SharingLinkBatchResponseDTO::class],
    'search affiliate trace orders' => ['ac-search-affiliate-trace-orders', AffiliateTraceOrderSearchResponseDTO::class],
    'search open collaboration products' => ['ac-search-open-collaboration-products', OpenCollaborationProductSearchResponseDTO::class],
    'search sample application fulfillments' => ['ac-search-sample-application-fulfillments', SampleApplicationFulfillmentSearchResponseDTO::class],
    'get applicable sample label' => ['ac-get-applicable-sample-label', CreatorSampleLabelResponseDTO::class],
    'get creator profile' => ['ac-get-creator-profile', CreatorProfileResponseDTO::class],
    'get sample application detail' => ['ac-get-sample-application-detail', CreatorSampleApplicationDetailResponseDTO::class],
    'get open collaboration products by ids' => ['ac-get-open-collaboration-products-by-ids', OpenCollaborationProductListResponseDTO::class],
    'get shop products' => ['ac-get-shop-products', ShopProductSearchResponseDTO::class],
    'get video precheck result' => ['ac-get-video-precheck-result', ShoppableVideoPrecheckResultResponseDTO::class],
    'get video status' => ['ac-get-video-status', ShoppableVideoStatusResponseDTO::class],
    'get showcase products' => ['ac-get-showcase-products', ShowcaseProductSearchResponseDTO::class],
    'get toko product mappers' => ['ac-get-toko-product-mappers', TokoProductMapperResponseDTO::class],
    'post shoppable photos' => ['ac-post-shoppable-photos', ShoppablePhotoPostResponseDTO::class],
    'post shoppable video' => ['ac-post-shoppable-video', ShoppableVideoPostResponseDTO::class],
    'precheck video content' => ['ac-precheck-video-content', VideoPrecheckTaskResponseDTO::class],
    'remove showcase products' => ['ac-remove-showcase-products', ShowcaseOperationResponseDTO::class],
    'search affiliate orders' => ['ac-search-affiliate-orders', AffiliateOrderSearchResponseDTO::class],
    'search sample applications' => ['ac-search-sample-applications', SampleApplicationSearchResponseDTO::class],
    'search target collaborations' => ['ac-search-target-collaborations', TargetCollaborationSearchResponseDTO::class],
    'search music' => ['ac-search-music', MusicSearchResponseDTO::class],
    'map toko product' => ['ac-map-toko-product', TokoProductMapperResponseDTO::class],
    'top showcase products' => ['ac-top-showcase-products', ShowcaseOperationResponseDTO::class],
    'upload photo file' => ['ac-upload-photo-file', PhotoFileUploadResponseDTO::class],
    'upload video file' => ['ac-upload-video-file', VideoFileUploadResponseDTO::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $payload = acFixture($slug);

    $perdidos = acLostFields($payload, $dto::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], "campos descartados em {$slug}: ".implode(', ', $perdidos));
})->with('affiliate_creator_endpoints');

describe('affiliate_creator — comportamento que surpreende quem consome', function () {
    it('amostra aprovada VIRA PEDIDO: main_order_id e o elo com a nota', function () {
        $app = CreatorSampleApplicationDetailResponseDTO::fromArray(
            acFixture('ac-get-sample-application-detail'),
        )->sampleApplication;

        // O pedido nasceu de uma amostra GRATIS — nao e' venda. Emitir nota de
        // venda pra esse order_id e o erro que custou 3.173 titulos indevidos.
        expect($app->type)->toBe('FREE_SAMPLE')
            ->and($app->mainOrderId)->toBe('57871819384716917')
            ->and($app->mainOrderId)->toBeString();
    });

    it('dinheiro continua STRING JA FORMATADA na moeda local — nao da pra somar direto', function () {
        $sku = AffiliateOrderSearchResponseDTO::fromArray(
            acFixture('ac-search-affiliate-orders'),
        )->orders[0]->skus[0];

        // "Rp9.900" nao e' 9.9: o ponto e separador de MILHAR no formato indonesio.
        expect($sku->price->amount)->toBe('Rp9.900')
            ->and($sku->price->amount)->toBeString()
            ->and($sku->actualCommission->amount)->toBe('Rp1.900');
    });

    it('commission_rate e INTEIRO em centesimos de por cento, nao fracao nem percentual', function () {
        $sku = AffiliateOrderSearchResponseDTO::fromArray(
            acFixture('ac-search-affiliate-orders'),
        )->orders[0]->skus[0];

        // 1000 = 10,00% (divida por 10000). Ler como percentual daria 1000%.
        expect($sku->commissionRate)->toBe(1000)
            ->and($sku->standardCommissionRate)->toBe(5000)
            ->and($sku->commissionRate / 10000)->toBe(0.1);
    });

    it('o id do pedido rastreado vem como NUMERO na API e vira string sem perder digito', function () {
        $payload = acFixture('ac-search-affiliate-trace-orders');

        // O exemplo oficial ja' chega arredondado pelo JSON (…123100): snowflake
        // como int nao sobrevive. O DTO forca string pra comparacao ser textual.
        expect($payload['orders'][0]['id'])->toBeInt();

        $order = AffiliateTraceOrderSearchResponseDTO::fromArray($payload)->orders[0];

        expect($order->id)->toBeString()
            ->and($order->id)->toBe((string) $payload['orders'][0]['id']);
    });

    it('a imagem da vitrine mantem o typo `heigth` do TikTok — corrigir descartaria o campo', function () {
        $product = ShowcaseProductSearchResponseDTO::fromArray(
            acFixture('ac-get-showcase-products'),
        )->products[0];

        expect($product->mainImages[0]->heigth)->toBe(100)
            ->and($product->mainImages[0]->toArray())->toHaveKey('heigth')
            ->and($product->mainImages[0]->toArray())->not->toHaveKey('height');
    });

    it('a busca de musica pagina por search_id, nao so por token — e o fim e has_more=false', function () {
        $music = MusicSearchResponseDTO::fromArray(acFixture('ac-search-music'));

        // has_more=false COM next_page_token preenchido: quem parar no token
        // vazio pagina pra sempre.
        expect($music->hasMore)->toBeFalse()
            ->and($music->nextPageToken)->toBe('40')
            ->and($music->searchId)->toBe('2026021821534288157A7F308E8C81359B')
            // duracao e STRING de segundos, nao int
            ->and($music->music[0]->duration)->toBe('235');
    });

    it('remover/topar vitrine repete o envelope DENTRO de data — e ele que vale', function () {
        $resp = ShowcaseOperationResponseDTO::fromArray(acFixture('ac-remove-showcase-products'));

        expect($resp->code)->toBe(0)
            ->and($resp->requestId)->toBe('202203070749000101890810281E8C70B7');
    });

    it('o de/para Tokopedia devolve erro DENTRO de data com HTTP 200 e code externo 0', function () {
        $resp = TokoProductMapperResponseDTO::fromArray(acFixture('ac-map-toko-product'));

        // Quem checa so' o envelope externo trata a falha como sucesso.
        expect($resp->error->code)->toBe(1)
            ->and($resp->error->message)->toBe('empty_product_id')
            // a lista se chama `product`, no SINGULAR
            ->and($resp->product[0]->tokoPid)->toBe(2177906740);
    });

    it('a etiqueta de amostra responde ANTES de existir pedido', function () {
        $label = CreatorSampleLabelResponseDTO::fromArray(
            acFixture('ac-get-applicable-sample-label'),
        )->label;

        // status ONGOING = ja pediu amostra desse produto e ainda deve conteudo.
        expect($label->canApply)->toBeTrue()
            ->and($label->status)->toBe('ONGOING')
            ->and($label->applicationId)->toBe('86427198341982134')
            ->and($label->sampleProduct->sampleSkuList[0]->unavailableReason)->toBe('IS_PREORDER');
    });

    it('o fulfillment de amostra ja vem achatado com shop_id e product_id', function () {
        $f = SampleApplicationFulfillmentSearchResponseDTO::fromArray(
            acFixture('ac-search-sample-application-fulfillments'),
        )->fulfillments[0];

        expect($f->sampleApplicationType)->toBe('FREE_SAMPLE')
            ->and($f->productId)->toBe('123456')
            ->and($f->shopId)->toBe('123456')
            // prazo em epoch de SEGUNDOS, suspensao em segundos
            ->and($f->expirationTime)->toBe(1728542813)
            ->and($f->totalSuspendDuration)->toBe(100020);
    });

    it('sku_sale_property_value_names e declarado lista mas o exemplo manda string', function () {
        $p = SampleApplicationSearchResponseDTO::fromArray(
            acFixture('ac-search-sample-applications'),
        )->sampleApplications[0]->sampleProduct;

        // Tipar array descartaria o valor. Fica `mixed` pra aceitar as duas formas.
        expect($p->skuSalePropertyValueNames)->toBe('red, large size');
    });

    it('geracao de link em lote falha PARCIAL: sucessos e falhas na mesma resposta 200', function () {
        $batch = SharingLinkBatchResponseDTO::fromArray(acFixture('ac-generate-general-link'));

        expect($batch->sharingLinks)->toHaveCount(1)
            ->and($batch->failedMaterials[0]->failReason)->toBe('Product was sold out')
            ->and($batch->sharingLinks[0]->oneLink)->toContain('onelink.me');
    });

    it('o precheck do video tem DOIS veredictos independentes', function () {
        $task = ShoppableVideoPrecheckResultResponseDTO::fromArray(
            acFixture('ac-get-video-precheck-result'),
        )->precheckTask;

        // violacao bloqueia; qualidade e' so' sugestao. Chaves de issue diferentes:
        // `risk` na violacao, `code` na qualidade.
        expect($task->violationCheckResult->issues[0]->risk)->toBe('Pirated Content')
            ->and($task->goodQualityCheckResult->issues[0]->code)->toBe('LOW_CONTENT_PROMOTIO');
    });

    it('publicar video devolve a quota como TEXTO, nao numero', function () {
        $resp = ShoppableVideoPostResponseDTO::fromArray(acFixture('ac-post-shoppable-video'));

        expect($resp->quota)->toBe('3/day')
            ->and($resp->video->id)->toBe('7548431509997292816');
    });

    it('o pedido de afiliado TEM quantidade (o pedido do vendedor nao tem)', function () {
        $sku = AffiliateOrderSearchResponseDTO::fromArray(
            acFixture('ac-search-affiliate-orders'),
        )->orders[0]->skus[0];

        // No /order/{v}/orders cada line_item e 1 unidade; aqui vem agregado.
        expect($sku->quantity)->toBe(2)
            ->and($sku->returnedQuantity)->toBe(1)
            ->and($sku->refundedQuantity)->toBe(0);
    });

    it('o trace do link chega em chave DIFERENTE nos dois endpoints de pedido', function () {
        $trace = AffiliateTraceOrderSearchResponseDTO::fromArray(
            acFixture('ac-search-affiliate-trace-orders'),
        )->orders[0]->skus[0]->trace;

        $traceInfo = AffiliateOrderSearchResponseDTO::fromArray(
            acFixture('ac-search-affiliate-orders'),
        )->orders[0]->skus[0]->traceInfo;

        // `trace` no /orders/trace/search, `trace_info` no /orders/search.
        expect($trace->type)->toBe('SPECIFIC')
            ->and($traceInfo->type)->toBe('SPECIFIC')
            ->and($trace->id)->toBe($traceInfo->id);
    });

    it('o perfil do creator lista permissoes como strings cruas', function () {
        $profile = CreatorProfileResponseDTO::fromArray(acFixture('ac-get-creator-profile'));

        expect($profile->permissions)->toContain('SELF_SALE_PERMISSION')
            ->and($profile->avatar->url)->toStartWith('https://')
            ->and($profile->creatorUserOpenId)->toBeString();
    });

    it('o produto de colaboracao aberta esconde units_sold quando falta permissao', function () {
        // O 202509 (por ids) traz shop_ads_commission; o 202405 (busca) nao.
        $porIds = OpenCollaborationProductListResponseDTO::fromArray(
            acFixture('ac-get-open-collaboration-products-by-ids'),
        )->products[0];

        $busca = OpenCollaborationProductSearchResponseDTO::fromArray(
            acFixture('ac-search-open-collaboration-products'),
        )->products[0];

        expect($porIds->shopAdsCommission->rate)->toBe(100)
            ->and($busca->shopAdsCommission)->toBeNull()
            // ausencia de units_sold != zero unidades vendidas
            ->and($busca->unitsSold)->toBe(12123);
    });

    it('o status de publicacao do video so traz post_time quando SUCCESS', function () {
        $video = ShoppableVideoStatusResponseDTO::fromArray(acFixture('ac-get-video-status'))->video;

        // O exemplo oficial e' FAIL mas manda post_time assim mesmo — nao confie
        // na presenca do campo, confie no post_status.
        expect($video->postStatus)->toBe('FAIL')
            ->and($video->postTime)->toBe(1685548800);
    });

    it('impostos retidos so aparecem em regioes especificas', function () {
        $sku = AffiliateOrderSearchResponseDTO::fromArray(
            acFixture('ac-search-affiliate-orders'),
        )->orders[0]->skus[0];

        // isr/iva = Mexico, pit = VN/TH/PH/ID. No BR os tres vem nulos.
        expect($sku->isr->amount)->toBe('100')
            ->and($sku->iva->amount)->toBe('100')
            ->and($sku->pit->amount)->toBe('100')
            ->and($sku->sharedWithPartner->amount)->toBe('100');
    });

    it('a adicao na vitrine responde 200 com a lista do que FALHOU', function () {
        $resp = AddShowcaseProductsResponseDTO::fromArray(acFixture('ac-add-showcase-products'));

        expect($resp->errors[0]->code)->toBe(16001001)
            ->and($resp->errors[0]->detail->productId)->toBe('12390753231');
    });

    it('a colaboracao direcionada traz a comissao por PRODUTO, nao por convite', function () {
        $collab = TargetCollaborationSearchResponseDTO::fromArray(
            acFixture('ac-search-target-collaborations'),
        )->targetCollaborations[0];

        expect($collab->status)->toBe('LIVE')
            ->and($collab->products[0]->commission->rate)->toBe(1000)
            ->and($collab->products[0]->commission->amount)->toBe('121.23');
    });

    it('o catalogo da loja devolve added_status com \n colado', function () {
        $p = ShopProductSearchResponseDTO::fromArray(acFixture('ac-get-shop-products'))->products[0];

        // Comparar sem trim() da' falso-negativo em ADDABLE.
        expect($p->addedStatus)->toBe("ADDABLE\n")
            ->and(trim($p->addedStatus))->toBe('ADDABLE')
            ->and($p->price->amount)->toBe('56.00');
    });

    it('os uploads devolvem identificadores DIFERENTES: file id no video, uri na foto', function () {
        $video = VideoFileUploadResponseDTO::fromArray(acFixture('ac-upload-video-file'));
        $photo = PhotoFileUploadResponseDTO::fromArray(acFixture('ac-upload-photo-file'));

        expect($video->videoFile->id)->toBe('123123123123')
            ->and($video->videoFile->md5)->toBe('D41D8CD98F00B204E9800998ECF8427E')
            ->and($photo->photoFile->photoUri)->toBe('skldjfskdlfjlskdfs000023423s');
    });

    it('o precheck e assincrono: devolve so o task_id', function () {
        expect(VideoPrecheckTaskResponseDTO::fromArray(acFixture('ac-precheck-video-content'))->precheck->taskId)
            ->toBe('1123123123');
    });

    it('publicar fotos devolve photo_post_id (chave diferente do video)', function () {
        $resp = ShoppablePhotoPostResponseDTO::fromArray(acFixture('ac-post-shoppable-photos'));

        expect($resp->photo->photoPostId)->toBe('34234234234324342')
            ->and($resp->quota)->toBe('3/day');
    });
});
