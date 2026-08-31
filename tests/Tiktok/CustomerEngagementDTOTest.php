<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement\CreatedEngagementTaskResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement\EngagementPermissionsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement\EngagementTaskPerformancesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement\MessageTemplatesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement\SendEngagementMessageResponseDTO;

function ceFixture(string $slug): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'),
        true,
    );
}

dataset('ce_fixtures', [
    'permissions' => ['ce-permissions', EngagementPermissionsResponseDTO::class],
    'message templates' => ['ce-message-templates', MessageTemplatesResponseDTO::class],
    'create engagement task' => ['ce-create-engagement-task', CreatedEngagementTaskResponseDTO::class],
    'create custom engagement task' => ['ce-create-custom-engagement-task', CreatedEngagementTaskResponseDTO::class],
    'send engagement message' => ['ce-send-engagement-message', SendEngagementMessageResponseDTO::class],
    'task performances' => ['ce-task-performances', EngagementTaskPerformancesResponseDTO::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $payload = ceFixture($slug);
    $perdidos = RecursiveFieldCoverage::missing($payload, $dto::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
})->with('ce_fixtures');

it('faz roundtrip lossless do payload inteiro', function (string $slug, string $dto) {
    $payload = ceFixture($slug);

    expect($dto::fromArray($payload)->toArray())->toEqual($payload);
})->with('ce_fixtures');

// ─────────────────────────────────────────────────────────────────────────
// Comportamento real
// ─────────────────────────────────────────────────────────────────────────

it('envio com falha volta como SUCESSO no envelope: o erro esta em errors[]', function () {
    $dto = SendEngagementMessageResponseDTO::fromArray(ceFixture('ce-send-engagement-message'));

    // O envelope trouxe `code: 0` e mesmo assim UM destinatario foi recusado.
    // Quem so' olhar o code da a mensagem por entregue.
    expect($dto->errors)->toHaveCount(1)
        ->and($dto->errors[0]->code)->toBe(68009003)
        ->and($dto->errors[0]->detail->buyerEmail)->toContain('@scs.tiktokw.us');
});

it('o destinatario e o e-mail ANONIMIZADO, nao o e-mail real do cliente', function () {
    $email = SendEngagementMessageResponseDTO::fromArray(ceFixture('ce-send-engagement-message'))
        ->errors[0]->detail->buyerEmail;

    // Dominio de proxy do TikTok. Tentar casar isso com o cadastro do cliente
    // no ERP nunca vai bater — o vinculo tem que vir do pedido.
    expect($email)->toEndWith('@scs.tiktokw.us');
});

it('o template dita quantos cards cabem e QUAIS cupons valem', function () {
    $tpl = MessageTemplatesResponseDTO::fromArray(ceFixture('ce-message-templates'))->messageTemplates[0];

    // maxCount 4 produtos e EXATAMENTE 1 cupom (min 1 = cupom obrigatorio
    // neste template). E o cupom precisa ser de um dos tipos declarados —
    // mandar outro faz a task falhar no ENVIO, nao na criacao.
    expect($tpl->productCardRules->maxCount)->toBe(4)
        ->and($tpl->couponCardRules->minCount)->toBe(1)
        ->and($tpl->couponCardRules->maxCount)->toBe(1)
        ->and($tpl->couponCardRules->couponType)->toBe(['REGULAR_ALL,REGULAR_REPEAT']);
});

it('GMV da task e STRING, contadores sao int', function () {
    $perf = EngagementTaskPerformancesResponseDTO::fromArray(ceFixture('ce-task-performances'))
        ->taskPerformances[0];

    // Mistura proposital do TikTok no MESMO objeto: dinheiro string, o resto int.
    expect($perf->gmvAmount)->toBe('5.5')
        ->and($perf->sentRecipientCount)->toBe(100)
        ->and($perf->status)->toBe('SENDING');
});

it('permissao e por FEATURE, e sem ela a familia inteira recusa', function () {
    $features = EngagementPermissionsResponseDTO::fromArray(ceFixture('ce-permissions'))->features;

    expect($features[0]->name)->toBe('CUSTOM_MSG')
        ->and($features[0]->isAuthorized)->toBeTrue();
});
