<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 11 — Cancellation status change.
 *
 * Cancelamento ANTES do envio (o pos-envio vira return, type 12). O estado que
 * fecha o ciclo financeiro e' CANCELLATION_REQUEST_COMPLETE — cancelamento
 * efetivado COM reembolso emitido; PENDING ainda e' pedido de cancelamento
 * aguardando o seller.
 */
final class CancellationStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderId = null,
        /** Quem pediu: BUYER | SELLER | SYSTEM. (chave no plural na API) */
        public readonly ?string $cancellationsRole = null,
        /**
         * CANCELLATION_REQUEST_PENDING | CANCELLATION_REQUEST_SUCCESS |
         * CANCELLATION_REQUEST_CANCELLED | CANCELLATION_REQUEST_COMPLETE.
         */
        public readonly ?string $cancelStatus = null,
        public readonly ?string $cancelId = null,
        /** Epoch em SEGUNDOS de quando o comprador abriu o cancelamento. */
        public readonly ?int $createTime = null,
    ) {}
}
