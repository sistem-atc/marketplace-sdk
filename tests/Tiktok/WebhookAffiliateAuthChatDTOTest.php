<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ActivityChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ActivityStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\AppealCompletedWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\CreatorDeauthorizationWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\NewConversationWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\NewMessageListenerWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\NewMessageWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\OpportunityMatchingStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\SampleApplicationStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ShoppableVideoPrecheckTasksResultWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\UpcomingAuthorizationExpirationWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\VideoPrecheckResultWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\WebhookEnvelope;
use SistemAtc\Marketplaces\Tiktok\Enums\WebhookEventType;

/**
 * Webhooks de AFILIADO, AMOSTRA, CHAT, AUTORIZACAO e CAMPANHA (topicos 6, 7, 13,
 * 14, 20, 25, 33, 39, 55, 56, 59, 63, 66).
 *
 * Sao payloads que NOS RECEBEMOS: nao ha' chamada pra testar, o contrato E' o
 * DTO. O risco aqui e' o mesmo que originou a lib — campo que a API manda e o
 * DTO joga fora sem ninguem perceber.
 */

/** @return array<string, mixed> o envelope INTEIRO do exemplo oficial */
function ttWhAffFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/webhook-'.$slug.'.json'), true);
}

/** Ordena chaves recursivamente pra comparacao ESTRITA sem depender da ordem do JSON. */
function ttWhAffSort(array $a): array
{
    ksort($a);

    foreach ($a as $k => $v) {
        if (is_array($v)) {
            $a[$k] = ttWhAffSort($v);
        }
    }

    return $a;
}

/**
 * Contrato do lote: nenhum campo perdido E roundtrip identico em VALOR e TIPO.
 *
 * @param  class-string  $dto
 */
function ttWhAffContrato(string $slug, string $dto, int $type): void
{
    $envelope = ttWhAffFixture($slug);

    expect($envelope['type'])->toBe($type);

    // envelope: o cabecalho comum nao pode perder shop_id/creator_open_id/etc.
    $envPerdidos = RecursiveFieldCoverage::missing($envelope, WebhookEnvelope::fromArray($envelope)->toArray());
    expect($envPerdidos)->toBe([], 'envelope descartou: '.implode(', ', $envPerdidos));

    $data = $envelope['data'];
    $roundtrip = $dto::fromArray($data)->toArray();

    $perdidos = RecursiveFieldCoverage::missing($data, $roundtrip);
    expect($perdidos)->toBe([], $dto.' descartou: '.implode(', ', $perdidos));

    // lossless de verdade: valor e tipo iguais, nao so' a chave presente.
    expect(ttWhAffSort($roundtrip))->toBe(ttWhAffSort($data));
}

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto, int $type) {
    ttWhAffContrato($slug, $dto, $type);
})->with([
    ['06-seller-deauthorization', \SistemAtc\Marketplaces\Tiktok\DTO\Webhook\SellerDeauthorizationWebhook::class, 6],
    ['07-upcoming-authorization-expiration', UpcomingAuthorizationExpirationWebhook::class, 7],
    ['13-new-conversation', NewConversationWebhook::class, 13],
    ['14-new-message', NewMessageWebhook::class, 14],
    ['20-creator-deauthorization', CreatorDeauthorizationWebhook::class, 20],
    ['25-opportunity-matching-status-change', OpportunityMatchingStatusChangeWebhook::class, 25],
    ['33-new-message-listener', NewMessageListenerWebhook::class, 33],
    ['39-activity-status-change', ActivityStatusChangeWebhook::class, 39],
    ['55-video-precheck-result', VideoPrecheckResultWebhook::class, 55],
    ['56-sample-application-status-change', SampleApplicationStatusChangeWebhook::class, 56],
    ['59-shoppable-video-precheck-tasks-result', ShoppableVideoPrecheckTasksResultWebhook::class, 59],
    ['63-activity-change', ActivityChangeWebhook::class, 63],
    ['66-appeal-completed', AppealCompletedWebhook::class, 66],
]);

it('topico 56 (amostra) NAO traz order_id — a ligacao com o pedido exige 2o passo', function () {
    $data = ttWhAffFixture('56-sample-application-status-change')['data'];
    $dto = SampleApplicationStatusChangeWebhook::fromArray($data);

    // 3.173 notas a valor cheio nasceram de amostra gratis tratada como venda.
    // Este webhook avisa CEDO (ciclo da aplicacao, antes da expedicao), mas as
    // chaves sao application_id + creator + sku — nao ha' order_id no payload.
    expect($dto->toArray())->not->toHaveKey('order_id')
        ->and($dto->applicationId)->toBe('123456')
        ->and($dto->newStatus)->toBe('PENDING')
        ->and($dto->product->skuId)->toBe('1729859567502267166')
        ->and($dto->creator->creatorOpenId)->toStartWith('uACafQ');
});

