<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 13 — New conversation.
 *
 * Apesar do nome, NAO significa "conversa criada": dispara quando um atendente
 * ENTRA OU SAI de uma conversa. Por isso a doc recomenda so' recarregar a lista
 * de conversas ao receber — o evento nao diz o que mudou nem quem entrou/saiu.
 *
 * `create_time` e' a criacao da CONVERSA (pode ser bem antiga), nao a hora do
 * evento; a hora do evento e' o `timestamp` do envelope.
 */
final class NewConversationWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $conversationId = null,
        /** Epoch em SEGUNDOS da criacao da conversa. */
        public readonly ?int $createTime = null,
    ) {}
}
