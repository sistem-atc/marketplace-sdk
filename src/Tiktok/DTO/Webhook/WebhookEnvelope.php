<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Envelope comum de TODO webhook do TikTok Shop. O corpo que chega no nosso
 * endpoint e' sempre {type, tts_notification_id, shop_id|seller_open_id,
 * timestamp, data} — so' o miolo de `data` muda por topico.
 *
 * `data` fica CRU de proposito: o roteamento e' por `type`, e so' depois de
 * saber o topico da pra escolher o DTO certo (`OrderStatusChangeWebhook`,
 * `RefundSuccessWebhook`, ...). Hidratar cedo demais exigiria um match aqui
 * dentro e faria o envelope depender de todos os DTOs de topico.
 *
 * Quirks de identificacao da loja, medidos nos exemplos oficiais:
 *  - topicos de pedido/pos-venda/fiscal mandam `shop_id`;
 *  - topicos FBT (21, 22, 23, 24) mandam `seller_open_id` e NAO mandam shop_id;
 *  - o topico 58 (FBT MCF) documenta `seller_open_id` mas o exemplo oficial
 *    manda `creator_open_id`. Os tres campos existem aqui pra nao perder nada.
 *
 * `tts_notification_id` e' a chave de deduplicacao (o TikTok reenvia enquanto
 * nao receber 2xx); nem todo exemplo oficial traz (o do topico 3 nao traz).
 */
final class WebhookEnvelope implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<string, mixed>|null $data */
    public function __construct(
        /** Type code do topico (1 = order status change, 36 = invoice, ...). */
        public readonly ?int $type = null,
        /** ID da notificacao — use pra dedup, o TikTok reentrega. */
        public readonly ?string $ttsNotificationId = null,
        /** Loja TikTok. Ausente nos topicos FBT. */
        public readonly ?string $shopId = null,
        /** open_id do seller — e' o que os topicos FBT mandam no lugar de shop_id. */
        public readonly ?string $sellerOpenId = null,
        /** Divergencia do topico 58: exemplo oficial manda creator_open_id. */
        public readonly ?string $creatorOpenId = null,
        /** Epoch em SEGUNDOS de quando o webhook foi disparado. */
        public readonly ?int $timestamp = null,
        /** Miolo do topico, cru. Hidrate com o DTO do `type`. */
        public readonly ?array $data = null,
    ) {}
}
