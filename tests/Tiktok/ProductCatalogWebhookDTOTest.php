<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\CombinedListingChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\GlobalListingMethodChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\GlobalReplicationStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ImageTranslationCompletedWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ProductAuditStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ProductCategoryChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ProductCreationWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ProductInformationChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ProductPackageRecommendedWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ProductStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ShoppableContentPostingWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\SizeChartChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\SkuStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\StrikethroughPriceExpiredWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\TokopediaMirrorStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\WebhookEnvelope;

/**
 * Webhooks de PRODUTO / CATALOGO / PRECO do TikTok Shop.
 *
 * Webhook nao tem chamada: o unico contrato que existe e' o payload que cai no
 * nosso endpoint. Se o DTO comer um campo, ninguem descobre — nao ha' request
 * pra comparar. Por isso o teste de cobertura recursiva contra o exemplo
 * OFICIAL da doc e' o coracao desta bateria.
 */
function ttCatalogWebhookFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'), true);
}

dataset('webhooks de catalogo', [
    'type 5 — product status change' => ['webhook-05-product-status-change', ProductStatusChangeWebhook::class],
    'type 15 — product information change' => ['webhook-15-product-information-change', ProductInformationChangeWebhook::class],
    'type 16 — product creation' => ['webhook-16-product-creation', ProductCreationWebhook::class],
    'type 17 — shoppable content posting' => ['webhook-17-shoppable-content-posting', ShoppableContentPostingWebhook::class],
    'type 18 — product category change' => ['webhook-18-product-category-change', ProductCategoryChangeWebhook::class],
    'type 19 — size chart change' => ['webhook-19-size-chart-change', SizeChartChangeWebhook::class],
    'type 35 — tokopedia mirror status change' => ['webhook-35-tokopedia-mirror-status-change', TokopediaMirrorStatusChangeWebhook::class],
    'type 37 — product audit status change' => ['webhook-37-product-audit-status-change', ProductAuditStatusChangeWebhook::class],
    'type 38 — strikethrough price expired' => ['webhook-38-strikethrough-price-expired', StrikethroughPriceExpiredWebhook::class],
    'type 42 — combined listing change' => ['webhook-42-combined-listing-change', CombinedListingChangeWebhook::class],
    'type 46 — image translation completed' => ['webhook-46-image-translation-completed', ImageTranslationCompletedWebhook::class],
    'type 50 — sku status change' => ['webhook-50-sku-status-change', SkuStatusChangeWebhook::class],
    'type 51 — global replication status change' => ['webhook-51-global-replication-status-change', GlobalReplicationStatusChangeWebhook::class],
    'type 52 — global listing method change' => ['webhook-52-global-listing-method-change', GlobalListingMethodChangeWebhook::class],
    'type 62 — product package recommended' => ['webhook-62-product-package-recommended', ProductPackageRecommendedWebhook::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $data = ttCatalogWebhookFixture($slug)['data'];

    $perdidos = RecursiveFieldCoverage::missing($data, $dto::fromArray($data)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
})->with('webhooks de catalogo');

it('roundtrip e lossless: hidratar o proprio toArray da o mesmo array', function (string $slug, string $dto) {
    $data = ttCatalogWebhookFixture($slug)['data'];

    $primeiro = $dto::fromArray($data)->toArray();

    expect($dto::fromArray($primeiro)->toArray())->toBe($primeiro);
})->with('webhooks de catalogo');

it('envelope compartilhado le o cabecalho de todos eles', function (string $slug) {
    $envelope = WebhookEnvelope::fromArray(ttCatalogWebhookFixture($slug));

    expect($envelope->ttsNotificationId)->toBe('7327112393057371910')
        ->and($envelope->timestamp)->toBeInt()
        // shop_id, seller_open_id ou creator_open_id — nunca os tres nulos.
        ->and($envelope->shopId ?? $envelope->sellerOpenId ?? $envelope->creatorOpenId)->not->toBeNull();
})->with([
    'webhook-05-product-status-change',
    'webhook-15-product-information-change',
    'webhook-16-product-creation',
    'webhook-17-shoppable-content-posting',
    'webhook-18-product-category-change',
    'webhook-19-size-chart-change',
    'webhook-35-tokopedia-mirror-status-change',
    'webhook-37-product-audit-status-change',
    'webhook-38-strikethrough-price-expired',
    'webhook-42-combined-listing-change',
    'webhook-46-image-translation-completed',
    'webhook-51-global-replication-status-change',
    'webhook-52-global-listing-method-change',
]);

it('guarda product_id como string mesmo quando o TikTok manda numero', function () {
    // Nos topicos 5, 16 e 37 o exemplo oficial manda o snowflake SEM aspas.
    // Em float de JSON isso ja' chega arredondado; o que nao pode e' o DTO
    // piorar virando int e o id vazar como notacao cientifica pra frente.
    $dto = ProductStatusChangeWebhook::fromArray(
        ttCatalogWebhookFixture('webhook-05-product-status-change')['data']
    );

    expect($dto->productId)->toBeString()->toBe('576486316948490000');
});

it('type 37: mantem a divergencia doc x exemplo do motivo de PRE_APPROVED', function () {
    // Doc: pre_approved_reasons (lista). Exemplo oficial: pre_approved_reason
    // (string). Os dois cabem no DTO; o payload real decide qual vem.
    $dto = ProductAuditStatusChangeWebhook::fromArray(
        ttCatalogWebhookFixture('webhook-37-product-audit-status-change')['data']
    );

    expect($dto->audit->preApprovedReason)->toBe('KYC_PENDING')
        ->and($dto->audit->preApprovedReasons)->toBeNull()
        ->and($dto->integratedPlatformStatuses->platform)->toBe('TOKOPEDIA');

    // A forma documentada (lista) tambem hidrata, sem estourar.
    $comLista = ProductAuditStatusChangeWebhook::fromArray([
        'product_id' => '1', 'audit' => ['status' => 'PRE_APPROVED', 'pre_approved_reasons' => ['KYC_PENDING']],
    ]);

    expect($comLista->audit->preApprovedReasons)->toBe(['KYC_PENDING']);
});

it('type 46: aceita target_lang (exemplo) e target_language (doc)', function () {
    $task = ImageTranslationCompletedWebhook::fromArray(
        ttCatalogWebhookFixture('webhook-46-image-translation-completed')['data']
    )->translationTask;

    expect($task->targetLang)->toBe('it-IT')
        ->and($task->targetLanguage)->toBeNull()
        // uri e' o handle persistivel; url do CDN expira.
        ->and($task->originalImage->uri)->toStartWith('tos-useast2a-');
});

it('type 42: products e a lista COMPLETA pos-mudanca, nao o delta', function () {
    $dto = CombinedListingChangeWebhook::fromArray(
        ttCatalogWebhookFixture('webhook-42-combined-listing-change')['data']
    );

    // O exemplo oficial remove 987654333 — um id que NAO esta' em products,
    // justamente porque products e' o estado final e removed_* e' historico.
    expect($dto->products)->toHaveCount(2)
        ->and($dto->products[0]->id)->toBe('987654321')
        ->and($dto->removedProductIds)->toBe(['987654333'])
        ->and($dto->addedProductIds)->toBe(['987654321']);
});

it('type 50: um evento carrega VARIAS skus e so a desativada traz a origem', function () {
    $dto = SkuStatusChangeWebhook::fromArray(
        ttCatalogWebhookFixture('webhook-50-sku-status-change')['data']
    );

    expect($dto->skus)->toHaveCount(2)
        ->and($dto->skus[0]->deactivationSource)->toBe('SELLER')
        ->and($dto->skus[1]->status)->toBe('CREATED')
        ->and($dto->skus[1]->deactivationSource)->toBeNull();
});

it('type 51: fail_reasons sobrevive nas DUAS formas (objeto do exemplo, lista da doc)', function () {
    $doExemplo = GlobalReplicationStatusChangeWebhook::fromArray(
        ttCatalogWebhookFixture('webhook-51-global-replication-status-change')['data']
    );

    expect($doExemplo->replicatedProduct->failReasons)->toBe(['message' => '']);

    $daDoc = GlobalReplicationStatusChangeWebhook::fromArray([
        'source_product_id' => '1',
        'replicated_product' => ['result' => 'FAILED', 'fail_reasons' => [['message' => 'invalid category']]],
    ]);

    expect($daDoc->replicatedProduct->failReasons)->toBe([['message' => 'invalid category']]);
});

it('type 52: local_product_id e LISTA apesar do nome no singular', function () {
    $dto = GlobalListingMethodChangeWebhook::fromArray(
        ttCatalogWebhookFixture('webhook-52-global-listing-method-change')['data']
    );

    expect($dto->localProductId)->toBe(['1731711955927208538']);
});

it('type 62: recomendacao de PACOTE (peso/cubagem), com numeros como string', function () {
    $dto = ProductPackageRecommendedWebhook::fromArray(
        ttCatalogWebhookFixture('webhook-62-product-package-recommended')['data']
    );

    // O 62 nao e' "anomalia de produto" generica: e' peso/dimensao divergente
    // do cadastrado — o que muda frete. Faixas vem como string.
    expect($dto->abnormalityTypes)->toBe(['Weight', 'Dimension'])
        ->and($dto->weightRecommendation->unit)->toBe('GRAM')
        ->and($dto->weightRecommendation->weight->minValue)->toBeString()->toBe('12000')
        ->and($dto->dimensionRecommendation->unit)->toBe('CENTIMETER')
        ->and($dto->dimensionRecommendation->width->maxValue)->toBe('666');
});

it('type 19 e 52 nao identificam a loja por shop_id, e sim por seller_open_id', function () {
    // Quem indexar webhook por shop_id perde estes dois em silencio.
    foreach (['webhook-19-size-chart-change', 'webhook-52-global-listing-method-change'] as $slug) {
        $envelope = WebhookEnvelope::fromArray(ttCatalogWebhookFixture($slug));

        expect($envelope->shopId)->toBeNull()
            ->and($envelope->sellerOpenId)->not->toBeNull();
    }
});

it('type 35 carrega o shop id DENTRO do data, como tts_shop_id', function () {
    $payload = ttCatalogWebhookFixture('webhook-35-tokopedia-mirror-status-change');

    expect(WebhookEnvelope::fromArray($payload)->shopId)->toBeNull()
        ->and(TokopediaMirrorStatusChangeWebhook::fromArray($payload['data'])->ttsShopId)
        ->toBe('7494049642642441621');
});

it('type 17 e evento de CRIADOR: envelope traz creator_open_id, nao shop_id', function () {
    $payload = ttCatalogWebhookFixture('webhook-17-shoppable-content-posting');
    $envelope = WebhookEnvelope::fromArray($payload);

    expect($envelope->shopId)->toBeNull()
        ->and($envelope->creatorOpenId)->not->toBeNull();

    // `event.type` aqui e' a acao do criador — NAO o type code do webhook.
    expect(ShoppableContentPostingWebhook::fromArray($payload['data'])->event->type)->toBe('ADD');
});

it('type 15 diz O QUE mudou, nunca o valor novo', function () {
    $dto = ProductInformationChangeWebhook::fromArray(
        ttCatalogWebhookFixture('webhook-15-product-information-change')['data']
    );

    expect($dto->changedFields)->toContain('title', 'main_images')
        // change_source permite ignorar o eco da nossa propria escrita.
        ->and($dto->changeSource)->toBe('SELLER_CENTER');
});

it('type 16 traz product_types e o update_time e a hora da CRIACAO', function () {
    $dto = ProductCreationWebhook::fromArray(
        ttCatalogWebhookFixture('webhook-16-product-creation')['data']
    );

    expect($dto->productTypes)->toBe(['GPR_PRODUCT'])
        ->and($dto->updateTime)->toBe(1644412885);
});

it('type 18 traz a categoria ANTES e DEPOIS — mudanca pode partir do TikTok', function () {
    $dto = ProductCategoryChangeWebhook::fromArray(
        ttCatalogWebhookFixture('webhook-18-product-category-change')['data']
    );

    expect($dto->previousCategoryId)->toBe('600001')
        ->and($dto->currentCategoryId)->toBe('600002');
});

it('type 38 nao tem hora propria — a referencia e o timestamp do envelope', function () {
    $payload = ttCatalogWebhookFixture('webhook-38-strikethrough-price-expired');
    $dto = StrikethroughPriceExpiredWebhook::fromArray($payload['data']);

    // Payload minimo: produto + SKU. Quem precisa datar a expiracao tem que
    // ler o envelope.
    expect(array_keys($dto->toArray()))->toBe(['product_id', 'sku_id'])
        ->and(WebhookEnvelope::fromArray($payload)->timestamp)->toBe(1644412885);
});

it('type 50 ainda manda "TBD" no lugar do type code — roteador numerico precisa aguentar', function () {
    // A doc do topico 50 diz que o numero esta' "pending final assignment" e o
    // exemplo oficial traz "type": "TBD". No envelope isso vira 0, nao explode.
    $envelope = WebhookEnvelope::fromArray(ttCatalogWebhookFixture('webhook-50-sku-status-change'));

    expect($envelope->type)->toBe(0)
        ->and($envelope->shopId)->toBe('7494049642642441621');
});

it('valor enumerado desconhecido nao explode a hidratacao', function () {
    // Enum tipado quebraria producao no dia em que o TikTok criar um status
    // novo. Por isso todo enumerado e' ?string.
    $dto = ProductStatusChangeWebhook::fromArray([
        'product_id' => '1', 'status' => 'STATUS_QUE_AINDA_NAO_EXISTE', 'update_time' => 1,
    ]);

    expect($dto->status)->toBe('STATUS_QUE_AINDA_NAO_EXISTE');
});
