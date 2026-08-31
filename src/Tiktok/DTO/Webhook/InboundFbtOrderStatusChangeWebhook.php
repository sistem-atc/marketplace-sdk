<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 21 — Inbound FBT order status change.
 *
 * Ciclo da REMESSA pro armazem do TikTok (equivalente do envio Full do ML).
 * RECEIVED/PARTIALLY_RECEIVED e' o que confirma o recebimento fisico e fecha a
 * conferencia contra a NF-e de remessa emitida na saida; PARTIALLY_RECEIVED
 * significa divergencia de quantidade — nota emitida a maior.
 *
 * Este topico chega com `seller_open_id` no envelope, sem `shop_id`.
 */
final class InboundFbtOrderStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id da ordem de entrada FBT (prefixo IBR no exemplo oficial). */
        public readonly ?string $inboundOrderId = null,
        /**
         * TO_BE_RECEIVED | ARRIVE_HUB | RECEIVING | PARTIALLY_RECEIVED |
         * RECEIVED | CANCELLED.
         */
        public readonly ?string $orderStatus = null,
        /** Epoch em SEGUNDOS da mudanca. */
        public readonly ?int $updateTime = null,
    ) {}
}