it('topico 56 manda creator/product como OBJETO, apesar de a doc declarar lista', function () {
    $dto = SampleApplicationStatusChangeWebhook::fromArray(
        ttWhAffFixture('56-sample-application-status-change')['data']
    );

    // Quem tipar array (como a tabela sugere) quebra na primeira entrega real.
    expect($dto->creator)->toBeInstanceOf(\SistemAtc\Marketplaces\Tiktok\DTO\Webhook\SampleApplicationCreator::class)
        ->and($dto->product)->toBeInstanceOf(\SistemAtc\Marketplaces\Tiktok\DTO\Webhook\SampleApplicationProduct::class);
});

it('topico 7 avisa 30 dias antes e repete diario — expiration_time vem STRING', function () {
    $dto = UpcomingAuthorizationExpirationWebhook::fromArray(
        ttWhAffFixture('07-upcoming-authorization-expiration')['data']
    );

    // Epoch, mas serializado como string no exemplo oficial. Quem tipar int
    // perde o roundtrip; quem comparar com now() precisa castar.
    expect($dto->expirationTime)->toBeString()->toBe('1627587506')
        ->and(date('Y', (int) $dto->expirationTime))->toBe('2021');
});

it('topico 6 so tem texto livre — quem identifica a loja e o shop_id do ENVELOPE', function () {
    $envelope = ttWhAffFixture('06-seller-deauthorization');
    $dto = \SistemAtc\Marketplaces\Tiktok\DTO\Webhook\SellerDeauthorizationWebhook::fromArray($envelope['data']);

    expect(array_keys($dto->toArray()))->toBe(['message'])
        ->and($dto->message)->toContain('deauthorized')
        // o {xxx} do texto NAO e' o shop_id: nao parseie a mensagem.
        ->and($dto->message)->toContain('{xxx}')
        ->and(WebhookEnvelope::fromArray($envelope)->shopId)->toBe('7494049642642441621');
});

it('topicos de creator (20, 55, 59) vem SEM shop_id — a chave e creator_open_id', function () {
    foreach (['20-creator-deauthorization', '55-video-precheck-result', '59-shoppable-video-precheck-tasks-result'] as $slug) {
        $envelope = WebhookEnvelope::fromArray(ttWhAffFixture($slug));

        expect($envelope->shopId)->toBeNull()
            ->and($envelope->creatorOpenId)->not->toBeNull();
    }
});

it('topico 20: cancel_time e INT no exemplo, apesar de a doc dizer string', function () {
    $dto = CreatorDeauthorizationWebhook::fromArray(ttWhAffFixture('20-creator-deauthorization')['data']);

    expect($dto->cancelTime)->toBeInt()->toBe(1644412885);
});

it('os dois topicos de mensagem (14 e 33) tem shapes DIFERENTES', function () {
    $m14 = NewMessageWebhook::fromArray(ttWhAffFixture('14-new-message')['data']);
    $m33 = NewMessageListenerWebhook::fromArray(ttWhAffFixture('33-new-message-listener')['data']);

    // 14: chave do tipo e' `type`, create_time int, sender com role.
    expect($m14->type)->toBe('TEXT')
        ->and($m14->createTime)->toBeInt()
        ->and($m14->sender->role)->toBe('BUYER')
        ->and($m14->sender->imUserId)->toBe('707885565181165594')
        ->and($m14->isVisible)->toBeTrue();

    // 33: chave do tipo e' `msg_type`, create_time STRING, sender sem role.
    expect($m33->msgType)->toBe('TEXT')
        ->and($m33->createTime)->toBeString()->toBe('1691411573')
        ->and($m33->sender->senderImUserId)->toBe('2368694990397660924')
        ->and($m33->toArray()['sender'])->not->toHaveKey('role');
});

it('content do topico 14 e STRING com JSON serializado dentro, nao objeto', function () {
    $dto = NewMessageWebhook::fromArray(ttWhAffFixture('14-new-message')['data']);

    expect($dto->content)->toBeString()->toBe('{"content":"444"}')
        ->and(json_decode($dto->content, true))->toBe(['content' => '444'])
        // index (string snowflake) e' o que ordena, nao create_time.
        ->and($dto->index)->toBeString()->toBe('7494560109732334274');
});

it('topico 63 manda change_type FORA do enum da doc — por isso string crua', function () {
    $dto = ActivityChangeWebhook::fromArray(ttWhAffFixture('63-activity-change')['data']);

    // Doc: CREATE | UPDATE | DEACTIVATE. Exemplo oficial: "product_update".
    expect($dto->changeType)->toBe('product_update')
        ->and(['CREATE', 'UPDATE', 'DEACTIVATE'])->not->toContain($dto->changeType);
});

