<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 33 — New message listener: um CRIADOR mandou
 * mensagem pro seller (caixa Affiliate). O topico 14 e' a caixa do COMPRADOR.
 *
 * Diferencas que quebram quem reusa o DTO do 14:
 *  - o tipo da mensagem se chama `msg_type` aqui (no 14 e' `type`);
 *  - `create_time` vem como STRING ("1691411573"), no 14 vem int;
 *  - `sender` tem shape proprio (sender_im_user_id, sem role).
 *
 * ⚠️ `content` e' string; no TEXT o exemplo manda texto CRU ("Hello"), embora a
 * doc descreva JSON serializado ({"content": "simple text"}). Seguimos o
 * exemplo: string, sem json_decode cego. Shapes dos cards:
 *   PRODUCT_CARD              {"product_id": "12345"}
 *   TARGET_COLLABORATION_CARD {"invitation_group_id": "1234"}  (doc chama a
 *                             chave de TARGET_INVITATION_CARD na lista de
 *                             exemplos — divergencia da propria doc)
 *   FREE_SAMPLE_CARD          {"apply_id": "1234"}
 */
final class NewMessageListenerWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Ordenacao: maior index = mais nova. String, nao converta pra int. */
        public readonly ?string $index = null,
        public readonly ?string $messageId = null,
        public readonly ?string $conversationId = null,
        /** TEXT | PRODUCT_CARD | TARGET_COLLABORATION_CARD | FREE_SAMPLE_CARD */
        public readonly ?string $msgType = null,
        /** Texto cru ou JSON serializado, conforme msg_type. */
        public readonly ?string $content = null,
        /** Epoch em SEGUNDOS — chega como STRING neste topico. */
        public readonly ?string $createTime = null,
        public readonly ?NewMessageListenerSender $sender = null,
    ) {}
}
