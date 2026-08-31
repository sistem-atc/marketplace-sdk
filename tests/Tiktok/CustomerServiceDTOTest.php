<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\AgentSettingsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\ConversationDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\ConversationListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\ConversationMessagesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\CreatedConversationResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\CustomerServicePerformanceResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\SentMessageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\SessionSearchResponseDTO;

function csFixture(string $slug): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'),
        true,
    );
}

dataset('cs_fixtures', [
    'get conversations' => ['cs-get-conversations', ConversationListResponseDTO::class],
    'get conversation' => ['cs-get-conversation', ConversationDetailResponseDTO::class],
    'get conversation messages' => ['cs-get-conversation-messages', ConversationMessagesResponseDTO::class],
    'create conversation' => ['cs-create-conversation', CreatedConversationResponseDTO::class],
    'send message' => ['cs-send-message', SentMessageResponseDTO::class],
    'search sessions' => ['cs-search-sessions', SessionSearchResponseDTO::class],
    'get agent settings' => ['cs-get-agent-settings', AgentSettingsResponseDTO::class],
    'performance' => ['cs-performance', CustomerServicePerformanceResponseDTO::class],
]);

/**
 * A trava principal: comparacao RECURSIVA contra o exemplo OFICIAL da doc.
 * Campo que vive dentro de `conversations[].latest_message.sender` conta igual
 * a um campo de topo — foi um campo aninhado que sumiu em silencio da ultima vez.
 */
it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $payload = csFixture($slug);
    $perdidos = RecursiveFieldCoverage::missing($payload, $dto::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
})->with('cs_fixtures');

it('faz roundtrip lossless do payload inteiro', function (string $slug, string $dto) {
    $payload = csFixture($slug);

    expect($dto::fromArray($payload)->toArray())->toEqual($payload);
})->with('cs_fixtures');

// ─────────────────────────────────────────────────────────────────────────
// Comportamento real do chat — o que surpreende quem consome
// ─────────────────────────────────────────────────────────────────────────

it('a identidade do comprador tem DOIS ids e so um deles casa com pedido', function () {
    $participante = ConversationListResponseDTO::fromArray(csFixture('cs-get-conversations'))
        ->conversations[0]
        ->participants[0];

    // Sao valores DIFERENTES no mesmo objeto. Quem cruzar chat x pedido pelo
    // im_user_id (o unico que o webhook type 33 entrega) nao acha pedido nenhum.
    expect($participante->imUserId)->toBe('7494560109732334261')
        ->and($participante->userId)->toBe('7494560109732334262')
        ->and($participante->role)->toBe('BUYER');
});

it('o texto da mensagem NAO esta em content: content e JSON serializado', function () {
    $msg = ConversationMessagesResponseDTO::fromArray(csFixture('cs-get-conversation-messages'))
        ->messages[0];

    // Ler `content` direto na tela mostra `{"content": "..."}` pro atendente.
    expect($msg->content)->toBe('{"content": "simple text message"}')
        ->and(json_decode($msg->content, true)['content'])->toBe('simple text message');
});

it('o vinculo da conversa com um pedido nasce do TYPE da mensagem, nao da conversa', function () {
    $conversa = ConversationListResponseDTO::fromArray(csFixture('cs-get-conversations'))->conversations[0];

    // Nao existe order_id na conversa — em nenhum nivel. O TikTok so' amarra
    // pedido por mensagem ORDER_CARD, cujo `content` traz {"order_id": ...}.
    expect($conversa->toArray())->not->toHaveKey('order_id')
        ->and($conversa->latestMessage->type)->toBe('TEXT');
});

it('detalhe e lista de conversa NAO sao simetricos', function () {
    $daLista = ConversationListResponseDTO::fromArray(csFixture('cs-get-conversations'))->conversations[0];
    $doDetalhe = ConversationDetailResponseDTO::fromArray(csFixture('cs-get-conversation'))->conversation;

    // `can_send_message` (a janela de resposta) e `latest_message` so' existem
    // na LISTA. Quem montar a tela a partir do detalhe perde os dois.
    expect($daLista->canSendMessage)->toBeTrue()
        ->and($doDetalhe->canSendMessage)->toBeNull()
        ->and($doDetalhe->latestMessage)->toBeNull();

    // E o participante muda de nome de campo entre as duas versoes do endpoint.
    expect($daLista->participants[0]->buyerPlatform)->toBe('TIKTOK_SHOP')
        ->and($doDetalhe->participants[0]->platform)->toBe('TIKTOK_SHOP');
});

it('o index da mensagem ordena mas nao conta', function () {
    $msg = ConversationMessagesResponseDTO::fromArray(csFixture('cs-get-conversation-messages'))->messages[0];

    // Fica STRING de proposito: e' snowflake, cabe em 19 digitos e virar int
    // perde precisao em 32 bits. Serve pra ordenar, nunca pra "mensagem numero N".
    expect($msg->index)->toBeString()->toBe('7494560109732334274');
});

it('as metricas de atendimento vem como STRING e em base 100', function () {
    $p = CustomerServicePerformanceResponseDTO::fromArray(csFixture('cs-performance'))->performance;

    // "93.4" e' 93,4% — nao 9340% nem 0,934. E e' string: tipar float aqui
    // perderia a forma e a comparacao com o painel do TikTok deixaria de bater.
    expect($p->responsePercentage)->toBe('93.4')
        ->and($p->responseTimeMins)->toBe('3.4')
        ->and($p->csGuidedGmv)->toBe('36500')
        ->and((float) $p->responsePercentage)->toBeLessThan(100.0);
});

it('sessao traz o SLA de primeira resposta em segundos', function () {
    $sessao = SessionSearchResponseDTO::fromArray(csFixture('cs-search-sessions'))->sessions[0];

    // 123 SEGUNDOS. O performance da loja reporta o mesmo indicador em MINUTOS
    // — misturar as duas fontes num grafico so' da' numero errado por 60x.
    expect($sessao->firstResponseTime)->toBe(123)
        ->and($sessao->firstResponseLate)->toBeFalse()
        ->and($sessao->chatTags)->toBe(['Logistics']);
});