it('topico 63 preserva listas VAZIAS do remove_list (vazio != ausente)', function () {
    $dto = ActivityChangeWebhook::fromArray(ttWhAffFixture('63-activity-change')['data']);

    expect($dto->productRemoveList->skuIds)->toBe(['66666666'])
        ->and($dto->productRemoveList->benefitProductIds)->toBe([])
        ->and($dto->productRemoveList->excludeProductIds)->toBe([])
        // sku_ids so' existe no remove_list; o update_list nao tem esse campo.
        ->and($dto->productUpdateList->toArray())->not->toHaveKey('sku_ids');
});

it('topico 39 traz so o status NOVO — nao da pra saber de onde veio', function () {
    $dto = ActivityStatusChangeWebhook::fromArray(ttWhAffFixture('39-activity-status-change')['data']);

    expect($dto->status)->toBe('ONGOING')
        ->and($dto->toArray())->not->toHaveKey('previous_status')
        ->and($dto->activityType)->toBe('FIXED_PRICE')
        ->and($dto->productLevel)->toBe('PRODUCT');
});

it('topicos 55 e 59 usam chaves diferentes pro mesmo conceito', function () {
    $p55 = VideoPrecheckResultWebhook::fromArray(ttWhAffFixture('55-video-precheck-result')['data']);
    $p59 = ShoppableVideoPrecheckTasksResultWebhook::fromArray(ttWhAffFixture('59-shoppable-video-precheck-tasks-result')['data']);

    // 55: precheck_task_id + issues[].suggestion (SINGULAR).
    expect($p55->precheckTaskId)->toBe('7493990579714164574')
        ->and($p55->result)->toBe('FAIL')
        ->and($p55->issues[0]->suggestion)->toContain('unoriginal content');

    // 59: task_id (nem sempre numerico) + dois blocos, issues[].suggestions (PLURAL).
    expect($p59->taskId)->toBe('task_202402011200001234')
        ->and($p59->violationCheckResult->status)->toBe('FAIL')
        ->and($p59->violationCheckResult->issues[0]->risk)->toBe('HIGH')
        ->and($p59->violationCheckResult->issues[0]->suggestions)->toContain('prohibited')
        // qualidade e violacao sao vereditos INDEPENDENTES.
        ->and($p59->goodQualityCheckResult->status)->toBe('SUCCESS')
        ->and($p59->goodQualityCheckResult->issues[0]->code)->toBe('LOW_RESOLUTION');
});

it('topico 25 identifica o produto pelo ID EXTERNO, nao pelo product_id TikTok', function () {
    $dto = OpportunityMatchingStatusChangeWebhook::fromArray(
        ttWhAffFixture('25-opportunity-matching-status-change')['data']
    );

    expect($dto->externalProductId)->toBe('576486316948490000')
        ->and($dto->toArray())->not->toHaveKey('product_id')
        ->and($dto->opportunityIds)->toBe(['213231', '213233'])
        ->and($dto->opportunityMatchingStatus)->toBe('PENDING');
});

it('topico 13 nao diz o que mudou — create_time e da CONVERSA, nao do evento', function () {
    $envelope = ttWhAffFixture('13-new-conversation');
    $dto = NewConversationWebhook::fromArray($envelope['data']);

    // 1627587506 (jul/2021) != 1644412885 (fev/2022) do envelope.
    expect($dto->createTime)->toBe(1627587506)
        ->and($dto->createTime)->not->toBe($envelope['timestamp'])
        ->and(array_keys($dto->toArray()))->toBe(['conversation_id', 'create_time']);
});

it('topico 66 aceita veredito por indicador, com reject_reason so no rejeitado', function () {
    $dto = AppealCompletedWebhook::fromArray(ttWhAffFixture('66-appeal-completed')['data']);

    expect($dto->appealTask->appealAuditResult)->toHaveCount(2)
        ->and($dto->appealTask->appealAuditResult[0]->toArray())->not->toHaveKey('reject_reason')
        ->and($dto->appealTask->appealAuditResult[1]->rejectReason)->toContain('not clear')
        // int64 na doc, string aqui: snowflake nao cabe com seguranca em int JS.
        ->and($dto->productId)->toBeString()
        ->and($dto->appealTask->sizeChartId)->toBeString();
});

it('todo topico do lote tem um case no enum de type codes', function () {
    foreach ([6, 7, 13, 14, 20, 25, 33, 39, 55, 56, 59, 63, 66] as $type) {
        expect(WebhookEventType::isKnown($type))->toBeTrue("type {$type} sem case no enum");
    }
});
