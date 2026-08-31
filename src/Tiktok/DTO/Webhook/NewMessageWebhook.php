<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 14 — New message (conversa de atendimento ao
 * COMPRADOR). Nao confundir com o topico 33, que e' a caixa do CRIADOR.
 *
 * ⚠️ `content` e' STRING com JSON SERIALIZADO dentro — nao e' objeto. O shape
 * de dentro depende de `type`:
 *   TEXT / ALLOCATED_SERVICE / NOTIFICATION / BUYER_ENTER_FROM_TRANSFER / OTHER
 *       {"content": "..."}
 *   IMAGE                                    {"url","width","height"}
 *   PRODUCT_CARD / BUYER_ENTER_FROM_PRODUCT  {"product_id": "..."}
 *   ORDER_CARD  / BUYER_ENTER_FROM_ORDER     {"order_id": "..."}
 *   VIDEO       {"url","cover","width","height","duration","vid","expire_time",
 *                "format","size","bit_rate","quality","codec_type"}
 *   LOGISTICS_CARD {"order_id", "package_id"(opcional)}
 *   COUPON_CARD    {"coupon_id": "..."}      (detalhe via Get Coupon)
 * Mantido string de proposito: tipar em objeto exigiria um DTO por variante e
 * QUALQUER campo novo do TikTok seria descartado em silencio.
 *
 * `index` e' o que ordena mensagem, nao o `create_time`: maior index = mais
 * nova. E' string (snowflake) — nao converta pra int.
 *
 * `is_visible = false` significa mensagem que o TikTok nao quer exibir pro
 * atendente; ela chega mesmo assim.
 */
final class NewMessageWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $messageId = null,
        /** Ordenacao da conversa. String snowflake — maior = mais nova. */
        public readonly ?string $index = null,
        public readonly ?string $conversationId = null,
        /**
         * TEXT | IMAGE | ALLOCATED_SERVICE | NOTIFICATION |
         * BUYER_ENTER_FROM_TRANSFER | BUYER_ENTER_FROM_PRODUCT |
         * BUYER_ENTER_FROM_ORDER | PRODUCT_CARD | ORDER_CARD | EMOTICONS |
         * VIDEO | COUPON_CARD | LOGISTICS_CARD | OTHER
         */
        public readonly ?string $type = null,
        /** JSON serializado em string. Veja o docblock da classe. */
        public readonly ?string $content = null,
        /** Epoch em SEGUNDOS. */
        public readonly ?int $createTime = null,
        public readonly ?bool $isVisible = null,
        public readonly ?NewMessageSender $sender = null,
    ) {}
}
