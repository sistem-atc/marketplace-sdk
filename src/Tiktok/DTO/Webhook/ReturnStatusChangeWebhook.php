<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 12 — Return status change.
 *
 * Fluxo de devolucao/reembolso pos-envio. Nao traz valor: quanto foi devolvido
 * so' chega no type 67 (Refund Success) ou no Get Return Records. Serve pra
 * marcar o pedido em disputa e, no RETURN_AND_REFUND concluido, disparar a
 * NF-e de entrada (devolucao) — REFUND puro NAO tem retorno fisico.
 */
final class ReturnStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderId = null,
        /** BUYER | SELLER | SYSTEM. */
        public readonly ?string $returnRole = null,
        /** REFUND | REPLACEMENT | RETURN_AND_REFUND. */
        public readonly ?string $returnType = null,
        /**
         * AWAITING_BUYER_SHIP | BUYER_SHIPPED_ITEM | REFUND_OR_RETURN_REQUEST_REJECT |
         * REJECT_RECEIVE_PACKAGE | REPLACEMENT_REQUEST_CANCEL |
         * REPLACEMENT_REQUEST_COMPLETE | REPLACEMENT_REQUEST_PENDING |
         * REPLACEMENT_REQUEST_REFUND_SUCCESS | REPLACEMENT_REQUEST_REJECT |
         * RETURN_OR_REFUND_REQUEST_CANCEL | RETURN_OR_REFUND_REQUEST_COMPLETE |
         * RETURN_OR_REFUND_REQUEST_PENDING | RETURN_OR_REFUND_REQUEST_SUCCESS.
         */
        public readonly ?string $returnStatus = null,
        public readonly ?string $returnId = null,
        /** Epoch em SEGUNDOS da abertura do pedido de devolucao. */
        public readonly ?int $createTime = null,
        /** Epoch em SEGUNDOS da mudanca de status. */
        public readonly ?int $updateTime = null,
    ) {}
}
